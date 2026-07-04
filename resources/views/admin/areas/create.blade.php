@extends('layouts.dashboard')

@section('title', isset($area) ? 'Edit Area' : 'Add New Area')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ isset($area) ? 'Edit Area' : 'Create New Area' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Manage local service areas and neighborhoods with precise map location</p>
        </div>
        <a href="{{ route('admin.areas.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
        <form action="{{ isset($area) ? route('admin.areas.update', $area->id) : route('admin.areas.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf
            @if(isset($area)) @method('PUT') @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Form Fields -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">District <span class="text-red-500">*</span></label>
                        <select name="district_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                            <option value="">Select District</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ old('district_id', $area->district_id ?? '') == $district->id ? 'selected' : '' }}>{{ $district->name }} ({{ $district->division->name }})</option>
                            @endforeach
                        </select>
                        @error('district_id') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Area Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $area->name ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="Search area or type name..." required>
                        <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-wide">Type to see Google Map suggestions</p>
                        @error('name') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Slug <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $area->slug ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. gulshan" required>
                        @error('slug') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Latitude</label>
                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $area->latitude ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition font-mono text-xs" placeholder="Auto-filled" readonly>
                            @error('latitude') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Longitude</label>
                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $area->longitude ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition font-mono text-xs" placeholder="Auto-filled" readonly>
                            @error('longitude') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Map Picker -->
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-slate-700">Set Exact Location</label>
                    <div id="map" class="w-full h-[320px] lg:h-[400px] rounded-2xl border border-slate-200 shadow-inner bg-slate-50"></div>
                    <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Drag the marker to pin the exact service point.
                    </p>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="pt-8 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.areas.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-10 py-3 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">
                    {{ isset($area) ? 'Save Changes' : 'Create Area' }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsdLSxCzJS1DypOOyGan4BWTZvZIhiS9M&libraries=places"></script>
<script>
    function initMap() {
        const defaultLat = {{ $area->latitude ?? 23.8103 }};
        const defaultLng = {{ $area->longitude ?? 90.4125 }};
        
        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: defaultLat, lng: defaultLng },
            zoom: 13,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
            styles: [
                {
                    "featureType": "poi",
                    "stylers": [{ "visibility": "off" }]
                }
            ]
        });

        const marker = new google.maps.Marker({
            position: { lat: defaultLat, lng: defaultLng },
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP
        });

        // Autocomplete
        const input = document.getElementById("name");
        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo("bounds", map);

        autocomplete.addListener("place_changed", () => {
            const place = autocomplete.getPlace();

            if (!place.geometry || !place.geometry.location) {
                return;
            }

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            marker.setPosition(place.geometry.location);
            updateCoords(place.geometry.location.lat(), place.geometry.location.lng());
            
            // Auto slug
            updateSlug(place.name || input.value);
        });

        // Marker Drag
        google.maps.event.addListener(marker, 'dragend', function() {
            updateCoords(marker.getPosition().lat(), marker.getPosition().lng());
        });

        // Manual Slug Event
        input.addEventListener('input', function() {
            updateSlug(this.value);
        });

        function updateCoords(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
        }

        function updateSlug(text) {
            const slug = text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
            document.getElementById('slug').value = slug;
        }
    }

    google.maps.event.addDomListener(window, 'load', initMap);
</script>
@endpush
@endsection
