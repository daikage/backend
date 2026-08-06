<?php

namespace App\Filament\Resources\Rides\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RidesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('driver_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pickup_address')
                    ->searchable(),
                TextColumn::make('pickup_lat')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pickup_lng')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('dropoff_address')
                    ->searchable(),
                TextColumn::make('dropoff_lat')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('dropoff_lng')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('distance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estimated_fare')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('actual_fare')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
