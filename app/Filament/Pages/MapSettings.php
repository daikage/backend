<?php

namespace App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;

class MapSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    protected string $view = 'filament.pages.map-settings';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'map_engine' => Cache::get('admin_map_engine', 'google'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('map_engine')
                    ->label('Admin Dashboard Map Engine')
                    ->options([
                        'google' => 'Google Maps',
                        'maplibre' => 'MapLibre GL',
                    ])
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Cache::put('admin_map_engine', $data['map_engine']);

        Notification::make()
            ->title('Settings saved successfully!')
            ->success()
            ->send();
    }
}
