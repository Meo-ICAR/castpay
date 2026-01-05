<?php

namespace App\Filament\Resources\Prices;

use App\Filament\Resources\Prices\Pages\ManagePrices;
use App\Models\Price;
use App\Services\StripeSyncPrice;
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

class PriceResource extends Resource
{
    protected static ?string $model = Price::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_id')
                    ->relationship(
                        name: 'service',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->where('company_id', filament()->getTenant()->id),
                    )
                    ->required(),
                TextInput::make('name')->placeholder('Monthly, Yearly...'),
                TextInput::make('amount')->numeric()->prefix('$')->required(),
                TextInput::make('currency')->default('usd')->required(),
                Select::make('type')
                    ->options([
                        'one_time' => 'One Time',
                        'recurring' => 'Recurring',
                    ])
                    ->default('one_time'),
                Select::make('interval')
                    ->options([
                        'day' => 'Day',
                        'week' => 'Week',
                        'month' => 'Month',
                        'year' => 'Year',
                    ])
                    ->visible(fn($get) => $get('type') === 'recurring'),
                TextInput::make('stripe_price_id'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        if ($user && $user->hasRole('superadmin')) {
            return parent::getEloquentQuery()->withoutGlobalScopes();
        }

        return parent::getEloquentQuery();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->selectable()
            ->columns([
                TextColumn::make('service.name')->label('Service'),
                TextColumn::make('name'),
                TextColumn::make('amount')->money(fn($record) => $record->currency),
                TextColumn::make('type'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('sync_stripe')
                        ->label('Sync with Stripe')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function (Collection $records, StripeSyncPrice $syncPrice) {
                            $synced = 0;
                            $errors = [];

                            foreach ($records as $record) {
                                try {
                                    $result = $syncPrice->sync($record, $record->service->stripe_product_id);
                                    if ($result['success']) {
                                        $synced++;
                                    } else {
                                        $errors[] = "Price #{$record->id}: " . ($result['message'] ?? 'Unknown error');
                                    }
                                } catch (\Exception $e) {
                                    $errors[] = "Price #{$record->id}: " . $e->getMessage();
                                }
                            }

                            if (count($errors) === 0) {
                                Notification::make()
                                    ->title("Successfully synced $synced prices to Stripe")
                                    ->success()
                                    ->send();
                            } else {
                                $errorMessage = count($errors) === 1
                                    ? '1 price failed to sync'
                                    : count($errors) . ' prices failed to sync';

                                Notification::make()
                                    ->title("$errorMessage")
                                    ->body(implode("\n", $errors))
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePrices::route('/'),
        ];
    }
}
