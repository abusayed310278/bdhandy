@extends('layouts.dashboard')
@section('title', 'Review: ' . $provider->business_name)

@section('content')
<div class="space-y-6"
     x-data="{
       approveOpen: false,
       rejectOpen: false,
       docRejectOpen: false,
       docRejectUrl: '',
       docRejectName: '',
       subType: 'without',
     }">

  {{-- Flash --}}
  @if(session('success'))
    <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700 font-medium">
      {{ session('success') }}
    </div>
  @endif

  {{-- Back + Header --}}
  <div class="flex items-center gap-3">
    <a href="{{ route('admin.providers.index') }}"
       class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition">
      <svg class="w-5 h-5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
      <h2 class="text-xl font-semibold text-slate-900">{{ $provider->business_name }}</h2>
      <p class="text-sm text-slate-500">{{ ucfirst($provider->provider_type) }} · {{ $provider->user->email }}</p>
    </div>
    <div class="ms-auto flex items-center gap-2">
      @if($provider->verification_status === 'in_review')
        <button @click="rejectOpen = true"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          Reject All
        </button>
        <button @click="approveOpen = true"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Approve
        </button>
      @else
        @php
          $badges = [
            'approved' => 'bg-green-50 text-green-700 border-green-200',
            'rejected' => 'bg-red-50 text-red-700 border-red-200',
            'pending'  => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            'in_review'=> 'bg-primary-50 text-primary-700 border-primary-200',
          ];
        @endphp
        <span class="px-3 py-1 rounded-full text-sm font-semibold border {{ $badges[$provider->verification_status] ?? '' }}">
          {{ ucfirst(str_replace('_', ' ', $provider->verification_status)) }}
        </span>
      @endif
    </div>
  </div>

  {{-- Cover + Logo banner --}}
  @if($provider->cover_photo || $provider->logo)
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
      <div class="relative h-40 bg-gradient-to-r from-slate-100 to-slate-200">
        @if($provider->cover_photo)
          <img src="{{ asset('storage/' . $provider->cover_photo) }}" class="absolute inset-0 w-full h-full object-cover">
        @endif
        @if($provider->logo)
          <div class="absolute -bottom-8 left-5">
            <img src="{{ asset('storage/' . $provider->logo) }}" class="w-16 h-16 rounded-full border-4 border-white object-cover shadow">
          </div>
        @endif
      </div>
      <div class="h-10"></div>
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Profile, Services, Hours, Area, Documents --}}
    <div class="lg:col-span-2 space-y-5">

      {{-- Profile Card --}}
      <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">Profile Information</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div><span class="text-slate-500">Name</span><p class="font-medium text-slate-900 mt-0.5">{{ $provider->user->name }}</p></div>
          <div><span class="text-slate-500">Phone</span><p class="font-medium text-slate-900 mt-0.5">{{ $provider->primary_phone }}</p></div>
          @if($provider->whatsapp_number)
            <div><span class="text-slate-500">WhatsApp</span><p class="font-medium text-slate-900 mt-0.5">{{ $provider->whatsapp_number }}</p></div>
          @endif
          <div><span class="text-slate-500">Experience</span><p class="font-medium text-slate-900 mt-0.5">{{ $provider->years_of_experience ?? '—' }} yrs · {{ ucfirst($provider->experience_level ?? 'N/A') }}</p></div>
          <div><span class="text-slate-500">Emergency</span><p class="font-medium text-slate-900 mt-0.5">{{ $provider->emergency_available ? 'Yes' : 'No' }}</p></div>
          @if($provider->description)
            <div class="col-span-2"><span class="text-slate-500">Bio</span><p class="text-slate-700 mt-0.5">{{ $provider->description }}</p></div>
          @endif
        </div>

        {{-- Social links --}}
        @if($provider->website || $provider->facebook_url || $provider->instagram_url || $provider->youtube_url)
          <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-3">
            @if($provider->website)
              <a href="{{ $provider->website }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-primary-600 hover:underline">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                Website
              </a>
            @endif
            @if($provider->facebook_url)
              <a href="{{ $provider->facebook_url }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-blue-600 hover:underline">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Facebook
              </a>
            @endif
            @if($provider->instagram_url)
              <a href="{{ $provider->instagram_url }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-pink-600 hover:underline">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                Instagram
              </a>
            @endif
            @if($provider->youtube_url)
              <a href="{{ $provider->youtube_url }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-red-600 hover:underline">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
                YouTube
              </a>
            @endif
          </div>
        @endif
      </div>

      {{-- Services --}}
      <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">Services Offered</h3>
        @if($provider->services->isEmpty())
          <p class="text-sm text-slate-400">No services added.</p>
        @else
          <div class="space-y-3">
            @foreach($provider->services as $svc)
              @php
                $catName  = $svc->service?->category?->getTranslation('translations','en') ?: '—';
                $svcName  = ($svc->service?->getTranslation('translations','en')['name'] ?? null) ?: $svc->service?->slug ?? '—';
                $priceTxt = match($svc->pricing_type) {
                    'fixed'  => ($svc->currency?->symbol ?? '') . number_format($svc->price_fixed, 2),
                    'range'  => ($svc->currency?->symbol ?? '') . number_format($svc->price_min, 2) . ' – ' . number_format($svc->price_max, 2),
                    'hourly' => ($svc->currency?->symbol ?? '') . number_format($svc->price_fixed, 2) . '/hr',
                    default  => 'Quote on request',
                };
              @endphp
              <div class="rounded-lg border border-slate-200 p-3.5">
                <div class="flex items-start justify-between gap-3">
                  <div class="flex-1 min-w-0">
                    <p class="text-xs text-slate-400 mb-0.5">{{ $catName }} › {{ $svcName }}</p>
                    <p class="text-sm font-semibold text-slate-900">{{ $svc->title }}</p>
                    @if($svc->description)
                      <p class="text-xs text-slate-500 mt-1">{{ $svc->description }}</p>
                    @endif
                  </div>
                  <div class="text-right shrink-0">
                    <p class="text-sm font-bold text-primary-600">{{ $priceTxt }}</p>
                    @if($svc->duration_minutes)
                      <p class="text-xs text-slate-400 mt-0.5">{{ $svc->duration_minutes }} min</p>
                    @endif
                    @if($svc->is_emergency)
                      <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-50 text-red-600 border border-red-200">Emergency</span>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      {{-- Business Hours --}}
      <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">Business Hours</h3>
        @if($provider->businessHours->isEmpty())
          <p class="text-sm text-slate-400">No business hours set.</p>
        @else
          <div class="space-y-1.5">
            @foreach($provider->businessHours->sortBy('day_of_week_id') as $hour)
              @php $dayName = $hour->dayOfWeek?->getTranslation('translations','en') ?: 'Day ' . $hour->day_of_week_id; @endphp
              <div class="flex items-center gap-3 py-1.5 border-b border-slate-100 last:border-0 text-sm">
                <span class="w-24 font-medium text-slate-700 shrink-0">{{ $dayName }}</span>
                @if($hour->is_closed)
                  <span class="text-slate-400 italic text-xs">Closed</span>
                @else
                  <span class="text-slate-600">{{ \Carbon\Carbon::parse($hour->start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($hour->end_time)->format('g:i A') }}</span>
                @endif
              </div>
            @endforeach
          </div>
        @endif
      </div>

      {{-- Service Area --}}
      @php $serviceArea = $provider->serviceAreas->first(); @endphp
      <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">Service Area</h3>
        @if(!$serviceArea)
          <p class="text-sm text-slate-400">No service area set.</p>
        @else
          <div class="grid grid-cols-2 gap-3 text-sm mb-4">
            <div><span class="text-slate-500 text-xs">Country</span><p class="font-medium text-slate-900 mt-0.5">{{ $serviceArea->country?->name ?? '—' }}</p></div>
            <div><span class="text-slate-500 text-xs">Division</span><p class="font-medium text-slate-900 mt-0.5">{{ $serviceArea->division?->name ?? '—' }}</p></div>
            <div><span class="text-slate-500 text-xs">District</span><p class="font-medium text-slate-900 mt-0.5">{{ $serviceArea->district?->name ?? '—' }}</p></div>
            <div><span class="text-slate-500 text-xs">Area</span><p class="font-medium text-slate-900 mt-0.5">{{ $serviceArea->area?->name ?? '—' }}</p></div>
            @if($serviceArea->address)
              <div class="col-span-2"><span class="text-slate-500 text-xs">Address</span><p class="font-medium text-slate-900 mt-0.5">{{ $serviceArea->address }}</p></div>
            @endif
            <div><span class="text-slate-500 text-xs">Radius</span><p class="font-medium text-primary-600 mt-0.5">{{ $serviceArea->radius_km }} km</p></div>
          </div>
          @if($serviceArea->latitude && $serviceArea->longitude)
            <div id="adminMap" class="w-full h-64 rounded-xl border border-slate-200 bg-slate-100"></div>
          @endif
        @endif
      </div>

      {{-- Documents --}}
      <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">Submitted Documents</h3>

        @if($provider->documents->isEmpty())
          <p class="text-sm text-slate-400">No documents uploaded.</p>
        @else
          <div class="space-y-3">
            @foreach($provider->documents as $doc)
              @php
                $docStatus  = $doc->verification_status;
                $statusCls  = match($docStatus) {
                  'approved' => 'bg-green-50 text-green-700 border-green-200',
                  'rejected' => 'bg-red-50 text-red-700 border-red-200',
                  default    => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                };
              @endphp
              <div class="rounded-xl border border-slate-200 p-4">
                <div class="flex items-start gap-3">
                  <div class="w-9 h-9 bg-primary-50 rounded-lg flex items-center justify-center text-primary-500 shrink-0 mt-0.5">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900">{{ $doc->documentType->name ?? 'Document' }}</p>
                    @if($doc->document_number)
                      <p class="text-xs text-slate-500 mt-0.5">No: {{ $doc->document_number }}</p>
                    @endif
                    @if($docStatus === 'rejected' && $doc->rejection_reason)
                      <p class="text-xs text-red-600 mt-1"><span class="font-semibold">Rejected:</span> {{ $doc->rejection_reason }}</p>
                    @endif
                  </div>
                  <div class="flex items-center gap-2 shrink-0">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $statusCls }}">
                      {{ ucfirst($docStatus) }}
                    </span>
                    <a href="{{ asset('storage/' . $doc->document_file) }}" target="_blank"
                       class="text-xs font-semibold text-primary-600 hover:text-primary-700 hover:underline underline-offset-2">
                      View
                    </a>
                  </div>
                </div>

                @if($docStatus !== 'approved')
                  <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100">
                    <form method="POST" action="{{ route('admin.providers.documents.approve', [$provider, $doc]) }}">
                      @csrf
                      <button type="submit"
                              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 border border-green-200 text-green-700 text-xs font-semibold hover:bg-green-100 transition">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Approve
                      </button>
                    </form>
                    <button type="button"
                            @click="docRejectOpen = true;
                                    docRejectUrl  = '{{ route('admin.providers.documents.reject', [$provider, $doc]) }}';
                                    docRejectName = '{{ addslashes($doc->documentType->name ?? 'Document') }}';"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-100 transition">
                      <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      Reject
                    </button>
                  </div>
                @else
                  <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100">
                    <button type="button"
                            @click="docRejectOpen = true;
                                    docRejectUrl  = '{{ route('admin.providers.documents.reject', [$provider, $doc]) }}';
                                    docRejectName = '{{ addslashes($doc->documentType->name ?? 'Document') }}';"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-slate-500 border border-slate-200 text-xs font-medium hover:border-red-200 hover:text-red-600 transition">
                      <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      Request new upload
                    </button>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @endif
      </div>

    </div>

    {{-- Right: Quick Stats --}}
    <div class="space-y-4">
      <div class="bg-white rounded-xl border border-slate-200 p-5 text-sm space-y-3">
        <h3 class="font-semibold text-slate-900">Account Details</h3>
        <div class="flex justify-between"><span class="text-slate-500">User Code</span><span class="font-mono font-semibold text-slate-900">{{ $provider->user->user_code }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Registered</span><span class="text-slate-700">{{ $provider->user->created_at->format('M d, Y') }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Services</span><span class="text-slate-700">{{ $provider->services->count() }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Service Area</span><span class="text-slate-700">{{ $serviceArea ? ($serviceArea->radius_km . ' km radius') : 'Not set' }}</span></div>
      </div>

      @if($provider->documents->isNotEmpty())
        @php $docCounts = $provider->documents->groupBy('verification_status')->map->count(); @endphp
        <div class="bg-white rounded-xl border border-slate-200 p-5 text-sm space-y-2">
          <h3 class="font-semibold text-slate-900 mb-3">Document Summary</h3>
          @foreach(['pending' => 'yellow', 'approved' => 'green', 'rejected' => 'red'] as $s => $color)
            @if(($docCounts[$s] ?? 0) > 0)
              <div class="flex justify-between items-center">
                <span class="text-slate-500 capitalize">{{ $s }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-200">
                  {{ $docCounts[$s] }}
                </span>
              </div>
            @endif
          @endforeach
        </div>
      @endif
    </div>

  </div>

  {{-- ── Approve Provider Modal ── --}}
  <div x-show="approveOpen" x-transition
       class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50"
       style="display:none">
    <div @click.away="approveOpen = false"
         class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
      <div class="flex items-center justify-between mb-5">
        <h3 class="text-lg font-semibold text-slate-900">Approve Provider</h3>
        <button @click="approveOpen = false" class="text-slate-400 hover:text-slate-600">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form method="POST" action="{{ route('admin.providers.approve', $provider) }}">
        @csrf
        <div class="space-y-4">
          <div>
            <p class="text-sm font-medium text-slate-700 mb-2">Subscription</p>
            <div class="space-y-2">
              <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition"
                     :class="subType === 'without' ? 'border-primary-400 bg-primary-50' : ''">
                <input type="radio" name="subscription_type" value="without" x-model="subType" class="text-primary-600 focus:ring-primary-100">
                <div>
                  <p class="text-sm font-semibold text-slate-800">Without subscription</p>
                  <p class="text-xs text-slate-500">Provider can subscribe themselves later</p>
                </div>
              </label>
              <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition"
                     :class="subType === 'with' ? 'border-primary-400 bg-primary-50' : ''">
                <input type="radio" name="subscription_type" value="with" x-model="subType" class="text-primary-600 focus:ring-primary-100">
                <div>
                  <p class="text-sm font-semibold text-slate-800">Assign subscription plan</p>
                  <p class="text-xs text-slate-500">Assign a plan now on behalf of the provider</p>
                </div>
              </label>
            </div>
          </div>
          <div x-show="subType === 'with'" x-transition>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Select Plan</label>
            <select name="plan_id" :required="subType === 'with'"
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
              <option value="">Select a plan…</option>
              @foreach($plans as $plan)
                <option value="{{ $plan->id }}">
                  {{ $plan->name }}
                  @if($plan->price > 0)
                    — {{ $plan->currency->symbol ?? '' }}{{ number_format($plan->price, 2) }} / {{ $plan->duration_months }}mo
                  @else
                    — Free
                  @endif
                </option>
              @endforeach
            </select>
          </div>
          <p class="text-xs text-slate-500">All pending documents will be automatically approved. The provider will receive an email notification.</p>
        </div>
        <div class="flex justify-end gap-3 mt-6">
          <button type="button" @click="approveOpen = false"
                  class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">Cancel</button>
          <button type="submit"
                  class="px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition">Confirm Approval</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ── Reject Provider Modal ── --}}
  <div x-show="rejectOpen" x-transition
       class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50"
       style="display:none">
    <div @click.away="rejectOpen = false"
         class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
      <div class="flex items-center justify-between mb-5">
        <h3 class="text-lg font-semibold text-slate-900">Reject Application</h3>
        <button @click="rejectOpen = false" class="text-slate-400 hover:text-slate-600">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form method="POST" action="{{ route('admin.providers.reject', $provider) }}">
        @csrf
        <label class="block">
          <span class="block text-sm font-medium text-slate-700 mb-1.5">Rejection Reason <span class="text-red-500">*</span></span>
          <textarea name="reason" rows="4" required
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition resize-none"
                    placeholder="Explain why the application was rejected…"></textarea>
        </label>
        <p class="text-xs text-slate-500 mt-3">All pending documents will be marked rejected. The provider will be notified by email.</p>
        <div class="flex justify-end gap-3 mt-6">
          <button type="button" @click="rejectOpen = false"
                  class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">Cancel</button>
          <button type="submit"
                  class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition">Send Rejection</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ── Per-Document Reject Modal ── --}}
  <div x-show="docRejectOpen" x-transition
       class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50"
       style="display:none">
    <div @click.away="docRejectOpen = false"
         class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
      <div class="flex items-center justify-between mb-1">
        <h3 class="text-lg font-semibold text-slate-900">Reject Document</h3>
        <button @click="docRejectOpen = false" class="text-slate-400 hover:text-slate-600">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <p class="text-sm text-slate-500 mb-5">Rejecting: <span class="font-semibold text-slate-700" x-text="docRejectName"></span></p>
      <form method="POST" :action="docRejectUrl">
        @csrf
        <label class="block">
          <span class="block text-sm font-medium text-slate-700 mb-1.5">Reason <span class="text-red-500">*</span></span>
          <textarea name="reason" rows="3" required
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition resize-none"
                    placeholder="What's wrong with this document?"></textarea>
        </label>
        <p class="text-xs text-slate-500 mt-3">The provider will receive an email with this reason and a link to re-upload.</p>
        <div class="flex justify-end gap-3 mt-6">
          <button type="button" @click="docRejectOpen = false"
                  class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">Cancel</button>
          <button type="submit"
                  class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition">Reject Document</button>
        </div>
      </form>
    </div>
  </div>

</div>

@if(isset($serviceArea) && $serviceArea?->latitude && $serviceArea?->longitude)
@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsdLSxCzJS1DypOOyGan4BWTZvZIhiS9M"></script>
<script>
    function initAdminMap() {
        const lat    = parseFloat('{{ $serviceArea->latitude }}');
        const lng    = parseFloat('{{ $serviceArea->longitude }}');
        const radius = parseFloat('{{ $serviceArea->radius_km ?? 5 }}');

        const map = new google.maps.Map(document.getElementById('adminMap'), {
            center: { lat, lng },
            zoom: 12,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            styles: [{ featureType: 'poi', stylers: [{ visibility: 'off' }] }]
        });

        new google.maps.Marker({ position: { lat, lng }, map });

        const circle = new google.maps.Circle({
            map,
            center: { lat, lng },
            radius: radius * 1000,
            strokeColor: '#0F94EA',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#0F94EA',
            fillOpacity: 0.12
        });

        map.fitBounds(circle.getBounds());
    }
    google.maps.event.addDomListener(window, 'load', initAdminMap);
</script>
@endpush
@endif
@endsection
