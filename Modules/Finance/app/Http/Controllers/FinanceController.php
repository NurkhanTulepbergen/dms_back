<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Models\Charge;
use Modules\Finance\Models\Payment;
use Modules\Finance\Services\StripeService;

class FinanceController extends Controller
{
    public function checkout($chargeId)
    {
        $charge = Charge::where('id', $chargeId)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

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

                $payment->update([
                    'status' => 'succeeded',
                    'paid_at' => now(),
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'raw_payload' => $session,
                ]);

                $payment->charge->update([
                    'status' => 'paid',
                ]);
            });
        }

        return response()->json(['status' => 'ok']);
    }

}
