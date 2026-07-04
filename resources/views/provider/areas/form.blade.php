@extends('layouts.dashboard')
@section('title', isset($area) ? 'Edit Service Area' : 'Add Service Area')

@section('content')
<div class="max-w-3xl space-y-5 text-sm">

  <div>
    <a href="{{ route('provider.areas.index') }}" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1 mb-1">
      <svg class="w-3.5 h-3.5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      Service Areas
    </a>
    <h2 class="text-xl font-bold text-slate-900">{{ isset($area) ? 'Edit Service Area' : 'Add Service Area' }}</h2>
    <p class="text-slate-500 text-xs mt-0.5">Set your base location and the radius you cover.</p>
  </div>

  @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
      <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form action="{{ isset($area) ? route('provider.areas.update', $area) : route('provider.areas.store') }}" method="POST">
    @csrf
    @if(isset($area)) @method('PUT') @endif

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div class="grid grid-cols-1 lg:grid-cols-2">

        {{-- Form --}}
        <div class="p-5 space-y-4 border-b lg:border-b-0 lg:border-r border-slate-200">

          @if($singleCountry)
            <input type="hidden" name="country_id" id="country_id" value="{{ $singleCountry->id }}">
          @else
          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1.5">Country <span class="text-red-500">*</span></label>
            <select name="country_id" id="country_id" required
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
              <option value="">Select country</option>
              @foreach($countries as $c)
                <option value="{{ $c->id }}" {{ old('country_id', $area?->country_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
          @endif

          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1.5">Division <span class="text-red-500">*</span></label>
            <select name="division_id" id="division_id" required
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
              <option value="">Select division</option>
              @foreach($divisions as $d)
                <option value="{{ $d->id }}" {{ old('division_id', $area?->division_id) == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1.5">District <span class="text-red-500">*</span></label>
            <select name="district_id" id="district_id" required
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
              <option value="">Select district</option>
              @foreach($districts as $d)
                <option value="{{ $d->id }}" {{ old('district_id', $area?->district_id) == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1.5">Area</label>
            <select name="area_id" id="area_id"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
              <option value="">Select area (optional)</option>
              @foreach($areas as $a)
                <option value="{{ $a->id }}" data-lat="{{ $a->latitude }}" data-lng="{{ $a->longitude }}"
                  {{ old('area_id', $area?->area_id) == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1.5">Address / Landmark</label>
            <input type="text" name="address" id="address"
              value="{{ old('address', $area?->address) }}"
              placeholder="e.g. House 12, Road 5, Sector 3"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          </div>

          <div x-data="{ radius: {{ old('radius_km', $area?->radius_km ?? 5) }} }">
            <label class="block text-xs font-medium text-slate-700 mb-1.5">
              Service radius: <span class="text-primary-600 font-bold" x-text="radius + ' km'"></span>
            </label>
            <input type="range" name="radius_km" id="radius_km"
              min="1" max="100" step="0.5"
              x-model="radius"
              @input="updateRadius(parseFloat($event.target.value))"
              class="w-full h-2 bg-slate-200 rounded-full appearance-none cursor-pointer accent-primary-500">
            <div class="flex justify-between text-[10px] text-slate-400 mt-1">
              <span>1 km</span><span>50 km</span><span>100 km</span>
            </div>
          </div>

          <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $area?->latitude ?? '23.8103') }}">
          <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $area?->longitude ?? '90.4125') }}">

          <div class="flex items-center gap-2 rounded-xl bg-slate-50 border border-slate-200 px-3 py-2">
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>
            <span class="text-[11px] text-slate-500 font-medium">Lat:</span>
            <span id="display_lat" class="text-[11px] text-slate-700 font-mono">{{ number_format(old('latitude', $area?->latitude ?? 23.8103), 6) }}</span>
            <span class="text-slate-300 text-[11px]">|</span>
            <span class="text-[11px] text-slate-500 font-medium">Lng:</span>
            <span id="display_lng" class="text-[11px] text-slate-700 font-mono">{{ number_format(old('longitude', $area?->longitude ?? 90.4125), 6) }}</span>
          </div>
        </div>

        {{-- Map --}}
        <div class="p-4 flex flex-col gap-3">
          <div id="map" class="w-full h-[360px] rounded-xl border border-slate-200 bg-slate-100"></div>
          <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Drag the marker or select an area to update the pin. The circle shows your coverage.
          </p>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-4">
      <a href="{{ route('provider.areas.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Cancel</a>
      <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition">
        {{ isset($area) ? 'Update Area' : 'Save Area' }}
      </button>
    </div>
  </form>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsdLSxCzJS1DypOOyGan4BWTZvZIhiS9M&libraries=places" async defer></script>
<script>
let map, marker, radiusCircle;

function initMap() {
    const lat = parseFloat(document.getElementById('latitude').value) || 23.8103;
    const lng = parseFloat(document.getElementById('longitude').value) || 90.4125;
    const radius = parseFloat(document.getElementById('radius_km').value) || 5;

    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat, lng }, zoom: 12,
        mapTypeControl: false, streetViewControl: false, fullscreenControl: false,
        styles: [{ featureType: 'poi', stylers: [{ visibility: 'off' }] }]
    });

    marker = new google.maps.Marker({
        position: { lat, lng }, map, draggable: true,
        animation: google.maps.Animation.DROP
    });

    radiusCircle = new google.maps.Circle({
        map, center: { lat, lng }, radius: radius * 1000,
        strokeColor: '#0F94EA', strokeOpacity: 0.8, strokeWeight: 2,
        fillColor: '#0F94EA', fillOpacity: 0.12
    });

    marker.addListener('dragend', function () {
        const pos = marker.getPosition();
        updateCoords(pos.lat(), pos.lng());
        radiusCircle.setCenter(pos);
    });

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
    if (radiusCircle) { radiusCircle.setRadius(km * 1000); fitBoundsToCircle(); }
}

function fitBoundsToCircle() {
    if (radiusCircle) map.fitBounds(radiusCircle.getBounds());
}

@unless($singleCountry)
cascadeSelect('country_id', 'division_id', "{{ route('geo.divisions') }}", 'country_id');
@endunless
cascadeSelect('division_id', 'district_id', "{{ route('geo.districts') }}", 'division_id');
cascadeSelect('district_id', 'area_id', "{{ route('geo.areas') }}", 'district_id');

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

window.addEventListener('load', initMap);
</script>
@endsection
