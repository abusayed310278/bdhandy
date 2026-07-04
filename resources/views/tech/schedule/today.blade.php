@extends('layouts.dashboard')
@section('title', "Today's Schedule")

@section('content')
{{-- Negative margins cancel the dashboard's default padding so we own the full viewport height --}}
<div class="-m-4 sm:-m-6 lg:-m-8 flex" style="height:calc(100vh - 4rem); overflow:hidden">

  {{-- ── LEFT: Job List ──────────────────────────────────────────── --}}
  <div class="w-full lg:w-[420px] flex flex-col bg-white border-e border-slate-200 shrink-0 overflow-hidden">

    {{-- Panel header --}}
    <div class="px-4 py-3.5 border-b border-slate-100 bg-slate-50 shrink-0">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-sm font-bold text-slate-900">Today's Schedule</h2>
          <p class="text-[11px] text-slate-400 mt-0.5">{{ $day->format('l, d F Y') }}</p>
        </div>
        @if($schedule && $schedule->waypoints->isNotEmpty())
          <span class="px-2 py-0.5 rounded-full bg-primary-100 text-primary-700 text-[11px] font-bold">
            {{ $schedule->waypoints->count() }} job{{ $schedule->waypoints->count() !== 1 ? 's' : '' }}
          </span>
        @endif
      </div>
      <p class="mt-2 text-[11px] text-slate-400 flex items-center gap-1 lg:hidden">
        <svg class="w-3.5 h-3.5 text-primary-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        Map view available on desktop
      </p>
    </div>

    {{-- Scrollable job cards --}}
    <div class="flex-1 overflow-y-auto p-3 space-y-2.5">

      @if($schedule && $schedule->waypoints->isNotEmpty())
        @foreach($schedule->waypoints as $wp)
        @php
          $job = $wp->jobAssignment;
          $req = $job?->request;
          $sc  = match($job?->status) {
            'assigned'    => ['c'=>'slate',  'l'=>'Assigned'],
            'accepted'    => ['c'=>'blue',   'l'=>'Accepted'],
            'en_route'    => ['c'=>'yellow', 'l'=>'En Route'],
            'arrived'     => ['c'=>'cyan',   'l'=>'Arrived'],
            'in_progress' => ['c'=>'orange', 'l'=>'In Progress'],
            'paused'      => ['c'=>'amber',  'l'=>'Paused'],
            'completed'   => ['c'=>'green',  'l'=>'Completed'],
            'rejected'    => ['c'=>'red',    'l'=>'Rejected'],
            'reassigned'  => ['c'=>'slate',  'l'=>'Reassigned'],
            default       => ['c'=>'slate',  'l'=>$job?->status ?? ''],
          };
          $isActive  = in_array($job?->status, ['in_progress', 'arrived']);
          $hasCoords = $req?->latitude && $req?->longitude;
        @endphp

        <div
          id="job-card-{{ $wp->sequence_order }}"
          data-seq="{{ $wp->sequence_order }}"
          onclick="focusMarker({{ $wp->sequence_order }})"
          class="job-card rounded-xl border {{ $isActive ? 'border-primary-300 ring-2 ring-primary-100' : 'border-slate-200' }} p-3.5 space-y-2.5 cursor-pointer transition hover:border-primary-300 hover:shadow-sm bg-white"
        >
          {{-- Top row: sequence + info + status --}}
          <div class="flex items-start gap-2.5">
            <span class="w-7 h-7 rounded-lg bg-primary-50 border border-primary-100 flex items-center justify-center text-primary-700 font-black text-xs shrink-0">{{ $wp->sequence_order }}</span>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-1.5 flex-wrap">
                <p class="font-bold text-slate-900 text-sm">{{ $req?->request_number ?? '—' }}</p>
                @if($job)
                <a href="{{ route('tech.jobs.show', $job) }}" onclick="event.stopPropagation()"
                   class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 text-[10px] font-bold hover:bg-primary-50 hover:text-primary-600 transition shrink-0">
                  Details
                </a>
                @endif
              </div>
              @if($req?->address)
                <p class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $req->address }}</p>
              @endif
              @if($job?->scheduled_start_time)
                <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                  <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  {{ $job->scheduled_start_time->format('H:i') }}
                  @if($wp->estimated_travel_time_from_previous_minutes)
                    · ~{{ $wp->estimated_travel_time_from_previous_minutes }} min travel
                  @endif
                </p>
              @endif
            </div>
            <span class="px-1.5 py-0.5 rounded-full bg-{{ $sc['c'] }}-100 text-{{ $sc['c'] }}-700 text-[10px] font-semibold shrink-0">{{ $sc['l'] }}</span>
          </div>

          {{-- Navigate button --}}
          @if($hasCoords)
          <a href="https://www.google.com/maps/dir/?api=1&destination={{ $req->latitude }},{{ $req->longitude }}"
             target="_blank"
             rel="noopener"
             onclick="event.stopPropagation()"
             class="flex items-center justify-center gap-1.5 w-full py-1.5 rounded-lg bg-primary-50 border border-primary-200 text-primary-700 text-xs font-semibold hover:bg-primary-100 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
            Navigate to Job
          </a>
          @endif

          {{-- Action buttons --}}
          @if($job && !in_array($job->status, ['completed','rejected','reassigned']))
          @php
            $isAssigned    = $job->status === 'assigned';
            $scheduledDate = $job->scheduled_start_time?->startOfDay();
            $canOperate    = !$scheduledDate || today()->gte($scheduledDate);
          @endphp

          @if($isAssigned)
          <form action="{{ route('tech.jobs.update-status', $job) }}" method="POST" class="flex gap-1.5" onclick="event.stopPropagation()">
            @csrf
            <button name="status" value="accepted" class="flex-1 py-1.5 rounded-lg bg-green-500 text-white text-xs font-bold hover:bg-green-600 transition">Accept</button>
            <button name="status" value="rejected" class="flex-1 py-1.5 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs font-bold hover:bg-red-100 transition">Reject</button>
          </form>

          @elseif(!$canOperate)
          <div class="flex items-center gap-1.5 rounded-lg bg-amber-50 border border-amber-200 px-3 py-2">
            <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p class="text-xs font-semibold text-amber-800">Available from {{ $scheduledDate->format('d F Y') }}</p>
          </div>

          @else
          <form action="{{ route('tech.jobs.update-status', $job) }}" method="POST" class="flex flex-wrap gap-1.5" onclick="event.stopPropagation()">
            @csrf
            @foreach([
              'en_route'    => ['En Route',   'bg-slate-100 text-slate-700 hover:bg-primary-50 hover:text-primary-600'],
              'arrived'     => ['Arrived',    'bg-slate-100 text-slate-700 hover:bg-primary-50 hover:text-primary-600'],
              'in_progress' => ['Start Work', 'bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100'],
              'paused'      => ['Pause',      'bg-amber-50 border border-amber-200 text-amber-700 hover:bg-amber-100'],
              'completed'   => ['Complete',   'bg-green-500 text-white hover:bg-green-600'],
            ] as $s => [$label, $cls])
              @if($job->status !== $s)
              <button name="status" value="{{ $s }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition {{ $cls }}">{{ $label }}</button>
              @endif
            @endforeach
          </form>
          @endif
          @endif

        </div>
        @endforeach

      @elseif($schedule)
      <div class="flex flex-col items-center justify-center h-full py-16 text-center text-slate-400">
        <svg class="w-10 h-10 mb-3 text-slate-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
        <p class="text-sm italic">Schedule published but no jobs assigned yet.</p>
      </div>
      @else
      <div class="flex flex-col items-center justify-center h-full py-16 text-center text-slate-400">
        <svg class="w-10 h-10 mb-3 text-slate-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <p class="text-sm italic">No published schedule for today.</p>
        <p class="text-xs mt-1">Check back later.</p>
      </div>
      @endif

    </div>
  </div>

  {{-- ── RIGHT: Map ───────────────────────────────────────────────── --}}
  <div class="hidden lg:flex flex-1 flex-col relative">

    {{-- Loading state --}}
    <div id="map-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-100 z-10 gap-3">
      <svg class="w-8 h-8 animate-spin text-primary-400" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
      </svg>
      <p class="text-xs text-slate-400">Loading map…</p>
    </div>

    <div id="map" class="w-full h-full"></div>

    {{-- Center-on-me button --}}
    <button onclick="centerOnMe()" title="My location"
      class="absolute bottom-6 end-6 z-10 w-10 h-10 rounded-full bg-white shadow-lg border border-slate-200 flex items-center justify-center text-primary-600 hover:bg-primary-50 transition">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>
    </button>

    {{-- Legend --}}
    @if(count($mapPins))
    <div class="absolute top-4 start-4 z-10 bg-white/90 backdrop-blur-sm border border-slate-200 rounded-xl px-3 py-2 shadow-sm space-y-1">
      @foreach([
        ['color'=>'#64748b','label'=>'Assigned'],
        ['color'=>'#3b82f6','label'=>'Accepted'],
        ['color'=>'#eab308','label'=>'En Route'],
        ['color'=>'#06b6d4','label'=>'Arrived'],
        ['color'=>'#f97316','label'=>'In Progress'],
        ['color'=>'#22c55e','label'=>'Completed'],
      ] as $leg)
      @php $used = collect($mapPins)->contains('status', strtolower(str_replace(' ', '_', $leg['label']))); @endphp
      @if($used)
      <div class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded-full shrink-0" style="background:{{ $leg['color'] }}"></span>
        <span class="text-[10px] text-slate-600">{{ $leg['label'] }}</span>
      </div>
      @endif
      @endforeach
      <div class="flex items-center gap-1.5 border-t border-slate-100 pt-1 mt-1">
        <span class="w-3 h-3 rounded-full bg-primary-500 shrink-0"></span>
        <span class="text-[10px] text-slate-600">My location</span>
      </div>
    </div>
    @endif

  </div>

</div>

@push('scripts')
<script>
let map, meMarker, meAccuracyCircle;
const jobMarkers = {};
const MARKER_COLORS = {
  assigned   : '#64748b',
  accepted   : '#3b82f6',
  en_route   : '#eab308',
  arrived    : '#06b6d4',
  in_progress: '#f97316',
  paused     : '#f59e0b',
  completed  : '#22c55e',
  rejected   : '#ef4444',
  reassigned : '#94a3b8',
};

const MAP_PINS = @json($mapPins);
const LAST_KNOWN = @json($lastLocation ? ['lat' => (float)$lastLocation->latitude, 'lng' => (float)$lastLocation->longitude] : null);

function initMap() {
  const defaultCenter = { lat: 23.8103, lng: 90.4125 };
  const center = MAP_PINS.length > 0
    ? { lat: MAP_PINS[0].lat, lng: MAP_PINS[0].lng }
    : (LAST_KNOWN ? { lat: LAST_KNOWN.lat, lng: LAST_KNOWN.lng } : defaultCenter);

  map = new google.maps.Map(document.getElementById('map'), {
    center,
    zoom: 13,
    mapTypeControl: false,
    streetViewControl: false,
    fullscreenControl: false,
    zoomControl: true,
    zoomControlOptions: { position: google.maps.ControlPosition.RIGHT_BOTTOM },
    styles: [{ featureType: 'poi.business', stylers: [{ visibility: 'off' }] }],
  });

  const bounds = new google.maps.LatLngBounds();

  MAP_PINS.forEach(wp => {
    const pos   = { lat: wp.lat, lng: wp.lng };
    const color = MARKER_COLORS[wp.status] || '#64748b';
    const svg   = `<svg xmlns="http://www.w3.org/2000/svg" width="36" height="46" viewBox="0 0 36 46">
      <path d="M18 0C8.06 0 0 8.06 0 18c0 13.25 18 28 18 28S36 31.25 36 18C36 8.06 27.94 0 18 0z" fill="${color}"/>
      <circle cx="18" cy="18" r="11" fill="rgba(255,255,255,0.2)"/>
      <text x="18" y="23.5" text-anchor="middle" fill="white" font-family="Inter,system-ui,sans-serif" font-weight="800" font-size="13">${wp.seq}</text>
    </svg>`;

    const marker = new google.maps.Marker({
      position : pos,
      map,
      title    : `#${wp.seq}: ${wp.address}`,
      icon     : {
        url         : 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
        scaledSize  : new google.maps.Size(36, 46),
        anchor      : new google.maps.Point(18, 46),
      },
    });

    const infoWin = new google.maps.InfoWindow({
      content: `<div style="font-family:Inter,sans-serif;padding:4px;min-width:150px">
        <p style="font-weight:700;font-size:13px;margin:0 0 2px 0">${wp.number}</p>
        <p style="font-size:11px;color:#64748b;margin:0">${wp.address}</p>
      </div>`,
    });

    marker.addListener('click', () => {
      Object.values(jobMarkers).forEach(m => m.win.close());
      infoWin.open(map, marker);
      scrollToCard(wp.seq);
      pulseCard(wp.seq);
    });

    jobMarkers[wp.seq] = { marker, win: infoWin };
    bounds.extend(pos);
  });

  // Last known location (faint) before geolocation kicks in
  if (LAST_KNOWN) {
    placeMe(LAST_KNOWN.lat, LAST_KNOWN.lng, false);
    bounds.extend(new google.maps.LatLng(LAST_KNOWN.lat, LAST_KNOWN.lng));
  }

  if (!bounds.isEmpty()) {
    map.fitBounds(bounds, { top: 60, right: 40, bottom: 60, left: 40 });
    google.maps.event.addListenerOnce(map, 'idle', () => {
      if (map.getZoom() > 15) map.setZoom(15);
    });
  }

  // Live geolocation
  if (navigator.geolocation) {
    navigator.geolocation.watchPosition(
      pos => placeMe(pos.coords.latitude, pos.coords.longitude, true, pos.coords.accuracy),
      () => {},
      { enableHighAccuracy: true, maximumAge: 15000, timeout: 10000 }
    );
  }

  document.getElementById('map-loading').style.display = 'none';
}

function placeMe(lat, lng, live, accuracy) {
  const pos = { lat, lng };
  if (meMarker) {
    meMarker.setPosition(pos);
    if (live) meMarker.setIcon({
      path         : google.maps.SymbolPath.CIRCLE,
      scale        : 10,
      fillColor    : '#0F94EA',
      fillOpacity  : 1,
      strokeColor  : '#ffffff',
      strokeWeight : 3,
    });
  } else {
    meMarker = new google.maps.Marker({
      position : pos,
      map,
      title    : 'My Location',
      zIndex   : 200,
      icon     : {
        path         : google.maps.SymbolPath.CIRCLE,
        scale        : 10,
        fillColor    : '#0F94EA',
        fillOpacity  : live ? 1 : 0.35,
        strokeColor  : '#ffffff',
        strokeWeight : 3,
      },
    });
  }

  if (live && accuracy) {
    if (meAccuracyCircle) {
      meAccuracyCircle.setCenter(pos);
      meAccuracyCircle.setRadius(accuracy);
    } else {
      meAccuracyCircle = new google.maps.Circle({
        map,
        center       : pos,
        radius       : accuracy,
        strokeColor  : '#0F94EA',
        strokeOpacity: 0.25,
        strokeWeight : 1,
        fillColor    : '#0F94EA',
        fillOpacity  : 0.07,
      });
    }
  }
}

function centerOnMe() {
  if (meMarker) {
    map.panTo(meMarker.getPosition());
    map.setZoom(15);
    return;
  }
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(pos => {
      placeMe(pos.coords.latitude, pos.coords.longitude, true, pos.coords.accuracy);
      map.panTo({ lat: pos.coords.latitude, lng: pos.coords.longitude });
      map.setZoom(15);
    });
  }
}

function focusMarker(seq) {
  if (!jobMarkers[seq]) return;
  const { marker, win } = jobMarkers[seq];
  map.panTo(marker.getPosition());
  if (map.getZoom() < 15) map.setZoom(15);
  Object.values(jobMarkers).forEach(m => m.win.close());
  win.open(map, marker);
  pulseCard(seq);
}

function scrollToCard(seq) {
  const card = document.getElementById('job-card-' + seq);
  if (card) card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function pulseCard(seq) {
  document.querySelectorAll('.job-card').forEach(c => c.classList.remove('ring-2', 'ring-primary-400'));
  const card = document.getElementById('job-card-' + seq);
  if (card) {
    card.classList.add('ring-2', 'ring-primary-400');
    setTimeout(() => card.classList.remove('ring-2', 'ring-primary-400'), 2000);
  }
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsdLSxCzJS1DypOOyGan4BWTZvZIhiS9M&callback=initMap" async defer></script>
@endpush

@endsection
