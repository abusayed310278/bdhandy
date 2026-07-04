@extends('layouts.dashboard')
@section('title', 'Live Team Map')

@section('content')
<div class="space-y-4 text-sm">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Live Team Location</h2>
      <p class="text-slate-500 text-xs mt-0.5">Real-time positions of active field technicians</p>
    </div>
    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-green-50 border border-green-100 text-green-700 text-xs font-semibold">
      <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
      Live
    </span>
  </div>

  <div class="grid lg:grid-cols-4 gap-4 h-[70vh]">
    {{-- Map --}}
    <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 overflow-hidden relative">
      <div id="live-map" class="w-full h-full"></div>
      <p class="absolute bottom-3 right-3 text-[11px] text-slate-400 bg-white/80 px-2 py-1 rounded-lg backdrop-blur-sm">Updates every 45s</p>
    </div>

    {{-- Sidebar member list --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-y-auto">
      <div class="px-4 py-3 border-b border-slate-100 sticky top-0 bg-white z-10">
        <p class="font-semibold text-slate-900 text-xs uppercase tracking-wider">{{ $members->count() }} Active Members</p>
      </div>
      <div class="divide-y divide-slate-50">
        @forelse($members as $member)
        <div class="px-4 py-3 cursor-pointer hover:bg-slate-50 transition member-card" data-id="{{ $member['id'] }}">
          <div class="flex items-center gap-2.5">
            @if($member['photo'])
              <img src="{{ $member['photo'] }}" class="w-8 h-8 rounded-full object-cover">
            @else
              <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-xs">
                {{ strtoupper(substr($member['name'], 0, 2)) }}
              </div>
            @endif
            <div>
              <p class="font-semibold text-slate-900 text-xs">{{ $member['name'] }}</p>
              <p class="text-[11px] text-slate-400">{{ $member['code'] }}</p>
            </div>
            <div class="ms-auto">
              @if($member['clocked_in'])
                <span class="w-2 h-2 rounded-full bg-green-500 block"></span>
              @else
                <span class="w-2 h-2 rounded-full bg-slate-300 block"></span>
              @endif
            </div>
          </div>
          @if($member['location'])
          <p class="text-[11px] text-slate-400 mt-1 ps-10">
            {{ $member['location']['is_moving'] ? '🟢 Moving' : '🔴 Stationary' }}
            @if($member['location']['speed']) · {{ $member['location']['speed'] }} km/h @endif
          </p>
          @else
          <p class="text-[11px] text-slate-300 mt-1 ps-10 italic">No location data</p>
          @endif
        </div>
        @empty
        <div class="px-4 py-8 text-center text-slate-400 italic text-xs">No active members</div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const members = @json($members);
const map = L.map('live-map').setView([23.8103, 90.4125], 11);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

const markers = {};
members.forEach(m => {
    if (!m.location) return;
    const marker = L.circleMarker([m.location.lat, m.location.lng], {
        radius: 8, fillColor: m.clocked_in ? '#22c55e' : '#94a3b8',
        color: '#fff', weight: 2, fillOpacity: 0.9
    }).bindPopup(`<strong>${m.name}</strong><br>${m.code}`).addTo(map);
    markers[m.id] = marker;
});

// Center map on first located member
const located = members.filter(m => m.location);
if (located.length) map.setView([located[0].location.lat, located[0].location.lng], 13);

// Click member card → pan to marker
document.querySelectorAll('.member-card').forEach(card => {
    card.addEventListener('click', () => {
        const id = parseInt(card.dataset.id);
        if (markers[id]) { markers[id].openPopup(); map.setView(markers[id].getLatLng(), 15); }
    });
});

// Refresh every 45 seconds
setTimeout(() => location.reload(), 45000);
</script>
@endsection
