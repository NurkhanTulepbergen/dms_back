<?php

namespace Modules\Finance\Http\Controllers;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Finance\Models\Charge;
use Modules\Finance\Models\Payment;
use Modules\Finance\Services\StripeService;
use Modules\Gym\Services\GymService;
use Stripe\Exception\InvalidRequestException;
use Throwable;

class  FinanceController extends Controller
{
    public function charges(Request $request)
    {
        app(GymService::class)->cleanupPendingChargesForUser($request->user());
        $this->syncPendingPaymentsForUser((int) $request->user()->id);

        $charges = Charge::query()
            ->where('user_id', $request->user()->id)
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('id')
            ->get();

        return result($charges, 200, 'Начисления получены');
    }

    public function checkout($chargeId)
    {
        try {
            $charge = Charge::where('id', $chargeId)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->firstOrFail();

            $amount = (float) $charge->amount;
            $currency = strtolower((string) ($charge->currency ?? 'kzt'));

            if ($amount <= 0) {
                return result(null, 422, 'Сумма начисления должна быть больше 0');
            }

            if ($currency === 'kzt' && $amount > 999999.99) {
                return result(
                    null,
                    422,
                    'Сумма слишком большая для оплаты в KZT через Stripe Checkout (максимум 999 999.99 KZT). Разбейте начисление на несколько платежей.'
                );
            }

            $stripe = app(StripeService::class);
            $session = $stripe->createCheckoutSession($charge);

            Payment::create([
                'charge_id' => $charge->id,
                'stripe_session_id' => $session->id,
                'amount' => $charge->amount,
            ]);

            return result([
                'url' => $session->url
            ]);
        } catch (ModelNotFoundException) {
            return result(null, 404, 'Начисление не найдено или уже оплачено');
        } catch (BusinessException $e) {
            return result(null, $e->status_code, $e->getMessage());
        } catch (InvalidRequestException $e) {
            Log::warning('Finance checkout validation failed', [
                'charge_id' => $chargeId,
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            return result(null, 422, $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Finance checkout failed', [
                'charge_id' => $chargeId,
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
            ]);

            return result(null, 500, 'Не удалось создать Stripe checkout. Проверьте настройки Stripe.');
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');

        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sig,
            config('services.stripe.webhook_secret')
        );

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $payment = Payment::where('stripe_session_id', $session->id)->first();
            $this->markPaymentAsSucceeded($payment, $session);
        }

        return response()->json(['status' => 'ok']);
    }

    public function confirmCheckout(Request $request)
    {
        $sessionId = (string) $request->input('session_id', '');

        if ($sessionId === '') {
            return result(null, 422, 'session_id is required');
        }

        $payment = Payment::query()
            ->with('charge')
            ->where('stripe_session_id', $sessionId)
            ->whereHas('charge', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->first();

        if (! $payment || ! $payment->charge) {
            return result(null, 404, 'Платеж не найден');
        }

        if ($payment->status === 'succeeded' && $payment->charge->status === 'paid') {
            return result([
                'confirmed' => true,
                'charge_id' => $payment->charge->id,
                'charge_status' => $payment->charge->status,
                'payment_status' => $payment->status,
            ], 200, 'Платеж уже подтвержден');
        }

        try {
            $session = app(StripeService::class)->retrieveCheckoutSession($sessionId);
        } catch (Throwable $e) {
            Log::error('Finance checkout confirmation failed', [
                'session_id' => $sessionId,
                'user_id' => $request->user()->id,
                'message' => $e->getMessage(),
            ]);

            return result(null, 500, 'Не удалось проверить статус оплаты');
        }

        $paymentStatus = (string) ($session->payment_status ?? '');
        $sessionStatus = (string) ($session->status ?? '');
        $isPaid = $paymentStatus === 'paid' || $sessionStatus === 'complete';

        if ($isPaid) {
            $this->markPaymentAsSucceeded($payment, $session);
            $payment->refresh()->load('charge');
        }

        return result([
            'confirmed' => $isPaid,
            'charge_id' => $payment->charge->id,
            'charge_status' => $payment->charge->refresh()->status,
            'payment_status' => $payment->refresh()->status,
            'stripe_payment_status' => $paymentStatus,
            'stripe_session_status' => $sessionStatus,
        ], 200, $isPaid ? 'Платеж подтвержден' : 'Платеж еще обрабатывается');
    }

    private function markPaymentAsSucceeded(?Payment $payment, object $session): void
    {
        DB::transaction(function () use ($payment, $session) {
            if (! $payment || ! $payment->charge) {
                return;
            }

            $payment->update([
                'status' => 'succeeded',
                'paid_at' => now(),
                'stripe_payment_intent_id' => $session->payment_intent ?? $payment->stripe_payment_intent_id,
                'raw_payload' => method_exists($session, 'toArray') ? $session->toArray() : (array) $session,
            ]);

            $payment->charge->update([
                'status' => 'paid',
            ]);

            if ($payment->charge->type === 'gym_membership') {
                app(GymService::class)->activateMembership($payment->charge);
            }
        });
    }

    private function syncPendingPaymentsForUser(int $userId): void
    {
        $payments = Payment::query()
            ->with('charge')
            ->where('status', '!=', 'succeeded')
            ->whereHas('charge', function ($query) use ($userId) {
                $query
                    ->where('user_id', $userId)
                    ->where('status', 'pending');
            })
            ->latest('id')
            ->get()
            ->unique('charge_id')
            ->values();

        if ($payments->isEmpty()) {
            return;
        }

        $stripe = app(StripeService::class);

        foreach ($payments as $payment) {
            try {
                $session = $stripe->retrieveCheckoutSession($payment->stripe_session_id);
                $paymentStatus = (string) ($session->payment_status ?? '');
                $sessionStatus = (string) ($session->status ?? '');

                if ($paymentStatus === 'paid' || $sessionStatus === 'complete') {
                    $this->markPaymentAsSucceeded($payment, $session);
                }
            } catch (Throwable $e) {
                Log::warning('Finance pending payment sync failed', [
                    'payment_id' => $payment->id,
                    'charge_id' => $payment->charge_id,
                    'user_id' => $userId,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

}
