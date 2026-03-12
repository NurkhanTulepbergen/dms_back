<?php

namespace Modules\Finance\Services;

use Modules\Finance\Models\Charge;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use RuntimeException;

class StripeService
{
    public function createCheckoutSession(Charge $charge)
    {
        $secret = config('services.stripe.secret');

        if (empty($secret)) {
            throw new RuntimeException('Stripe secret key is not configured');
        }

        Stripe::setApiKey($secret);

        return Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'kzt',
                    'product_data' => [
                        'name' => 'Dormitory Semester Fee',
                    ],
                    'unit_amount' => (int) round(((float) $charge->amount) * 100),
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
