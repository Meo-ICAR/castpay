<?php

namespace App\Services;

use App\Models\Price;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeSyncPrice
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('cashier.secret'));
    }

    /**
     * Sync a single price to Stripe
     *
     * @param Price $price
     * @param string $stripeProductId
     * @return array
     */
    public function sync(Price $price, string $stripeProductId): array
    {
        try {
            // If price already exists in Stripe, archive it and create a new one
            if ($price->stripe_price_id) {
                return $this->updateExistingPrice($price, $stripeProductId);
            }

            // Create new price in Stripe
            return $this->createNewPrice($price, $stripeProductId);
        } catch (\Exception $e) {
            Log::error('Failed to sync price to Stripe', [
                'price_id' => $price->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to sync price: ' . $e->getMessage(),
                'price_id' => $price->id
            ];
        }
    }

    /**
     * Create a new price in Stripe
     *
     * @param Price $price
     * @param string $stripeProductId
     * @return array
     * @throws \Stripe\Exception\ApiErrorException
     */
    protected function createNewPrice(Price $price, string $stripeProductId): array
    {
        $params = $this->buildPriceParams($price, $stripeProductId);
        $stripePrice = $this->stripe->prices->create($params);

        $price->update(['stripe_price_id' => $stripePrice->id]);

        return [
            'success' => true,
            'message' => 'Price created successfully',
            'stripe_price_id' => $stripePrice->id,
            'price_id' => $price->id
        ];
    }

    /**
     * Update an existing price in Stripe by archiving the old one and creating a new one
     *
     * @param Price $price
     * @param string $stripeProductId
     * @return array
     * @throws \Stripe\Exception\ApiErrorException
     */
    protected function updateExistingPrice(Price $price, string $stripeProductId): array
    {
        // Archive the old price in Stripe
        $this->stripe->prices->update($price->stripe_price_id, [
            'active' => false
        ]);

        // Create new price with updated data
        return $this->createNewPrice($price, $stripeProductId);
    }

    /**
     * Build the parameters array for Stripe price creation
     *
     * @param Price $price
     * @param string $stripeProductId
     * @return array
     */
    protected function buildPriceParams(Price $price, string $stripeProductId): array
    {
        $params = [
            'product' => $stripeProductId,
            'unit_amount' => $price->amount * 100,  // Convert to cents
            'currency' => strtolower($price->currency),
            'metadata' => [
                'price_id' => $price->id,
                'created_at' => now()->toDateTimeString(),
            ]
        ];

        if ($price->type === 'recurring') {
            $params['recurring'] = [
                'interval' => $price->interval,
                'interval_count' => $price->interval_count ?? 1,
            ];
        }

        return $params;
    }

    /**
     * Get price details from Stripe
     *
     * @param string $stripePriceId
     * @return array|null
     */
    public function getStripePrice(string $stripePriceId): ?array
    {
        try {
            $price = $this->stripe->prices->retrieve($stripePriceId);
            return $price->toArray();
        } catch (ApiErrorException $e) {
            Log::error('Failed to retrieve price from Stripe', [
                'stripe_price_id' => $stripePriceId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
