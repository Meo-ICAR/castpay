<?php

namespace App\Filament\Resources\Prices;

use App\Filament\Resources\Prices\Pages\ManagePrices;
use App\Models\Price;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PriceResource extends Resource
{
    protected static ?string $model = Price::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_id')
                    ->relationship('service', 'name')
                    ->required(),
                TextInput::make('name')->placeholder('Monthly, Yearly...'),
                TextInput::make('amount')->numeric()->prefix('$')->required(),
                TextInput::make('currency')->default('usd')->required(),
                Select::make('type')
                    ->options([
                        'one_time' => 'One Time',
                        'recurring' => 'Recurring',
                    ])->default('one_time'),
                Select::make('interval')
                    ->options([
                        'day' => 'Day',
                        'week' => 'Week',
                        'month' => 'Month',
                        'year' => 'Year',
                    ])->visible(fn ($get) => $get('type') === 'recurring'),
                TextInput::make('stripe_price_id'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service.name')->label('Service'),
                TextColumn::make('name'),
                TextColumn::make('amount')->money(fn ($record) => $record->currency),
                TextColumn::make('type'),
            ])
            ->filters([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePrices::route('/'),
        ];
    }
}
