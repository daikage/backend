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
        $activeRides = \App\Models\Ride::whereIn('status', ['pending', 'accepted', 'arrived', 'started'])
            ->get(['id', 'driver_id', 'pickup_lat', 'pickup_lng'])
            ->map(function ($ride) {
                return [
                    'id' => $ride->id,
                    'lat' => $ride->pickup_lat,
                    'lng' => $ride->pickup_lng,
                ];
            })->toArray();

        return [
            'mapEngine' => Cache::get('admin_map_engine', 'google'),
            'googleMapsApiKey' => config('services.maps.google_maps_api_key'),
            'maplibreApiKey' => config('services.maps.maplibre_api_key'),
            'onlineDriversCount' => \App\Models\User::where('role', 'driver')->where('is_online', true)->count(),
            'activeRides' => $activeRides,
        ];
    }
}
