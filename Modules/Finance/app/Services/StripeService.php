<?php

namespace Modules\Finance\Services;

use Modules\Finance\Models\Charge;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeService
{
    public function createCheckoutSession(Charge $charge)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        return Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'kzt',
                    'product_data' => [
                        'name' => 'Dormitory Semester Fee',
                    ],
                    'unit_amount' => $charge->amount * 100,
                ],
                'quantity' => 1,
            ]],
            'success_url' => config('app.url') . '/payment-success',
            'cancel_url' => config('app.url') . '/payment-cancel',
            'metadata' => [
                'charge_id' => $charge->id,
            ],
        ]);
    }
}
