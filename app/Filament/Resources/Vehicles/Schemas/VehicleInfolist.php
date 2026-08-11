<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class VehicleInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('make'),
                TextEntry::make('model'),
                TextEntry::make('year'),
                TextEntry::make('license_plate'),
                TextEntry::make('color'),
                TextEntry::make('type'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
