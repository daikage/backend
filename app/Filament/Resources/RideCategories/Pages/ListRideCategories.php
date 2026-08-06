<?php

namespace App\Filament\Resources\RideCategories\Pages;

use App\Filament\Resources\RideCategories\RideCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRideCategories extends ListRecords
{
    protected static string $resource = RideCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
