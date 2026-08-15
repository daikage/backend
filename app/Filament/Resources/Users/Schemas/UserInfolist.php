<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

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

                Section::make('Driver Documents')
                    ->description('Uploaded KYC documents. Click a filename to open the file.')
                    ->schema([
                        self::documentEntry('driverDocument.license_path', 'License'),
                        self::documentEntry('driverDocument.insurance_path', 'Insurance'),
                        self::documentEntry('driverDocument.vehicle_license_path', 'Vehicle License'),
                        self::documentEntry('driverDocument.road_worthiness_path', 'Road Worthiness'),
                        self::documentEntry('driverDocument.hackney_permit_path', 'Hackney Permit'),
                        TextEntry::make('driverDocument.status')
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

    /**
     * Build a display block for a single uploaded driver document.
     *
     * - Images (png/jpg/jpeg/webp/…) get a visible thumbnail preview.
     * - Every file type (including PDFs) also gets a clickable filename that
     *   opens the original file in a new tab, so uploads are always viewable
     *   from the driver data view, regardless of whether they're an image or a
     *   document (e.g. a signed PDF).
     */
    protected static function documentEntry(string $field, string $label): Group
    {
        return Group::make()
            ->schema([
                ImageEntry::make($field)
                    ->label($label)
                    ->disk(static::documentDisk())
                    ->placeholder('Not uploaded')
                    ->height(150)
                    ->visible(fn ($state) => is_string($state) && static::hasImageExtension($state)),
                TextEntry::make($field)
                    ->label('File')
                    ->getStateUsing(fn (?string $state): ?string => filled($state) ? basename($state) : null)
                    ->url(fn (?string $state) => static::documentUrl($state))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-eye')
                    ->placeholder('Not uploaded')
                    ->visible(fn (?string $state) => filled($state)),
            ])
            ->columns(1);
    }

    /**
     * Resolve the disk that uploaded KYC documents are stored on. Keeping the
     * exact convention used by the API controller: when the system default disk
     * is 'local', documents go to the 'public' disk so they are web-accessible.
     */
    protected static function documentDisk(): string
    {
        return config('filesystems.default') === 'local' ? 'public' : config('filesystems.default');
    }

    /**
     * Build a publicly accessible URL for a stored document path. Handles both
     * relative file paths (resolved through the configured disk) and values
     * that are already absolute URLs / data URIs (e.g. cloud uploads).
     */
    protected static function documentUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL) !== false || str_starts_with($path, 'data:')) {
            return $path;
        }

        return Storage::disk(static::documentDisk())->url($path);
    }

    /**
     * Whether the stored path points to a raster/vector image (which can be
     * rendered by ImageEntry) rather than a plain document such as a PDF.
     */
    protected static function hasImageExtension(?string $path): bool
    {
        return in_array(
            strtolower(pathinfo((string) $path, PATHINFO_EXTENSION)),
            ['png', 'jpg', 'jpeg', 'webp', 'gif', 'bmp', 'svg'],
            true,
        );
    }
}
