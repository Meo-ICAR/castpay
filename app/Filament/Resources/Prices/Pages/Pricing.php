<?php

namespace App\Filament\Resources\Prices\Pages;

use App\Filament\Resources\Prices\PriceResource;
use Filament\Resources\Pages\Page;

class Pricing extends Page
{
    protected static string $resource = PriceResource::class;

    protected string $view = 'filament.resources.prices.pages.pricing';
}
