<?php

namespace App\Filament\Pages;

use App\Models\Service;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class CustomerPriceList extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Price List';

    protected ?string $heading = 'Service Price List';

    protected string $view = 'filament.pages.customer-price-list';

    protected static ?string $slug = 'customer-price-list';

    public function getServices(): Collection
    {
        $tenant = Filament::getTenant();
        $user = auth()->user();

        $query = Service::where('company_id', $tenant->id)
            ->with(['prices', 'requiredRole'])
            ->where('is_active', true);

        if ($user && ! $user->hasRole('admin')) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('required_role_id')
                    ->orWhereIn('required_role_id', $user->roles->pluck('id'));
            });
        }

        return $query->get();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('customer') ?? false;
    }
}
