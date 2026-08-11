<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('role'),
                TextEntry::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('phone_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('avatar')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('is_online')
                    ->boolean(),
                TextEntry::make('rating')
                    ->numeric(),
                TextEntry::make('provider')
                    ->placeholder('-'),
                TextEntry::make('provider_id')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),

                \Filament\Infolists\Components\Section::make('Driver Documents')
                    ->schema([
                        \Filament\Infolists\Components\ImageEntry::make('driverDocument.license_path')
                            ->label('License')
                            ->disk(config('filesystems.default') === 'local' ? 'public' : config('filesystems.default'))
                            ->placeholder('Not uploaded'),
                        \Filament\Infolists\Components\ImageEntry::make('driverDocument.insurance_path')
                            ->label('Insurance')
                            ->disk(config('filesystems.default') === 'local' ? 'public' : config('filesystems.default'))
                            ->placeholder('Not uploaded'),
                        \Filament\Infolists\Components\ImageEntry::make('driverDocument.vehicle_license_path')
                            ->label('Vehicle License')
                            ->disk(config('filesystems.default') === 'local' ? 'public' : config('filesystems.default'))
                            ->placeholder('Not uploaded'),
                        \Filament\Infolists\Components\ImageEntry::make('driverDocument.road_worthiness_path')
                            ->label('Road Worthiness')
                            ->disk(config('filesystems.default') === 'local' ? 'public' : config('filesystems.default'))
                            ->placeholder('Not uploaded'),
                        \Filament\Infolists\Components\ImageEntry::make('driverDocument.hackney_permit_path')
                            ->label('Hackney Permit')
                            ->disk(config('filesystems.default') === 'local' ? 'public' : config('filesystems.default'))
                            ->placeholder('Not uploaded'),
                        \Filament\Infolists\Components\TextEntry::make('driverDocument.status')
                            ->label('Document Status')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->placeholder('No status'),
                    ])
                    ->columns(3)
                    ->visible(fn ($record) => $record?->role === 'driver' && $record?->driverDocument),
            ]);
    }
}
