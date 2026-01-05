<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\ServiceResource\RelationManagers\PricesRelationManager;
use App\Filament\Resources\Services\Pages\ManageServices;
use App\Models\Service;
use App\Services\StripeSyncService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
            ->selectable()
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('requiredRole.name')->label('Required Role')->badge(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('stripe_product_id')->label('Stripe ID')->placeholder('Not synced'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('sync_stripe')
                        ->label('Sync with Stripe')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function (Collection $records, StripeSyncService $syncService) {
                            $synced = 0;
                            $errors = [];

                            foreach ($records as $record) {
                                try {
                                    $syncService->syncService($record);
                                    $synced++;
                                } catch (\Exception $e) {
                                    $errors[] = "Service #{$record->id}: " . $e->getMessage();
                                }
                            }
                            if (count($errors) === 0) {
                                Notification::make()
                                    ->title("Successfully synced $synced services to Stripe")
                                    ->success()
                                    ->send();
                            } else {
                                $errorMessage = count($errors) === 1
                                    ? '1 service failed to sync'
                                    : count($errors) . ' services failed to sync';

                                Notification::make()
                                    ->title($errorMessage)
                                    ->body(implode("\n", $errors))
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                ])
            ])
            ->filters([
                //
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if ($user && $user->hasRole('superadmin')) {
            return Service::query();  // Access all records globally
        }

        $query = parent::getEloquentQuery();

        if ($user && !$user->hasRole('admin')) {
            $query->where(function ($q) use ($user) {
                $q
                    ->whereNull('required_role_id')
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

    public static function getRelations(): array
    {
        return [
            PricesRelationManager::class,
        ];
    }
}
