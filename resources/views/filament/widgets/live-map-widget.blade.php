<x-filament-widgets::widget>
    <x-filament::section>
        <style>
            .map-widget-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1rem;
            }
            .map-widget-title h2 {
                font-size: 1.25rem;
                font-weight: bold;
                line-height: 1.2;
                margin: 0;
            }
            .map-widget-title p {
                font-size: 0.875rem;
                color: #6b7280;
                margin: 0;
            }
            .map-live-badge {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                background-color: rgba(107, 114, 128, 0.1);
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
            }
            .map-pulse-dot {
                position: relative;
                width: 0.75rem;
                height: 0.75rem;
            }
            .map-pulse-dot .dot-inner {
                position: absolute;
                width: 100%;
                height: 100%;
                background-color: #22c55e;
                border-radius: 50%;
            }
            .map-pulse-dot .dot-outer {
                position: absolute;
                width: 100%;
                height: 100%;
                background-color: #4ade80;
                border-radius: 50%;
                opacity: 0.75;
                animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
            }
            @keyframes ping {
                75%, 100% {
                    transform: scale(2);
                    opacity: 0;
                }
            }
            .map-container-wrapper {
                width: 100%;
                height: 650px;
                border-radius: 1rem;
                overflow: hidden;
                position: relative;
                border: 1px solid rgba(107, 114, 128, 0.2);
            }
            .map-overlay-stats {
                position: absolute;
                top: 1rem;
                left: 1rem;
                z-index: 10;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                pointer-events: none;
            }
            .map-stat-card {
                background-color: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(8px);
                padding: 0.75rem 1rem;
                border-radius: 0.75rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                border: 1px solid rgba(107, 114, 128, 0.1);
                min-width: 200px;
            }
            .dark .map-stat-card {
                background-color: rgba(17, 24, 39, 0.95);
                border-color: rgba(75, 85, 99, 0.4);
            }
            .map-stat-label {
                font-size: 0.75rem;
                font-weight: 600;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin: 0 0 0.25rem 0;
            }
            .map-stat-value {
                display: flex;
                align-items: center;
                font-size: 1.125rem;
                font-weight: bold;
            }
            .map-stat-value svg {
                width: 1.25rem;
                height: 1.25rem;
                margin-right: 0.5rem;
            }
            .map-overlay-bottom {
                position: absolute;
                bottom: 1rem;
                right: 1rem;
                z-index: 10;
                pointer-events: none;
                background-color: rgba(85, 107, 47, 0.9);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                font-size: 0.875rem;
                font-weight: 500;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }
            .missing-key-overlay {
                position: absolute;
                inset: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                background-color: #f9fafb;
                z-index: 10;
                text-align: center;
            }
            .dark .missing-key-overlay {
                background-color: #111827;
            }
            .missing-key-overlay svg {
                width: 3rem;
                height: 3rem;
                color: #9ca3af;
                margin-bottom: 0.75rem;
            }
        </style>
        
        <div class="map-widget-header">
            <div class="map-widget-title">
                <h2 class="text-gray-900 dark:text-white">Live Drivers Map</h2>
                <p>Real-time tracking of active drivers on the road.</p>
            </div>
            <div class="map-live-badge">
                <div class="map-pulse-dot">
                  <div class="dot-outer"></div>
                  <div class="dot-inner"></div>
                </div>
                <span class="text-gray-700 dark:text-gray-300" style="font-size: 0.875rem; font-weight: 500;">Live Updates</span>
            </div>
        </div>
        
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
        
        <div class="map-container-wrapper" wire:ignore>
            
            @if($mapEngine === 'maplibre')
                <div 
                    x-data="{
                        markers: {},
                        initEcho() {
                            if (window.Echo) return;
                            window.Pusher = Pusher;
                            window.Echo = new Echo({
                                broadcaster: 'pusher',
                                key: '{{ env('PUSHER_APP_KEY') }}',
                                cluster: '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}',
                                wsHost: '{{ env('PUSHER_HOST') }}' ? '{{ env('PUSHER_HOST') }}' : `ws-${'{{ env('PUSHER_APP_CLUSTER', 'mt1') }}'}.pusher.com`,
                                wsPort: {{ env('PUSHER_PORT') ?: 80 }},
                                wssPort: {{ env('PUSHER_PORT') ?: 443 }},
                                forceTLS: '{{ env('PUSHER_SCHEME', 'https') }}' === 'https',
                                enabledTransports: ['ws', 'wss'],
                            });
                        },
                        initMap() {
                            var mapStyle = '{{ $maplibreApiKey ? "https://api.maptiler.com/maps/streets/style.json?key=".$maplibreApiKey : "https://demotiles.maplibre.org/style.json" }}';
                            var map = new maplibregl.Map({
                                container: this.$refs.mapContainer,
                                style: mapStyle,
                                center: [3.3792, 6.5244], // Longitude, Latitude (Lagos)
                                zoom: 12,
                                attributionControl: false
                            });
                            
                            const activeRides = @json($activeRides);
                            
                            const createMarkerEl = () => {
                                var el = document.createElement('div');
                                el.style.width = '30px';
                                el.style.height = '30px';
                                el.style.backgroundColor = '#556B2F';
                                el.style.borderRadius = '50%';
                                el.style.border = '3px solid white';
                                el.style.boxShadow = '0 2px 4px rgba(0,0,0,0.3)';
                                return el;
                            };

                            const activeRides = JSON.parse(atob('{{ base64_encode(json_encode($activeRides)) }}'));
                            activeRides.forEach(ride => {
                                this.markers[ride.id] = new maplibregl.Marker(createMarkerEl())
                                    .setLngLat([ride.lng, ride.lat])
                                    .addTo(map);
                            });

                            this.initEcho();
                            window.Echo.private('admin.map')
                                .listen('DriverLocationUpdated', (e) => {
                                    if (this.markers[e.rideId]) {
                                        this.markers[e.rideId].setLngLat([e.lng, e.lat]);
                                    } else {
                                        this.markers[e.rideId] = new maplibregl.Marker(createMarkerEl())
                                            .setLngLat([e.lng, e.lat])
                                            .addTo(map);
                                    }
                                });
                        }
                    }"
                    x-init="
                        if (typeof maplibregl === 'undefined') {
                            let script = document.createElement('script');
                            script.src = 'https://unpkg.com/maplibre-gl@3.3.0/dist/maplibre-gl.js';
                            script.onload = () => initMap();
                            document.head.appendChild(script);
                            
                            let link = document.createElement('link');
                            link.href = 'https://unpkg.com/maplibre-gl@3.3.0/dist/maplibre-gl.css';
                            link.rel = 'stylesheet';
                            document.head.appendChild(link);
                        } else {
                            initMap();
                        }
                    "
                    style="width: 100%; height: 100%;"
                >
                    <div x-ref="mapContainer" style="width: 100%; height: 100%;"></div>
                </div>
            @else
                <div 
                    x-data="{
                        markers: {},
                        initEcho() {
                            if (window.Echo) return;
                            window.Pusher = Pusher;
                            window.Echo = new Echo({
                                broadcaster: 'pusher',
                                key: '{{ env('PUSHER_APP_KEY') }}',
                                cluster: '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}',
                                wsHost: '{{ env('PUSHER_HOST') }}' ? '{{ env('PUSHER_HOST') }}' : `ws-${'{{ env('PUSHER_APP_CLUSTER', 'mt1') }}'}.pusher.com`,
                                wsPort: {{ env('PUSHER_PORT') ?: 80 }},
                                wssPort: {{ env('PUSHER_PORT') ?: 443 }},
                                forceTLS: '{{ env('PUSHER_SCHEME', 'https') }}' === 'https',
                                enabledTransports: ['ws', 'wss'],
                            });
                        },
                        initMap() {
                            if (!this.$refs.mapContainer) return;
                            const lagos = { lat: 6.5244, lng: 3.3792 };
                            const map = new google.maps.Map(this.$refs.mapContainer, {
                                zoom: 12,
                                center: lagos,
                                mapTypeControl: false,
                                streetViewControl: false,
                                fullscreenControl: false,
                                styles: [
                                    { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#e9e9e9' }, { lightness: 17 }] },
                                    { featureType: 'landscape', elementType: 'geometry', stylers: [{ color: '#f5f5f5' }, { lightness: 20 }] },
                                    { featureType: 'road.highway', elementType: 'geometry.fill', stylers: [{ color: '#ffffff' }, { lightness: 17 }] },
                                    { featureType: 'road.highway', elementType: 'geometry.stroke', stylers: [{ color: '#ffffff' }, { lightness: 29 }, { weight: 0.2 }] },
                                    { featureType: 'road.arterial', elementType: 'geometry', stylers: [{ color: '#ffffff' }, { lightness: 18 }] },
                                    { featureType: 'road.local', elementType: 'geometry', stylers: [{ color: '#ffffff' }, { lightness: 16 }] },
                                    { featureType: 'poi', elementType: 'geometry', stylers: [{ color: '#f5f5f5' }, { lightness: 21 }] },
                                    { featureType: 'poi.park', elementType: 'geometry', stylers: [{ color: '#dedede' }, { lightness: 21 }] },
                                    { elementType: 'labels.text.stroke', stylers: [{ visibility: 'on' }, { color: '#ffffff' }, { lightness: 16 }] },
                                    { elementType: 'labels.text.fill', stylers: [{ saturation: 36 }, { color: '#333333' }, { lightness: 40 }] },
                                    { elementType: 'labels.icon', stylers: [{ visibility: 'off' }] },
                                    { featureType: 'transit', elementType: 'geometry', stylers: [{ color: '#f2f2f2' }, { lightness: 19 }] },
                                    { featureType: 'administrative', elementType: 'geometry.fill', stylers: [{ color: '#fefefe' }, { lightness: 20 }] },
                                    { featureType: 'administrative', elementType: 'geometry.stroke', stylers: [{ color: '#fefefe' }, { lightness: 17 }, { weight: 1.2 }] }
                                ]
                            });
                            
                            const getIcon = () => ({
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 10,
                                fillColor: '#556B2F',
                                fillOpacity: 1,
                                strokeColor: '#ffffff',
                                strokeWeight: 3,
                            });

                            const activeRides = JSON.parse(atob('{{ base64_encode(json_encode($activeRides)) }}'));
                            activeRides.forEach(ride => {
                                this.markers[ride.id] = new google.maps.Marker({
                                    position: { lat: ride.lat, lng: ride.lng },
                                    map: map,
                                    icon: getIcon(),
                                });
                            });

                            this.initEcho();
                            window.Echo.private('admin.map')
                                .listen('DriverLocationUpdated', (e) => {
                                    if (this.markers[e.rideId]) {
                                        this.markers[e.rideId].setPosition({ lat: e.lat, lng: e.lng });
                                    } else {
                                        this.markers[e.rideId] = new google.maps.Marker({
                                            position: { lat: e.lat, lng: e.lng },
                                            map: map,
                                            icon: getIcon(),
                                        });
                                    }
                                });
                        }
                    }"
                    x-init="
                        @if($googleMapsApiKey)
                            window.initGoogleMap = () => initMap();
                            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                                let script = document.createElement('script');
                                script.src = 'https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initGoogleMap';
                                script.async = true;
                                script.defer = true;
                                document.head.appendChild(script);
                            } else {
                                initMap();
                            }
                        @endif
                    "
                    style="width: 100%; height: 100%; position: relative;"
                >
                    <div x-ref="mapContainer" style="width: 100%; height: 100%;"></div>
                    
                    @if(!$googleMapsApiKey)
                        <div class="missing-key-overlay">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p style="font-size: 1.125rem; font-weight: 500; margin-bottom: 0.25rem;" class="text-gray-600 dark:text-gray-300">Google Maps API Key Missing</p>
                            <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">Please add your API key to the .env file</p>
                            <code style="padding: 0.25rem 0.75rem; background-color: rgba(107, 114, 128, 0.1); border-radius: 0.25rem; font-size: 0.875rem;">GOOGLE_MAPS_API_KEY=your_key_here</code>
                        </div>
                    @endif
                </div>
            @endif
            
            <div class="map-overlay-stats">
                <div class="map-stat-card">
                    <p class="map-stat-label">Active Engine</p>
                    <div class="map-stat-value text-gray-800 dark:text-gray-100">
                        @if($mapEngine === 'google')
                            <svg style="color: #3b82f6;" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        @else
                            <svg style="color: #6366f1;" viewBox="0 0 24 24" fill="currentColor"><path d="M20.5 3l-.16.03L15 5.1 9 3 3.36 4.9c-.21.07-.36.25-.36.48V20.5c0 .28.22.5.5.5l.16-.03L9 18.9l6 2.1 5.64-1.9c.21-.07.36-.25.36-.48V3.5c0-.28-.22-.5-.5-.5zM15 19l-6-2.11V5l6 2.11V19z"/></svg>
                        @endif
                        <span>{{ $mapEngine === 'google' ? 'Google Maps' : 'MapLibre GL' }}</span>
                    </div>
                </div>
                
                <div class="map-stat-card">
                    <p class="map-stat-label">Drivers Online</p>
                    <div style="display: flex; align-items: flex-end; gap: 0.5rem;">
                        <span style="font-size: 1.5rem; line-height: 1;" class="map-stat-value text-gray-800 dark:text-gray-100">{{ $onlineDriversCount }}</span>
                        <span style="font-size: 0.875rem; color: #6b7280; font-weight: 500; margin-bottom: 0.125rem;">Active now</span>
                    </div>
                </div>
            </div>
            
            <div class="map-overlay-bottom">
                Tracking Area: Lagos, NG
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
