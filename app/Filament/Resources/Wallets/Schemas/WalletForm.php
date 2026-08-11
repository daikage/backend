<?php

namespace App\Filament\Resources\Wallets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class WalletForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('balance')
                    ->numeric()
                    ->default(0.00)
                    ->prefix('₦')
                    ->required(),
            ]);
    }
}
