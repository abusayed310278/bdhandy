@extends('layouts.onboarding')
@section('title', 'Add Your Address')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-primary-50 via-white to-accent-50 py-12 px-4">
  <div class="w-full max-w-2xl mx-auto">

    {{-- Progress --}}
    <div class="mb-8 text-center">
      <div class="inline-flex items-center gap-3 mb-4">
        <span class="w-8 h-8 rounded-full bg-primary-200 text-primary-700 text-sm font-bold flex items-center justify-center">✓</span>
        <div class="w-12 h-0.5 bg-primary-400"></div>
        <span class="w-8 h-8 rounded-full bg-primary-500 text-white text-sm font-bold flex items-center justify-center">2</span>
      </div>
      <h1 class="text-2xl font-bold text-slate-900">Add your address</h1>
      <p class="text-slate-500 mt-1">Used to find providers near you</p>
    </div>

    @if($errors->any())
      <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
        <p class="font-semibold mb-1">Please fix the following:</p>
        <ul class="list-disc list-inside space-y-0.5">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('customer.onboarding.address.store') }}"
          x-data="addressManager()">
      @csrf

      {{-- Address blocks --}}
      <template x-for="(addr, idx) in addresses" :key="addr.uid">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-4">

          {{-- Block header --}}
          <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide"
                x-text="'Address ' + (idx + 1)"></h3>
            <button type="button" x-show="addresses.length > 1" @click="removeAddress(addr.uid)"
                    class="inline-flex items-center gap-1 text-xs text-red-500 hover:text-red-700 font-medium transition">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
              Remove
            </button>
          </div>

          {{-- Label + Type --}}
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Label <span class="text-red-500">*</span>
              </label>
              <input type="text" :name="`addresses[${idx}][label]`" x-model="addr.label"
                     placeholder="e.g. Home, Office" required
                     class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Type <span class="text-red-500">*</span>
              </label>
              <select :name="`addresses[${idx}][address_type]`" x-model="addr.address_type" required
                      class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                <option value="house">🏠 House</option>
                <option value="office">🏢 Office</option>
                <option value="business">🏪 Business</option>
                <option value="other">📍 Other</option>
              </select>
            </div>
          </div>

          {{-- Country --}}
          @if($autoCountry)
            <input type="hidden" :name="`addresses[${idx}][country_id]`" value="{{ $autoCountry->id }}">
          @else
            <div class="mb-4">
              <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Country <span class="text-red-500">*</span>
              </label>
              <select :name="`addresses[${idx}][country_id]`" x-model="addr.country_id"
                      @change="loadDivisions(addr)" required
                      class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
                <option value="">Select country…</option>
                @foreach($countries as $country)
                  <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
              </select>
            </div>
          @endif

          {{-- Division / District / Area --}}
          <div class="grid grid-cols-3 gap-3 mb-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Division <span class="text-red-500">*</span>
              </label>
              <select :name="`addresses[${idx}][division_id]`" x-model="addr.division_id"
                      @change="loadDistricts(addr)" required
                      :disabled="!addr.divisions.length"
                      class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm disabled:bg-slate-50 disabled:text-slate-400">
                <option value="">Division…</option>
                <template x-for="div in addr.divisions" :key="div.id">
                  <option :value="div.id" x-text="div.name"></option>
                </template>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">
                District <span class="text-red-500">*</span>
              </label>
              <select :name="`addresses[${idx}][district_id]`" x-model="addr.district_id"
                      @change="loadAreas(addr)" required
                      :disabled="!addr.districts.length"
                      class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm disabled:bg-slate-50 disabled:text-slate-400">
                <option value="">District…</option>
                <template x-for="dist in addr.districts" :key="dist.id">
                  <option :value="dist.id" x-text="dist.name"></option>
                </template>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Area <span class="text-red-500">*</span>
              </label>
              <select :name="`addresses[${idx}][area_id]`" x-model="addr.area_id"
                      @change="onAreaChange(addr)" required
                      :disabled="!addr.areas.length"
                      class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm disabled:bg-slate-50 disabled:text-slate-400">
                <option value="">Area…</option>
                <template x-for="area in addr.areas" :key="area.id">
                  <option :value="area.id" x-text="area.name"></option>
                </template>
              </select>
            </div>
          </div>

          {{-- Map --}}
          <div class="mb-4">
            <div :id="`map-${addr.uid}`"
                 class="w-full h-48 rounded-xl border border-slate-200 bg-slate-100 overflow-hidden"></div>
            <p class="mt-1.5 text-[11px] text-slate-400 flex items-center gap-1">
              <svg class="w-3 h-3 text-primary-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              Drag the marker to pin your exact location
            </p>
          </div>

          {{-- Street address (Places Autocomplete) --}}
          <div class="mb-5">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
              Street address <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   :id="`street-${addr.uid}`"
                   :name="`addresses[${idx}][street]`"
                   x-model="addr.street"
                   placeholder="Start typing your address…"
                   required autocomplete="off"
                   class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition text-sm">
          </div>

          {{-- Hidden lat/lng --}}
          <input type="hidden" :name="`addresses[${idx}][latitude]`"  x-model="addr.latitude">
          <input type="hidden" :name="`addresses[${idx}][longitude]`" x-model="addr.longitude">

          {{-- Is Primary toggle --}}
          <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <div>
              <p class="text-sm font-medium text-slate-700">Primary address</p>
              <p class="text-xs text-slate-400">Default for finding nearby services</p>
            </div>
            <button type="button" @click="setPrimary(addr.uid)"
                    :class="addr.is_primary ? 'bg-primary-500' : 'bg-slate-200'"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                    :aria-checked="addr.is_primary" role="switch">
              <span :class="addr.is_primary ? 'translate-x-5' : 'translate-x-0'"
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
            </button>
            <input type="hidden" :name="`addresses[${idx}][is_primary]`" :value="addr.is_primary ? 1 : 0">
          </div>

        </div>
      </template>

      {{-- Add another address --}}
      <button type="button" @click="addAddress()"
              class="w-full flex items-center justify-center gap-2 px-5 py-3 rounded-xl border-2 border-dashed border-slate-300 text-slate-500 font-medium text-sm hover:border-primary-400 hover:text-primary-600 transition mb-6">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        Add another address
      </button>

      {{-- Actions --}}
      <button type="submit"
              class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-primary-500 text-white font-semibold hover:bg-primary-600 active:bg-primary-700 transition text-sm">
        Finish Setup
        <svg class="w-4 h-4 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
      </button>

    </form>
  </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsdLSxCzJS1DypOOyGan4BWTZvZIhiS9M&libraries=places"></script>
<script>
function addressManager() {
    const DEFAULT_LAT = 23.8103;
    const DEFAULT_LNG = 90.4125;
    const AUTO_COUNTRY_ID = '{{ $autoCountry?->id ?? '' }}';
    const INITIAL_DIVISIONS = {!! $divisions->values()->toJson() !!};

    function blankAddress(uid, isPrimary) {
        return {
            uid,
            label: isPrimary ? 'Home' : '',
            address_type: 'house',
            country_id: AUTO_COUNTRY_ID,
            division_id: '',
            district_id: '',
            area_id: '',
            street: '',
            latitude: '',
            longitude: '',
            is_primary: isPrimary,
            divisions: JSON.parse(JSON.stringify(INITIAL_DIVISIONS)),
            districts: [],
            areas: [],
        };
    }

    return {
        addresses: [blankAddress(1, true)],
        maps: {},
        markers: {},
        nextUid: 2,

        init() {
            this.$nextTick(() => {
                this.addresses.forEach(addr => this.initMap(addr.uid));
            });
        },

        addAddress() {
            const uid = this.nextUid++;
            this.addresses.push(blankAddress(uid, false));
            this.$nextTick(() => this.initMap(uid));
        },

        removeAddress(uid) {
            if (this.addresses.length <= 1) return;
            const wasPrimary = this.addresses.find(a => a.uid === uid)?.is_primary;
            this.addresses = this.addresses.filter(a => a.uid !== uid);
            if (wasPrimary && this.addresses.length) {
                this.addresses[0].is_primary = true;
            }
        },

        setPrimary(uid) {
            this.addresses.forEach(a => { a.is_primary = (a.uid === uid); });
        },

        async loadDivisions(addr) {
            if (!addr.country_id) { addr.divisions = []; return; }
            const res = await fetch(`/geo/divisions?country_id=${addr.country_id}`);
            addr.divisions  = await res.json();
            addr.division_id = '';
            addr.districts   = [];
            addr.district_id = '';
            addr.areas       = [];
            addr.area_id     = '';
        },

        async loadDistricts(addr) {
            if (!addr.division_id) { addr.districts = []; return; }
            const res = await fetch(`/geo/districts?division_id=${addr.division_id}`);
            addr.districts   = await res.json();
            addr.district_id = '';
            addr.areas       = [];
            addr.area_id     = '';
        },

        async loadAreas(addr) {
            if (!addr.district_id) { addr.areas = []; return; }
            const res = await fetch(`/geo/areas?district_id=${addr.district_id}`);
            addr.areas   = await res.json();
            addr.area_id = '';
        },

        onAreaChange(addr) {
            if (!addr.area_id) return;
            const area = addr.areas.find(a => String(a.id) === String(addr.area_id));
            if (!area || !area.latitude || !area.longitude) return;
            const lat = parseFloat(area.latitude);
            const lng = parseFloat(area.longitude);
            const map    = this.maps[addr.uid];
            const marker = this.markers[addr.uid];
            if (map)    { map.setCenter({ lat, lng }); map.setZoom(14); }
            if (marker) { marker.setPosition({ lat, lng }); }
            addr.latitude  = lat.toFixed(8);
            addr.longitude = lng.toFixed(8);
        },

        initMap(uid) {
            const self = this;
            const el   = document.getElementById(`map-${uid}`);
            if (!el) return;

            const map = new google.maps.Map(el, {
                center: { lat: DEFAULT_LAT, lng: DEFAULT_LNG },
                zoom: 12,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                styles: [{ featureType: 'poi', stylers: [{ visibility: 'off' }] }],
            });

            const marker = new google.maps.Marker({
                position: { lat: DEFAULT_LAT, lng: DEFAULT_LNG },
                map,
                draggable: true,
                animation: google.maps.Animation.DROP,
            });

            self.maps[uid]    = map;
            self.markers[uid] = marker;

            marker.addListener('dragend', function () {
                const pos  = marker.getPosition();
                const addr = self.addresses.find(a => a.uid === uid);
                if (addr) {
                    addr.latitude  = pos.lat().toFixed(8);
                    addr.longitude = pos.lng().toFixed(8);
                }
            });

            // Attach Places Autocomplete once the input exists
            const streetInput = document.getElementById(`street-${uid}`);
            if (!streetInput) return;

            const autocomplete = new google.maps.places.Autocomplete(streetInput);
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

                const addr = self.addresses.find(a => a.uid === uid);
                if (addr) {
                    addr.latitude  = place.geometry.location.lat().toFixed(8);
                    addr.longitude = place.geometry.location.lng().toFixed(8);
                    addr.street    = place.formatted_address || streetInput.value;
                }
            });
        },
    };
}
</script>
@endsection
