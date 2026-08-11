<?php

namespace App\Filament\Resources\DriverDocuments;

use App\Filament\Resources\DriverDocuments\Pages\CreateDriverDocument;
use App\Filament\Resources\DriverDocuments\Pages\EditDriverDocument;
use App\Filament\Resources\DriverDocuments\Pages\ListDriverDocuments;
use App\Filament\Resources\DriverDocuments\Schemas\DriverDocumentForm;
use App\Filament\Resources\DriverDocuments\Tables\DriverDocumentsTable;
use App\Models\DriverDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DriverDocumentResource extends Resource
{
    protected static ?string $model = DriverDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Form $form): Form
    {
        return DriverDocumentForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return DriverDocumentsTable::configure($table);
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
            'index' => ListDriverDocuments::route('/'),
            'create' => CreateDriverDocument::route('/create'),
            'edit' => EditDriverDocument::route('/{record}/edit'),
        ];
    }
}
