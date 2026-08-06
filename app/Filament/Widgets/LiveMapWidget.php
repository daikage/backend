<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class LiveMapWidget extends Widget
{
    protected string $view = 'filament.widgets.live-map-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        return [
            'mapEngine' => Cache::get('admin_map_engine', 'google'),
            'googleMapsApiKey' => config('services.maps.google_maps_api_key'),
            'maplibreApiKey' => config('services.maps.maplibre_api_key'),
            'onlineDriversCount' => \App\Models\User::where('role', 'driver')->where('is_online', true)->count(),
        ];
    }
}
