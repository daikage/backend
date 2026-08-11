<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;

class VehicleForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('make')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('year')
                    ->required(),
                TextInput::make('license_plate')
                    ->required(),
                TextInput::make('color')
                    ->required(),
                TextInput::make('type')
                    ->required()
                    ->default('sedan'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
