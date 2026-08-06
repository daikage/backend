<?php

namespace App\Filament\Resources\RideCategories\Pages;

use App\Filament\Resources\RideCategories\RideCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRideCategory extends EditRecord
{
    protected static string $resource = RideCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
