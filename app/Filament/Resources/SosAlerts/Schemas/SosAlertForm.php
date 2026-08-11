<?php

namespace App\Filament\Resources\SosAlerts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SosAlertForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ride_id')
                    ->relationship('ride', 'id')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('lat')
                    ->label('Latitude')
                    ->numeric()
                    ->required(),
                TextInput::make('lng')
                    ->label('Longitude')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'resolved' => 'Resolved',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }
}
