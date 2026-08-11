<?php

namespace App\Filament\Resources\Earnings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EarningForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('driver_id')
                    ->relationship('driver', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('ride_id')
                    ->relationship('ride', 'id')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('amount')
                    ->numeric()
                    ->prefix('₦')
                    ->required(),
                TextInput::make('commission_deducted')
                    ->numeric()
                    ->prefix('₦')
                    ->default(0.00)
                    ->required(),
            ]);
    }
}
