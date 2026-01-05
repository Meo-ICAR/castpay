<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages\ManageServices;
use App\Models\Service;
use App\Services\StripeSyncService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                Textarea::make('description'),
                Toggle::make('is_active')->default(true),
                Select::make('required_role_id')
                    ->relationship('requiredRole', 'name')
                    ->label('Required Role')
                    ->nullable(),
                TextInput::make('stripe_product_id')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('requiredRole.name')->label('Required Role')->badge(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('stripe_product_id')->label('Stripe ID')->placeholder('Not synced'),
            ])
            ->actions([
                Action::make('sync_stripe')
                    ->label('Sync Stripe')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Service $record, StripeSyncService $syncService) {
                        try {
                            $syncService->syncService($record);
                            Notification::make()->title('Synced to Stripe!')->success()->send();
                        } catch (\Exception $e) {
                            Notification::make()->title('Sync failed: ' . $e->getMessage())->danger()->send();
                        }
                    })
            ])
            ->filters([
                //
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $user = auth()->user();
        if ($user && !$user->hasRole('admin')) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('required_role_id')
                  ->orWhereIn('required_role_id', $user->roles->pluck('id'));
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServices::route('/'),
        ];
    }
}
