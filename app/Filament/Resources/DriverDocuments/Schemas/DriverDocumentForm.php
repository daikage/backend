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
                    ->image()
                    ->directory('documents')
                    ->label('License Document'),
                FileUpload::make('insurance_path')
                    ->image()
                    ->directory('documents')
                    ->label('Insurance Document'),
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
