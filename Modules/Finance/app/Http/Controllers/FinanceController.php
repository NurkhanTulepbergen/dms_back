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
            $chargeId = $session->metadata->charge_id;

            $payment = Payment::where('stripe_session_id', $session->id)->first();

            DB::transaction(function () use ($payment, $session) {
                if (! $payment) {
                    return;
                }

                $payment->update([
                    'status' => 'succeeded',
                    'paid_at' => now(),
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'raw_payload' => $session,
                ]);

                $payment->charge->update([
                    'status' => 'paid',
                ]);

                if ($payment->charge->type === 'gym_membership') {
                    app(GymService::class)->activateMembership($payment->charge);
                }
            });
        }

        return response()->json(['status' => 'ok']);
    }

}
