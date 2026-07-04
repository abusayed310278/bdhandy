@extends('layouts.dashboard')
@section('title', 'Requirement — ' . $requirement->title)

@section('content')
<div class="space-y-5 text-sm max-w-4xl">

  {{-- Header --}}
  <div class="flex items-start justify-between gap-3">
    <div>
      <a href="{{ route('customer.requirements.index') }}" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1 mb-1">
        <svg class="w-3.5 h-3.5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        My Requirements
      </a>
      <h2 class="text-xl font-bold text-slate-900">{{ $requirement->title }}</h2>
      <div class="flex items-center gap-2 flex-wrap mt-1">
        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider
          {{ $requirement->urgency === 'emergency' ? 'bg-red-50 text-red-700' : ($requirement->urgency === 'urgent' ? 'bg-yellow-50 text-yellow-700' : 'bg-slate-100 text-slate-600') }}">
          {{ $requirement->urgency }}
        </span>
        @php
          $sc = ['open'=>'primary','assigned'=>'green','completed'=>'green','expired'=>'slate','cancelled'=>'slate'][$requirement->status] ?? 'slate';
        @endphp
        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider bg-{{ $sc }}-50 text-{{ $sc }}-700">
          {{ $requirement->status }}
        </span>
      </div>
    </div>
    @if($requirement->status === 'open')
      <form action="{{ route('customer.requirements.cancel', $requirement) }}" method="POST" onsubmit="return confirm('Cancel this requirement?')">
        @csrf
        <button type="submit" class="px-4 py-2 rounded-xl border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 transition">
          Cancel
        </button>
      </form>
    @endif
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif

  <div class="grid lg:grid-cols-3 gap-5">
    {{-- Main --}}
    <div class="lg:col-span-2 space-y-5">

      {{-- Details --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h3 class="font-bold text-slate-800 mb-4">Requirement Details</h3>
        <dl class="grid grid-cols-2 gap-4">
          <div>
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Category</dt>
            <dd class="mt-1 text-slate-800">{{ $requirement->category?->getTranslation('translations','en') ?: $requirement->category?->slug ?? '—' }}</dd>
          </div>
          @if($requirement->service)
          <div>
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Service</dt>
            <dd class="mt-1 text-slate-800">{{ ($requirement->service->getTranslation('translations','en')['name'] ?? null) ?: $requirement->service->slug }}</dd>
          </div>
          @endif
          <div>
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Budget</dt>
            <dd class="mt-1 text-slate-800">
              @if($requirement->budget_type === 'negotiable')
                Negotiable
              @elseif($requirement->budget_type === 'range')
                {{ $requirement->currency?->symbol }}{{ number_format($requirement->budget_min, 0) }}
                – {{ $requirement->currency?->symbol }}{{ number_format($requirement->budget_max, 0) }}
              @else
                {{ $requirement->currency?->symbol }}{{ number_format($requirement->budget_fixed, 2) }}
              @endif
            </dd>
          </div>
          @if($requirement->preferred_date)
          <div>
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Preferred Date</dt>
            <dd class="mt-1 text-slate-800">{{ $requirement->preferred_date->format('d M Y') }}</dd>
          </div>
          @endif
          <div>
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Radius</dt>
            <dd class="mt-1 text-slate-800">{{ $requirement->visibility_radius_km }} km</dd>
          </div>
          @if($requirement->expiry_at)
          <div>
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Expires</dt>
            <dd class="mt-1 text-slate-800">{{ $requirement->expiry_at->diffForHumans() }}</dd>
          </div>
          @endif
          <div class="col-span-2">
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Address</dt>
            <dd class="mt-1 text-slate-800">{{ $requirement->address }}</dd>
          </div>
          <div class="col-span-2">
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Description</dt>
            <dd class="mt-1 text-slate-800 leading-relaxed">{{ $requirement->description }}</dd>
          </div>
        </dl>
      </div>

      {{-- Attachments --}}
      @if($requirement->attachments->isNotEmpty())
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h3 class="font-bold text-slate-800 mb-3">Attachments</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          @foreach($requirement->attachments as $att)
            <a href="{{ asset('storage/'.$att->file) }}" target="_blank"
               class="flex items-center gap-2 p-3 rounded-xl border border-slate-100 bg-slate-50 hover:border-primary-200 hover:bg-primary-50 transition">
              @if($att->file_type === 'image')
                <img src="{{ asset('storage/'.$att->file) }}" class="w-10 h-10 rounded-lg object-cover shrink-0">
              @else
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
              @endif
              <span class="text-[11px] text-slate-600 truncate">{{ basename($att->file) }}</span>
            </a>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Proposals --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-bold text-slate-800">Proposals <span class="text-slate-400 font-normal text-sm">({{ $requirement->proposals->count() }})</span></h3>
        </div>

        @if($requirement->proposals->isEmpty())
          <div class="text-center py-8">
            <svg class="w-10 h-10 text-slate-200 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <p class="text-slate-400 text-xs">No proposals yet. Providers will respond soon.</p>
          </div>
        @else
          <div class="space-y-3">
            @foreach($requirement->proposals as $proposal)
            <div class="border border-slate-100 rounded-xl p-4 {{ $proposal->status === 'accepted' ? 'border-green-200 bg-green-50/50' : '' }}">
              <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                  @if($proposal->provider?->providerProfile?->logo)
                    <img src="{{ asset('storage/'.$proposal->provider->providerProfile->logo) }}" class="w-10 h-10 rounded-full object-cover shrink-0">
                  @else
                    <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 font-bold text-sm flex items-center justify-center shrink-0">
                      {{ strtoupper(substr($proposal->provider?->providerProfile?->business_name ?? $proposal->provider?->name ?? '?', 0, 2)) }}
                    </div>
                  @endif
                  <div>
                    <p class="font-semibold text-slate-900">{{ $proposal->provider?->providerProfile?->business_name ?? $proposal->provider?->name }}</p>
                    <p class="text-[11px] text-slate-500">{{ $proposal->created_at->diffForHumans() }}</p>
                  </div>
                </div>
                <div class="text-right shrink-0">
                  <p class="font-bold text-slate-900">{{ $proposal->currency?->symbol }}{{ number_format($proposal->proposed_price, 2) }}</p>
                  @if($proposal->estimated_days)
                    <p class="text-[11px] text-slate-400">~{{ $proposal->estimated_days }} days</p>
                  @endif
                </div>
              </div>

              @if($proposal->message)
                <p class="mt-3 text-xs text-slate-600 leading-relaxed">{{ $proposal->message }}</p>
              @endif

              @if($requirement->status === 'open' && $proposal->status === 'pending')
                <div class="mt-3 flex gap-2">
                  <form action="{{ route('customer.requirements.proposals.accept', [$requirement, $proposal]) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-green-500 text-white text-xs font-semibold hover:bg-green-600 transition">Accept</button>
                  </form>
                  <form action="{{ route('customer.requirements.proposals.reject', [$requirement, $proposal]) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">Decline</button>
                  </form>
                </div>
              @elseif($proposal->status === 'accepted')
                <span class="mt-2 inline-block text-[11px] text-green-700 font-semibold">✓ Accepted</span>
              @elseif($proposal->status === 'rejected')
                <span class="mt-2 inline-block text-[11px] text-slate-400">Declined</span>
              @endif
            </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
      <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3">
        <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Summary</h3>
        <div class="space-y-2 text-xs">
          <div class="flex justify-between">
            <span class="text-slate-500">Posted</span>
            <span class="text-slate-700">{{ $requirement->created_at->diffForHumans() }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Proposals</span>
            <span class="font-semibold text-slate-800">{{ $requirement->proposals->count() }}</span>
          </div>
          @if($requirement->expiry_at)
          <div class="flex justify-between">
            <span class="text-slate-500">Expires</span>
            <span class="text-slate-700 {{ $requirement->expiry_at->isPast() ? 'text-red-600' : '' }}">{{ $requirement->expiry_at->diffForHumans() }}</span>
          </div>
          @endif
        </div>
      </div>

      @if($requirement->latitude && $requirement->longitude)
      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div id="req_show_map" class="w-full h-40"></div>
        <div class="px-4 py-3">
          <p class="text-xs text-slate-600">{{ $requirement->address }}</p>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

@if($requirement->latitude && $requirement->longitude)
@push('scripts')
<script>
  function initShowMap() {
    const lat = {{ $requirement->latitude }};
    const lng = {{ $requirement->longitude }};
    const map = new google.maps.Map(document.getElementById('req_show_map'), {
      center: { lat, lng }, zoom: 13,
      mapTypeControl: false, streetViewControl: false, fullscreenControl: false, zoomControl: false, scrollwheel: false
    });
    new google.maps.Marker({ position: { lat, lng }, map });
    new google.maps.Circle({
      map, center: { lat, lng },
      radius: {{ $requirement->visibility_radius_km ?? 10 }} * 1000,
      fillColor: '#0F94EA', fillOpacity: 0.08,
      strokeColor: '#0F94EA', strokeOpacity: 0.4, strokeWeight: 1.5
    });
  }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsdLSxCzJS1DypOOyGan4BWTZvZIhiS9M&callback=initShowMap" async defer></script>
@endpush
@endif
@endsection
