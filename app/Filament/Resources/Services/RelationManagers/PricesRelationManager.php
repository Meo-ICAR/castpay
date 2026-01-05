<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Monthly, Yearly'),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->maxValue(999999.99),
                Select::make('currency')
                    ->options([
                        'usd' => 'USD',
                        'eur' => 'EUR',
                        // Add more currencies as needed
                    ])
                    ->default('usd')
                    ->required(),
                Select::make('type')
                    ->options([
                        'one_time' => 'One Time',
                        'recurring' => 'Recurring',
                    ])
                    ->default('one_time')
                    ->live()
                    ->required(),
                Select::make('interval')
                    ->options([
                        'day' => 'Daily',
                        'week' => 'Weekly',
                        'month' => 'Monthly',
                        'year' => 'Yearly',
                    ])
                    ->visible(fn(callable $get) => $get('type') === 'recurring')
                    ->required(fn(callable $get) => $get('type') === 'recurring'),
                TextInput::make('interval_count')
                    ->numeric()
                    ->default(1)
                    ->visible(fn(callable $get) => $get('type') === 'recurring')
                    ->required(fn(callable $get) => $get('type') === 'recurring'),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money(fn($record) => $record->currency)
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => str($state)->replace('_', ' ')->title())
                    ->color(fn(string $state): string => match ($state) {
                        'one_time' => 'success',
                        'recurring' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('interval')
                    ->formatStateUsing(fn(string $state): string => $state ? ucfirst($state) : '-')
                    ->visible(fn($record) => $record->type === 'recurring'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('stripe_price_id')
                    ->label('Stripe ID')
                    ->placeholder('Not synced')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'one_time' => 'One Time',
                        'recurring' => 'Recurring',
                    ]),
                Filter::make('is_active')
                    ->label('Active')
                    ->query(fn(Builder $query): Builder => $query->where('is_active', true))
                    ->toggle(),
                TrashedFilter::make(),
                // ... other filters
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if ($data['type'] === 'one_time') {
                            $data['interval'] = null;
                            $data['interval_count'] = null;
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Action::make('sync')
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn($record) => $record->syncWithStripe())
                    ->visible(fn($record) => $record->stripe_price_id === null),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
