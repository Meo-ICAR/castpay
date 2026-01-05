<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Price;
use App\Models\Service;
use Stripe\StripeClient;

class StripeSyncPrice
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('cashier.secret'));
    }

    public function syncCompanyToStripe(Company $company): void
    {
        $services = $company->services()->with('prices')->get();

        foreach ($services as $service) {
            $this->syncService($service);
        }
    }

    public function syncService(Service $service): void
    {
        $params = [
            'name' => $service->name,
            'description' => $service->description,
            'active' => (bool) $service->is_active,
        ];

        if ($service->stripe_product_id) {
            $stripeProduct = $this->stripe->products->update($service->stripe_product_id, $params);
        } else {
            $stripeProduct = $this->stripe->products->create($params);
            $service->update(['stripe_product_id' => $stripeProduct->id]);
        }

        foreach ($service->prices as $price) {
            $this->syncPrice($price, $stripeProduct->id);
        }
    }

    public function syncPrice(Price $price, string $stripeProductId): void
    {
        if ($price->stripe_price_id) {
            // Stripe prices are mostly immutable, we usually create a new one or toggle active status
            // For simplicity, we only create if not exists or update active status if it was possible
            return;
        }

        $params = [
            'product' => $stripeProductId,
            'unit_amount' => $price->amount,
            'currency' => $price->currency,
        ];

        if ($price->type === 'recurring') {
            $params['recurring'] = ['interval' => $price->interval];
        }

        $stripePrice = $this->stripe->prices->create($params);
        $price->update(['stripe_price_id' => $stripePrice->id]);
    }
}
