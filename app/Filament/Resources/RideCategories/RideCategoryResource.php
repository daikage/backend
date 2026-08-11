<?php

namespace App\Filament\Resources\RideCategories;

use App\Filament\Resources\RideCategories\Pages\CreateRideCategory;
use App\Filament\Resources\RideCategories\Pages\EditRideCategory;
use App\Filament\Resources\RideCategories\Pages\ListRideCategories;
use App\Filament\Resources\RideCategories\Schemas\RideCategoryForm;
use App\Filament\Resources\RideCategories\Tables\RideCategoriesTable;
use App\Models\RideCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RideCategoryResource extends Resource
{
    protected static ?string $model = RideCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Form $form): Form
    {
        return RideCategoryForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return RideCategoriesTable::configure($table);
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
            'index' => ListRideCategories::route('/'),
            'create' => CreateRideCategory::route('/create'),
            'edit' => EditRideCategory::route('/{record}/edit'),
        ];
    }
}
