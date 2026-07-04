<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth/onboarding/provider-service-area.service_area') }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:{50:'#F0F8FF',100:'#E0F1FE',200:'#BAE0FD',400:'#38ADF7',500:'#0F94EA',600:'#0277C7',700:'#0561A1'},accent:{50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',500:'#F97316',600:'#EA580C',700:'#C2410C'}}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>html,body{font-family:'Inter',system-ui,sans-serif;}[dir="rtl"] .rtl-flip{transform:scaleX(-1);}</style>
</head>
<body class="min-h-full text-slate-700 antialiased">
<div class="min-h-screen flex flex-col">

    <!-- Top bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-3xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 shrink-0" dir="ltr">
                <span class="w-8 h-8 rounded-lg bg-primary-500 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </span>
                <span class="font-bold text-slate-900">{{ config('app.name') }}</span>
            </a>
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <span class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-xs">✓</span>
                <span class="text-slate-400 hidden sm:inline">{{ __('auth/onboarding/provider-service-area.profile') }}</span>
                <div class="w-6 border-t border-slate-300 hidden sm:block"></div>
                <span class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-xs">✓</span>
                <span class="text-slate-400 hidden sm:inline">{{ __('auth/onboarding/provider-service-area.services') }}</span>
                <div class="w-6 border-t border-slate-300 hidden sm:block"></div>
                <span class="w-6 h-6 rounded-full bg-primary-500 text-white flex items-center justify-center font-bold text-xs">3</span>
                <span class="font-semibold text-primary-600 hidden sm:inline">{{ __('auth/onboarding/provider-service-area.area') }}</span>
                <div class="w-6 border-t border-slate-300 hidden sm:block"></div>
                <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs hidden sm:flex">4</span>
                <span class="hidden sm:inline">{{ __('auth/onboarding/provider-service-area.documents') }}</span>
            </div>
        </div>
    </header>

    <main class="flex-1 py-8 px-4">
        <div class="max-w-3xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900">{{ __('auth/onboarding/provider-service-area.title') }}</h1>
                <p class="text-slate-500 text-sm mt-1">{{ __('auth/onboarding/provider-service-area.subtitle') }}</p>
            </div>

            @if($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    <ul class="space-y-1 list-disc list-inside">
                        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('provider.onboarding.service-area.store') }}" class="space-y-5">
                @csrf

                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2">

                        {{-- Form fields --}}
                        <div class="p-5 space-y-4 border-b lg:border-b-0 lg:border-r border-slate-200">

                            <!-- Country -->
                            @if($singleCountry)
                                <input type="hidden" name="country_id" id="country_id" value="{{ $singleCountry->id }}">
                            @else
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-service-area.country') }} <span class="text-red-500">*</span></label>
                                <select name="country_id" id="country_id"
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm" required>
                                    <option value="">{{ __('auth/onboarding/provider-service-area.select_country') }}</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ old('country_id', $area?->country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Division -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-service-area.division') }} <span class="text-red-500">*</span></label>
                                <select name="division_id" id="division_id"
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm" required>
                                    <option value="">{{ __('auth/onboarding/provider-service-area.select_division') }}</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" {{ old('division_id', $area?->division_id) == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- District -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-service-area.district') }} <span class="text-red-500">*</span></label>
                                <select name="district_id" id="district_id"
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm" required>
                                    <option value="">{{ __('auth/onboarding/provider-service-area.select_district') }}</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" {{ old('district_id', $area?->district_id) == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Area -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-service-area.area') }} <span class="text-red-500">*</span></label>
                                <select name="area_id" id="area_id"
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm" required>
                                    <option value="">{{ __('auth/onboarding/provider-service-area.select_area') }}</option>
                                    @foreach($areas as $a)
                                        <option value="{{ $a->id }}" data-lat="{{ $a->latitude }}" data-lng="{{ $a->longitude }}"
                                            {{ old('area_id', $area?->area_id) == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Address -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('auth/onboarding/provider-service-area.address_landmark') }}</label>
                                <input type="text" name="address" id="address"
                                    value="{{ old('address', $area?->address) }}"
                                    placeholder="{{ __('auth/onboarding/provider-service-area.address_placeholder') }}"
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                            </div>

                            <!-- Radius -->
                            <div x-data="{ radius: {{ old('radius_km', $area?->radius_km ?? 5) }} }">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    {{ __('auth/onboarding/provider-service-area.service_radius') }} <span class="text-primary-600 font-bold" x-text="radius + ' km'"></span>
                                </label>
                                <input type="range" name="radius_km" id="radius_km"
                                    min="1" max="50" step="0.5"
                                    x-model="radius"
                                    @input="updateRadius(parseFloat($event.target.value))"
                                    class="w-full h-2 bg-slate-200 rounded-full appearance-none cursor-pointer accent-primary-500">
                                <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                                    <span>1 km</span><span>25 km</span><span>50 km</span>
                                </div>
                            </div>

                            <!-- Hidden lat/lng -->
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $area?->latitude ?? '23.8103') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $area?->longitude ?? '90.4125') }}">

                            <!-- Coordinates display -->
                            <div class="flex items-center gap-2 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>
                                <span class="text-[11px] text-slate-500 font-medium">{{ __('auth/onboarding/provider-service-area.lat') }}</span>
                                <span id="display_lat" class="text-[11px] text-slate-700 font-mono">{{ number_format(old('latitude', $area?->latitude ?? 23.8103), 6) }}</span>
                                <span class="text-slate-300 text-[11px]">|</span>
                                <span class="text-[11px] text-slate-500 font-medium">{{ __('auth/onboarding/provider-service-area.lng') }}</span>
                                <span id="display_lng" class="text-[11px] text-slate-700 font-mono">{{ number_format(old('longitude', $area?->longitude ?? 90.4125), 6) }}</span>
                            </div>
                        </div>

                        {{-- Map --}}
                        <div class="p-4 flex flex-col gap-3">
                            <div id="map" class="w-full h-[360px] rounded-xl border border-slate-200 bg-slate-100"></div>
                            <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ __('auth/onboarding/provider-service-area.map_instruction') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('provider.onboarding.services') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        {{ __('auth/onboarding/provider-service-area.back') }}
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 active:bg-primary-700 transition">
                        {{ __('auth/onboarding/provider-service-area.save_continue') }}
                        <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsdLSxCzJS1DypOOyGan4BWTZvZIhiS9M&libraries=places"></script>
<script>
    let map, marker, radiusCircle;

    function initMap() {
        const lat = parseFloat(document.getElementById('latitude').value) || 23.8103;
        const lng = parseFloat(document.getElementById('longitude').value) || 90.4125;
        const radius = parseFloat(document.getElementById('radius_km').value) || 5;

        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat, lng },
            zoom: 12,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
            styles: [{ featureType: 'poi', stylers: [{ visibility: 'off' }] }]
        });

        marker = new google.maps.Marker({
            position: { lat, lng },
            map,
            draggable: true,
            animation: google.maps.Animation.DROP
        });

        radiusCircle = new google.maps.Circle({
            map,
            center: { lat, lng },
            radius: radius * 1000,
            strokeColor: '#0F94EA',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#0F94EA',
            fillOpacity: 0.12
        });

        marker.addListener('dragend', function () {
            const pos = marker.getPosition();
            updateCoords(pos.lat(), pos.lng());
            radiusCircle.setCenter(pos);
        });

        // Area dropdown → update pin
        document.getElementById('area_id').addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const lat = parseFloat(opt.dataset.lat);
            const lng = parseFloat(opt.dataset.lng);
            if (lat && lng) {
                const pos = new google.maps.LatLng(lat, lng);
                marker.setPosition(pos);
                radiusCircle.setCenter(pos);
                map.setCenter(pos);
                map.setZoom(14);
                updateCoords(lat, lng);
            }
        });

        // Address / Landmark Places Autocomplete
        const addressInput = document.getElementById('address');
        const autocomplete = new google.maps.places.Autocomplete(addressInput);
        autocomplete.bindTo('bounds', map);
        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();
            if (!place.geometry || !place.geometry.location) return;
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
            marker.setPosition(place.geometry.location);
            radiusCircle.setCenter(place.geometry.location);
            updateCoords(place.geometry.location.lat(), place.geometry.location.lng());
        });

        fitBoundsToCircle();
    }

    function updateCoords(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        document.getElementById('display_lat').textContent = lat.toFixed(6);
        document.getElementById('display_lng').textContent = lng.toFixed(6);
    }

    function updateRadius(km) {
        if (radiusCircle) {
            radiusCircle.setRadius(km * 1000);
            fitBoundsToCircle();
        }
    }

    function fitBoundsToCircle() {
        if (radiusCircle) {
            map.fitBounds(radiusCircle.getBounds());
        }
    }

    // Cascading dropdowns
    function cascadeSelect(sourceId, targetId, url, paramName) {
        document.getElementById(sourceId).addEventListener('change', function () {
            const val = this.value;
            const target = document.getElementById(targetId);
            target.innerHTML = '<option value="">Loading...</option>';
            if (!val) { target.innerHTML = '<option value="">Select ' + targetId.replace('_id','') + '</option>'; return; }
            fetch(url + '?' + paramName + '=' + val)
                .then(r => r.json())
                .then(data => {
                    target.innerHTML = '<option value="">Select ' + targetId.replace('_id','') + '</option>';
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.name;
                        if (item.latitude) opt.dataset.lat = item.latitude;
                        if (item.longitude) opt.dataset.lng = item.longitude;
                        target.appendChild(opt);
                    });
                });
        });
    }

    @unless($singleCountry)
    cascadeSelect('country_id', 'division_id', "{{ route('geo.divisions') }}", 'country_id');
    @endunless
    cascadeSelect('division_id', 'district_id', "{{ route('geo.districts') }}", 'division_id');
    cascadeSelect('district_id', 'area_id', "{{ route('geo.areas') }}", 'district_id');

    google.maps.event.addDomListener(window, 'load', initMap);
</script>
</body>
</html>
