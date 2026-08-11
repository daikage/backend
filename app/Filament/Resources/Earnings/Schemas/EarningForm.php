<?php

namespace App\Filament\Resources\Earnings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class EarningForm
{
    public static function configure(Form $form): Form
    {
        return $form
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
