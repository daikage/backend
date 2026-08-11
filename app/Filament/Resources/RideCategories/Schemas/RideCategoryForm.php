<?php

namespace App\Filament\Resources\RideCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RideCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('service_type')
                    ->options([
                        'single' => 'Single Ride',
                        'haulage' => 'Haulage',
                        'dispatch' => 'Dispatch',
                        'interstate' => 'Interstate',
                    ])
                    ->default('single')
                    ->required(),
                TextInput::make('base_fare')
                    ->numeric()
                    ->prefix('₦')
                    ->required(),
                TextInput::make('per_km_rate')
                    ->numeric()
                    ->prefix('₦')
                    ->required(),
                TextInput::make('image_url')
                    ->url()
                    ->maxLength(255),
            ]);
    }
}
