<?php

namespace App\Filament\Resources\Rides\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class RideForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                TextInput::make('customer_id')
                    ->required()
                    ->numeric(),
                TextInput::make('driver_id')
                    ->numeric(),
                TextInput::make('pickup_address')
                    ->required(),
                TextInput::make('pickup_lat')
                    ->required()
                    ->numeric(),
                TextInput::make('pickup_lng')
                    ->required()
                    ->numeric(),
                TextInput::make('dropoff_address')
                    ->required(),
                TextInput::make('dropoff_lat')
                    ->required()
                    ->numeric(),
                TextInput::make('dropoff_lng')
                    ->required()
                    ->numeric(),
                TextInput::make('distance')
                    ->numeric(),
                TextInput::make('estimated_fare')
                    ->numeric(),
                TextInput::make('actual_fare')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
