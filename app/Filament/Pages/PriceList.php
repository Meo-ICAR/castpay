<?php

namespace App\Filament\Pages;

use App\Models\Company;
use App\Models\Service;
use App\Services\StripeSyncService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PriceList extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.price-list';

    public function getServices()
    {
        $tenant = Filament::getTenant();
        $user = auth()->user();

        $query = Service::where('company_id', $tenant->id)
            ->with(['prices', 'requiredRole'])
            ->where('is_active', true);

        if ($user && !$user->hasRole('admin')) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('required_role_id')
                  ->orWhereIn('required_role_id', $user->roles->pluck('id'));
            });
        }

        return $query->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_to_stripe')
                ->label('Export All to Stripe')
                ->color('success')
                ->icon('heroicon-o-cloud-arrow-up')
                ->action(function (StripeSyncService $syncService) {
                    try {
                        $syncService->syncCompanyToStripe(Filament::getTenant());
                        Notification::make()->title('Company Price List synced to Stripe!')->success()->send();
                    } catch (\Exception $e) {
                        Notification::make()->title('Sync failed: ' . $e->getMessage())->danger()->send();
                    }
                })
        ];
    }
}
