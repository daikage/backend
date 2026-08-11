<?php

namespace App\Filament\Resources\Rides;

use App\Filament\Resources\Rides\Pages\CreateRide;
use App\Filament\Resources\Rides\Pages\EditRide;
use App\Filament\Resources\Rides\Pages\ListRides;
use App\Filament\Resources\Rides\Pages\ViewRide;
use App\Filament\Resources\Rides\Schemas\RideForm;
use App\Filament\Resources\Rides\Schemas\RideInfolist;
use App\Filament\Resources\Rides\Tables\RidesTable;
use App\Models\Ride;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RideResource extends Resource
{
    protected static ?string $model = Ride::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return RideForm::configure($form);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return RideInfolist::configure($infolist);
    }

    public static function table(Table $table): Table
    {
        return RidesTable::configure($table);
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
            'index' => ListRides::route('/'),
            'create' => CreateRide::route('/create'),
            'view' => ViewRide::route('/{record}'),
            'edit' => EditRide::route('/{record}/edit'),
        ];
    }
}
