<?php

namespace App\Filament\Resources\Ratings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RatingForm
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
                Select::make('rater_id')
                    ->relationship('rater', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('ratee_id')
                    ->relationship('ratee', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('stars')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required(),
                Textarea::make('comment')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
