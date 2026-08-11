<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class TransactionInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->components([
                TextEntry::make('ride_id')
                    ->numeric(),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('payment_method')
                    ->placeholder('-'),
                TextEntry::make('payment_status'),
                TextEntry::make('transaction_reference')
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
