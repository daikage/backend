<?php

namespace App\Filament\Resources\DriverDocuments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class DriverDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Driver')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('license_path')
                    ->disk('public')
                    ->label('License'),
                ImageColumn::make('insurance_path')
                    ->disk('public')
                    ->label('Insurance'),
                ImageColumn::make('vehicle_license_path')
                    ->disk('public')
                    ->label('Vehicle License'),
                ImageColumn::make('road_worthiness_path')
                    ->disk('public')
                    ->label('Road Worthiness'),
                ImageColumn::make('hackney_permit_path')
                    ->disk('public')
                    ->label('Hackney Permit'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
