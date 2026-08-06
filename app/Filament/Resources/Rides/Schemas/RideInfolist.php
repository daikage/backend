<?php

namespace App\Filament\Resources\Rides\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RideInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer_id')
                    ->numeric(),
                TextEntry::make('driver_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('pickup_address'),
                TextEntry::make('pickup_lat')
                    ->numeric(),
                TextEntry::make('pickup_lng')
                    ->numeric(),
                TextEntry::make('dropoff_address'),
                TextEntry::make('dropoff_lat')
                    ->numeric(),
                TextEntry::make('dropoff_lng')
                    ->numeric(),
                TextEntry::make('distance')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('estimated_fare')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('actual_fare')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('started_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
