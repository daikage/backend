<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold">Live Driver Map Tracking</h2>
            <div class="flex space-x-4">
                <span class="flex items-center text-sm"><span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span> Active</span>
            </div>
        </div>
        <div class="w-full h-96 rounded-xl overflow-hidden relative border border-gray-200 dark:border-gray-700" wire:ignore>
            
            @if($mapEngine === 'maplibre')
                <div id="maplibre-container" class="w-full h-full"></div>
                <link href="https://unpkg.com/maplibre-gl@3.3.0/dist/maplibre-gl.css" rel="stylesheet" />
                <script src="https://unpkg.com/maplibre-gl@3.3.0/dist/maplibre-gl.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var map = new maplibregl.Map({
                            container: 'maplibre-container',
                            style: 'https://demotiles.maplibre.org/style.json', // MapLibre default style
                            center: [3.3792, 6.5244], // Longitude, Latitude (Lagos)
                            zoom: 12
                        });
                        
                        // Example: Add a live driver marker
                        new maplibregl.Marker({color: "#556B2F"})
                            .setLngLat([3.3792, 6.5244])
                            .addTo(map);
                    });
                </script>
            @else
                <div id="google-map-container" class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                    <p class="text-gray-500">Google Maps requires a valid API Key to render. Switch to MapLibre in Settings to see the interactive map immediately.</p>
                </div>
                <!-- <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script> -->
            @endif
            
            <div class="absolute bottom-4 left-4 z-10 p-4 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 max-w-sm pointer-events-none">
                <h3 class="text-md font-bold text-gray-800 dark:text-gray-100 mb-1">Engine: {{ strtoupper($mapEngine) }}</h3>
                <p class="text-xs text-gray-500">Real-time WebSocket active</p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
