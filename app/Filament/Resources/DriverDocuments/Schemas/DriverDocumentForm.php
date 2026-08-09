<?php

namespace App\Filament\Resources\DriverDocuments\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;

class DriverDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label('Driver'),
                FileUpload::make('license_path')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                    ->directory('documents')
                    ->label('License Document'),
                FileUpload::make('insurance_path')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                    ->directory('documents')
                    ->label('Insurance Document'),
                FileUpload::make('vehicle_license_path')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                    ->directory('documents')
                    ->label('Vehicle License'),
                FileUpload::make('road_worthiness_path')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                    ->directory('documents')
                    ->label('Road Worthiness Certificate'),
                FileUpload::make('hackney_permit_path')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                    ->directory('documents')
                    ->label('Hackney Permit'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('pending'),
            ]);
    }
}
