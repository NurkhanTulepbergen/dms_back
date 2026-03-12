<?php

namespace Modules\Finance\Services;

use App\Exceptions\BusinessException;
use Modules\Finance\Models\Charge;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use RuntimeException;

class StripeService
{
    private const MAX_KZT_AMOUNT = 999999.99;

    public function createCheckoutSession(Charge $charge)
    {
        $secret = config('services.stripe.secret');

        if (empty($secret)) {
            throw new RuntimeException('Stripe secret key is not configured');
        }

        Stripe::setApiKey($secret);

        $amount = (float) $charge->amount;
        $currency = strtolower((string) ($charge->currency ?? 'kzt'));

        if ($amount <= 0) {
            throw new BusinessException('Сумма начисления должна быть больше 0', 422);
        }

        if ($currency === 'kzt' && $amount > self::MAX_KZT_AMOUNT) {
            throw new BusinessException(
                'Сумма слишком большая для оплаты в KZT через Stripe Checkout (максимум 999 999.99 KZT). Разбейте начисление на несколько платежей.',
                422
            );
        }

        $frontendUrl = rtrim((string) config('app.frontend_url', config('app.url')), '/');

        return Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => 'Dormitory Semester Fee',
                    ],
                    'unit_amount' => (int) round($amount * 100),
                ],
                'quantity' => 1,
            ]],
            'success_url' => $frontendUrl . '/payment-success',
            'cancel_url' => $frontendUrl . '/payment-cancel',
            'metadata' => [
                'charge_id' => $charge->id,
            ],
        ]);
    }
}
