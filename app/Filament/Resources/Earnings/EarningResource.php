<?php

namespace App\Filament\Resources\Earnings;

use App\Filament\Resources\Earnings\Pages\CreateEarning;
use App\Filament\Resources\Earnings\Pages\EditEarning;
use App\Filament\Resources\Earnings\Pages\ListEarnings;
use App\Filament\Resources\Earnings\Schemas\EarningForm;
use App\Filament\Resources\Earnings\Tables\EarningsTable;
use App\Models\Earning;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EarningResource extends Resource
{
    protected static ?string $model = Earning::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Form $form): Form
    {
        return EarningForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return EarningsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEarnings::route('/'),
            'create' => CreateEarning::route('/create'),
            'edit' => EditEarning::route('/{record}/edit'),
        ];
    }
}
