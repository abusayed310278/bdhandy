@extends('layouts.dashboard')
@section('title', $address ? 'Edit Address' : 'Add Address')

@section('content')
<div class="max-w-2xl space-y-5 text-sm">
  <div>
    <a href="{{ route('customer.addresses.index') }}" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1 mb-1">
      <svg class="w-3.5 h-3.5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      My Addresses
    </a>
    <h2 class="text-xl font-bold text-slate-900">{{ $address ? 'Edit Address' : 'Add New Address' }}</h2>
  </div>

  @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
      <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form action="{{ $address ? route('customer.addresses.update', $address) : route('customer.addresses.store') }}"
        method="POST" class="space-y-5">
    @csrf
    @if($address) @method('PUT') @endif

    {{-- Label & Type --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
      <h3 class="font-bold text-slate-800">Address Info</h3>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Label <span class="text-red-500">*</span></label>
          <input type="text" name="label" value="{{ old('label', $address?->label) }}" required
            placeholder="e.g. Home, My Office"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Type <span class="text-red-500">*</span></label>
          <select name="address_type" required
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
            @foreach(['house'=>'House / Home','office'=>'Office','business'=>'Business'] as $t=>$tl)
              <option value="{{ $t }}" {{ old('address_type', $address?->address_type) === $t ? 'selected' : '' }}>{{ $tl }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Full Address <span class="text-red-500">*</span></label>
        <input type="text" id="address_input" name="address" value="{{ old('address', $address?->address) }}" required
          placeholder="Search for your address or type it"
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
      </div>

      <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $address?->latitude) }}">
      <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $address?->longitude) }}">

      {{-- Coord display --}}
      @if($address?->latitude)
      <div class="flex items-center gap-2 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2">
        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>
        <span class="text-[11px] text-slate-500 font-medium">Lat:</span>
        <span id="display_lat" class="text-[11px] text-slate-700 font-mono">{{ number_format($address->latitude, 6) }}</span>
        <span class="text-slate-300 text-[11px]">|</span>
        <span class="text-[11px] text-slate-500 font-medium">Lng:</span>
        <span id="display_lng" class="text-[11px] text-slate-700 font-mono">{{ number_format($address->longitude, 6) }}</span>
      </div>
      @else
      <div id="coord_pill" class="hidden flex items-center gap-2 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2">
        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>
        <span class="text-[11px] text-slate-500 font-medium">Lat:</span>
        <span id="display_lat" class="text-[11px] text-slate-700 font-mono"></span>
        <span class="text-slate-300 text-[11px]">|</span>
        <span class="text-[11px] text-slate-500 font-medium">Lng:</span>
        <span id="display_lng" class="text-[11px] text-slate-700 font-mono"></span>
      </div>
      @endif

      <div id="addr_map" class="w-full h-52 rounded-xl overflow-hidden border border-slate-200"></div>
      <p class="text-[11px] text-slate-400">Search address above or drag the pin to set exact coordinates (optional)</p>
    </div>

    {{-- Geo selects --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
      <h3 class="font-bold text-slate-800">Region</h3>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Country <span class="text-red-500">*</span></label>
          <select name="country_id" id="country_id" required
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
            <option value="">Select country</option>
            @foreach($countries as $country)
              <option value="{{ $country->id }}" {{ old('country_id', $address?->country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Division <span class="text-red-500">*</span></label>
          <select name="division_id" id="division_id" required
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
            <option value="">Select division</option>
            @foreach($divisions as $div)
              <option value="{{ $div->id }}" {{ old('division_id', $address?->division_id) == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">District <span class="text-red-500">*</span></label>
          <select name="district_id" id="district_id" required
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
            <option value="">Select district</option>
            @foreach($districts as $dist)
              <option value="{{ $dist->id }}" {{ old('district_id', $address?->district_id) == $dist->id ? 'selected' : '' }}>{{ $dist->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Area / Upazila</label>
          <select name="area_id" id="area_id"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
            <option value="">Select area</option>
            @foreach($areas as $area)
              <option value="{{ $area->id }}" {{ old('area_id', $address?->area_id) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 pb-4">
      <a href="{{ route('customer.addresses.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Cancel</a>
      <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition">
        {{ $address ? 'Save Changes' : 'Add Address' }}
      </button>
    </div>
  </form>
</div>

@push('scripts')
<script>
  function cascadeSelect(sourceId, targetId, url, paramKey) {
    const source = document.getElementById(sourceId);
    const target = document.getElementById(targetId);
    if (!source || !target) return;
    source.addEventListener('change', function () {
      const val = this.value;
      target.innerHTML = '<option value="">Loading…</option>';
      if (!val) { target.innerHTML = '<option value="">Select option</option>'; return; }
      fetch(url + '?' + paramKey + '=' + val)
        .then(r => r.json())
        .then(data => {
          target.innerHTML = '<option value="">Select option</option>';
          data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id; opt.textContent = item.name;
            target.appendChild(opt);
          });
        });
    });
  }

  cascadeSelect('country_id', 'division_id', "{{ route('geo.divisions') }}", 'country_id');
  cascadeSelect('division_id', 'district_id', "{{ route('geo.districts') }}", 'division_id');
  cascadeSelect('district_id', 'area_id', "{{ route('geo.areas') }}", 'district_id');

  let addrMap, addrMarker;

  function initAddrMap() {
    const initLat = {{ old('latitude', $address?->latitude ?? 23.8103) }};
    const initLng = {{ old('longitude', $address?->longitude ?? 90.4125) }};

    addrMap = new google.maps.Map(document.getElementById('addr_map'), {
      center: { lat: initLat, lng: initLng },
      zoom: 12,
      mapTypeControl: false, streetViewControl: false, fullscreenControl: false
    });

    addrMarker = new google.maps.Marker({
      position: { lat: initLat, lng: initLng },
      map: addrMap,
      draggable: true,
      title: 'Drag to set location'
    });

    addrMarker.addListener('dragend', function (e) {
      updateAddrCoords(e.latLng.lat(), e.latLng.lng());
    });

    const addressInput = document.getElementById('address_input');
    const autocomplete = new google.maps.places.Autocomplete(addressInput);
    autocomplete.bindTo('bounds', addrMap);
    autocomplete.addListener('place_changed', function () {
      const place = autocomplete.getPlace();
      if (!place.geometry || !place.geometry.location) return;
      if (place.geometry.viewport) { addrMap.fitBounds(place.geometry.viewport); }
      else { addrMap.setCenter(place.geometry.location); addrMap.setZoom(16); }
      addrMarker.setPosition(place.geometry.location);
      updateAddrCoords(place.geometry.location.lat(), place.geometry.location.lng());
    });
  }

  function updateAddrCoords(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(8);
    document.getElementById('longitude').value = lng.toFixed(8);
    document.getElementById('display_lat').textContent = lat.toFixed(6);
    document.getElementById('display_lng').textContent = lng.toFixed(6);
    const pill = document.getElementById('coord_pill');
    if (pill) pill.classList.remove('hidden');
  }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsdLSxCzJS1DypOOyGan4BWTZvZIhiS9M&libraries=places&callback=initAddrMap" async defer></script>
@endpush
@endsection
