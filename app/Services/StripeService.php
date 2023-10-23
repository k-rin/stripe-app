<?php

namespace App\Services;

use App\Consts\CurrencyDecimal;
use Stripe\StripeClient;

class StripeService
{
    /**
     * Create a new service instance.
     */
    public function __construct(
        protected StripeClient $stripe,
    ) {}

    /**
     * Checkout by given currency and amount.
     * 
     * @param  string    $currency
     * @param  int|float $amount
     * @return object
     * @throws Exception 
     */
    public function checkout(string $currency, int|float $amount): object
    {
        $unitAmount = $this->getUnitAmount($currency, $amount);
        $session    = $this->stripe->checkout->sessions->create([
            'mode'        => 'payment',
            'success_url' => 'https://example.com/success',
            'line_items'  => [[
                'quantity'   => 1,
                'price_data' => [
                    'currency'     => $currency,
                    'unit_amount'  => $unitAmount,
                    'product_data' => [
                        'name' => 'Checkout with dynamic price',
                    ],
                ],
            ]],
        ]);

        return $session;
    }

    /**
     * Calculate unit amount by currency and amount.
     * 
     * @param  string    $currency
     * @param  int|float $amount
     * @return int
     */
    public function getUnitAmount(string $currency, int|float $amount): int
    {
        $unitAmount = 0;

        if (in_array($currency, CurrencyDecimal::ZERO_DECIMAL)) {
            $unitAmount = $amount;
        } elseif (in_array($currency, CurrencyDecimal::THREE_DECIMAL)) {
            $unitAmount = round($amount * 1000, -1); 
        } elseif (in_array($currency, CurrencyDecimal::SPECIAL_CASE)) {
            $unitAmount = floor($amount) * 100;
        } else {
            $unitAmount = floor($amount * 100);
        }

        return $unitAmount;
    }
}
