@extends('layouts.dashboard')
@section('title', 'Post a Requirement')

@section('content')
<style>
  [x-cloak] { display: none !important; }
</style>

<script>
  // Safe global variables injection to bypass Alpine double quotes encoding issues in HTML attributes
  window.requirementCategories = @json($categoriesForJs);
  window.customerAddresses = @json($addresses);

  function requirementForm(categories, initialAddresses) {
    return {
      categoryId: '{{ old('category_id', '') }}',
      serviceId: '{{ old('service_id', '') }}',
      budgetType: '{{ old('budget_type', 'fixed') }}',
      urgency: '{{ old('urgency', 'normal') }}',
      services: [],
      fileList: [],

      // Addresses variables
      addresses: initialAddresses || [],
      selectedAddressId: '',
      address: '{{ old('address', '') }}',
      latitude: '{{ old('latitude', '') }}',
      longitude: '{{ old('longitude', '') }}',
      showManualAddress: true,

      init() {
        if (this.categoryId) {
          this.onCategoryChange();
          this.serviceId = '{{ old('service_id', '') }}';
        }

        const oldAddressText = '{{ old('address', '') }}';
        const oldLat = '{{ old('latitude', '') }}';
        const oldLng = '{{ old('longitude', '') }}';

        if (oldAddressText) {
          this.address = oldAddressText;
          this.latitude = oldLat;
          this.longitude = oldLng;

          const matched = this.addresses.find(a => a.address === oldAddressText);
          if (matched) {
            this.selectedAddressId = matched.id;
            this.showManualAddress = false;
          } else {
            this.selectedAddressId = 'other';
            this.showManualAddress = true;
          }
        } else {
          // Default selection fallback chain
          const primary = this.addresses.find(a => a.is_primary);
          if (primary) {
            this.selectedAddressId = primary.id;
            this.selectAddress(primary.id);
          } else if (this.addresses.length > 0) {
            this.selectedAddressId = this.addresses[0].id;
            this.selectAddress(this.addresses[0].id);
          } else {
            this.selectedAddressId = 'other';
            this.selectAddress('other');
          }
        }
      },

      onCategoryChange() {
        const cat = categories.find(c => c.id == this.categoryId);
        this.services = cat ? cat.services : [];
        this.serviceId = '';
      },

      selectAddress(id) {
        if (id === 'other') {
          this.showManualAddress = true;
          this.address = '';
          this.latitude = '';
          this.longitude = '';
          if (window.reqMarker) {
            const defaultPos = { lat: 23.8103, lng: 90.4125 };
            window.reqMarker.setPosition(defaultPos);
            if (window.reqRadiusCircle) window.reqRadiusCircle.setCenter(defaultPos);
            window.reqMap.setCenter(defaultPos);
            window.reqMap.setZoom(12);
            const pill = document.getElementById('coord_pill');
            if (pill) pill.classList.add('hidden');
          }
        } else {
          const addr = this.addresses.find(a => a.id == id);
          if (addr) {
            this.showManualAddress = false;
            this.address = addr.address;
            this.latitude = addr.latitude;
            this.longitude = addr.longitude;
            
            if (window.reqMarker && addr.latitude && addr.longitude) {
              const pos = { lat: parseFloat(addr.latitude), lng: parseFloat(addr.longitude) };
              window.reqMarker.setPosition(pos);
              if (window.reqRadiusCircle) window.reqRadiusCircle.setCenter(pos);
              window.reqMap.setCenter(pos);
              window.reqMap.setZoom(15);
              updateReqCoords(pos.lat, pos.lng);
            }
          }
        }
      },

      previewFiles(e) {
        this.fileList = Array.from(e.target.files);
      },

      handleDrop(e) {
        const files = e.dataTransfer.files;
        if (!files.length) return;
        const input = document.getElementById('attachments');
        if (input) {
          const dt = new DataTransfer();
          Array.from(files).forEach(f => dt.items.add(f));
          input.files = dt.files;
          this.fileList = Array.from(files);
        }
      }
    }
  }
</script>

<div class="max-w-3xl space-y-6 text-sm"
     x-data="requirementForm(window.requirementCategories, window.customerAddresses)">

  {{-- Header --}}
  <div class="flex items-center justify-between gap-4">
    <div>
      <a href="{{ route('customer.requirements.index') }}" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1.5 mb-1.5 transition">
        <svg class="w-3.5 h-3.5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        My Requirements
      </a>
      <h2 class="text-2xl font-black text-slate-900 tracking-tight">Post a Requirement</h2>
      <p class="text-xs text-slate-500 mt-1">Describe your needs in detail — nearby matching providers will pitch their custom proposals</p>
    </div>
  </div>

  @if($errors->any())
    <div class="bg-red-50 border border-red-200/80 rounded-2xl px-4 py-3.5 flex items-start gap-3">
      <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <div>
        <h4 class="text-xs font-bold text-red-800 mb-1">Please fix the following validation errors:</h4>
        <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    </div>
  @endif

  <form action="{{ route('customer.requirements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- Basic Info Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/60 p-6 space-y-4 shadow-sm hover:shadow-soft transition duration-200">
      <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
        <span class="w-8 h-8 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
          <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </span>
        <h3 class="font-bold text-slate-800 text-sm">Basic Information</h3>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Title / Headline <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title') }}" required
          placeholder="e.g. Need a professional electrician to install 3 ceiling fans"
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition duration-150">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Detailed Description <span class="text-red-500">*</span></label>
        <textarea name="description" rows="5" required
          placeholder="Describe your job details in detail. List any specific tools, brands, or special challenges. The clearer the details, the higher the proposal accuracy."
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition duration-150 resize-none">{{ old('description') }}</textarea>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Category <span class="text-red-500">*</span></label>
          <select name="category_id" x-model="categoryId" @change="onCategoryChange" required
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition duration-150">
            <option value="">Select category</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}">
                {{ $cat->getTranslation('translations','en') ?: $cat->slug }}
              </option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Specific Service <span class="text-red-500">*</span></label>
          <select name="service_id" x-model="serviceId" :disabled="!services.length" required
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition duration-150 disabled:opacity-50">
            <option value="">Select service</option>
            <template x-for="svc in services" :key="svc.id">
              <option :value="svc.id" x-text="svc.name" :selected="svc.id == {{ old('service_id', 0) }}"></option>
            </template>
          </select>
        </div>
      </div>
    </div>

    {{-- Urgency & Timing Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/60 p-6 space-y-4 shadow-sm hover:shadow-soft transition duration-200">
      <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
        <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
          <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </span>
        <h3 class="font-bold text-slate-800 text-sm">Timing & Urgency</h3>
      </div>

      <div class="space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Urgency Level <span class="text-red-500">*</span></label>
          <div class="grid sm:grid-cols-3 gap-3">
            @foreach(['normal' => ['Normal', 'slate', 'bg-slate-50 text-slate-700 border-slate-400 ring-2 ring-slate-100', 'Standard listing priority'], 
                      'urgent' => ['Urgent', 'amber', 'bg-amber-50/60 text-amber-800 border-amber-400 ring-2 ring-amber-100', 'Faster matching responses'], 
                      'emergency' => ['Emergency', 'red', 'bg-red-50/60 text-red-800 border-red-400 ring-2 ring-red-100', 'Immediate action listing']] as $ug => [$ugLabel, $ugColor, $ugActiveClass, $ugDesc])
              <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 hover:bg-slate-50 select-none group"
                     :class="urgency === '{{ $ug }}' ? '{{ $ugActiveClass }}' : 'border-slate-200 bg-white hover:border-slate-300'">
                <input type="radio" name="urgency" value="{{ $ug }}" x-model="urgency" class="sr-only">
                
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-black uppercase tracking-wider">{{ $ugLabel }}</span>
                  <div class="w-6 h-6 rounded-full border flex items-center justify-center transition"
                       :class="urgency === '{{ $ug }}' ? 'bg-{{ $ugColor }}-600 border-transparent text-white' : 'border-slate-200 group-hover:border-slate-300 bg-white'">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" x-show="urgency === '{{ $ug }}'"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                  </div>
                </div>
                <p class="text-[10px] text-slate-400 font-semibold leading-normal group-hover:text-slate-500 transition"
                   :class="urgency === '{{ $ug }}' ? 'text-{{ $ugColor }}-700' : ''">
                  {{ $ugDesc }}
                </p>
              </label>
            @endforeach
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Preferred Service Date <span class="text-xs text-slate-400 font-semibold">(optional)</span></label>
          <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d') }}"
            class="w-full sm:max-w-xs rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition duration-150">
        </div>
      </div>
    </div>

    {{-- Budget Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/60 p-6 space-y-4 shadow-sm hover:shadow-soft transition duration-200">
      <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
        <span class="w-8 h-8 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shrink-0">
          <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </span>
        <h3 class="font-bold text-slate-800 text-sm">Budget Details</h3>
      </div>

      <div class="grid grid-cols-3 gap-2">
        @foreach(['fixed' => ['Fixed Price', 'bg-primary-50 text-primary-700 border-primary-400 ring-2 ring-primary-100'],
                  'range' => ['Price Range', 'bg-primary-50 text-primary-700 border-primary-400 ring-2 ring-primary-100'],
                  'negotiable' => ['Negotiable', 'bg-primary-50 text-primary-700 border-primary-400 ring-2 ring-primary-100']] as $bt => [$btLabel, $btActiveClass])
          <label class="cursor-pointer select-none">
            <input type="radio" name="budget_type" value="{{ $bt }}" x-model="budgetType" class="sr-only">
            <div class="text-center py-3 rounded-2xl border-2 text-xs font-black transition-all duration-200 hover:bg-slate-50"
                 :class="budgetType === '{{ $bt }}' ? '{{ $btActiveClass }}' : 'border-slate-200 text-slate-600 bg-white hover:border-slate-300'">
              {{ $btLabel }}
            </div>
          </label>
        @endforeach
      </div>

      <div class="grid sm:grid-cols-2 gap-4" x-show="budgetType !== 'negotiable'" x-cloak x-transition>
        <div x-show="budgetType === 'fixed'" x-transition>
          <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Fixed Price Amount</label>
          <div class="relative">
            <div class="absolute inset-y-0 start-0 ps-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
              {{ $currencies->first()?->symbol ?? '$' }}
            </div>
            <input type="number" name="budget_fixed" value="{{ old('budget_fixed') }}" min="0" step="0.01"
              placeholder="0.00"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 ps-8 pe-4 py-3 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition duration-150">
          </div>
        </div>
        <div x-show="budgetType === 'range'" x-transition class="sm:col-span-2 grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Minimum Amount</label>
            <div class="relative">
              <div class="absolute inset-y-0 start-0 ps-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                {{ $currencies->first()?->symbol ?? '$' }}
              </div>
              <input type="number" name="budget_min" value="{{ old('budget_min') }}" min="0" step="0.01"
                placeholder="0.00"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 ps-8 pe-4 py-3 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition duration-150">
            </div>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Maximum Amount</label>
            <div class="relative">
              <div class="absolute inset-y-0 start-0 ps-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                {{ $currencies->first()?->symbol ?? '$' }}
              </div>
              <input type="number" name="budget_max" value="{{ old('budget_max') }}" min="0" step="0.01"
                placeholder="0.00"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 ps-8 pe-4 py-3 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition duration-150">
            </div>
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Currency <span class="text-red-500">*</span></label>
          <select name="currency_id" required
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition duration-150">
            @foreach($currencies as $cur)
              <option value="{{ $cur->id }}" {{ old('currency_id', $currencies->first()?->id) == $cur->id ? 'selected' : '' }}>
                {{ $cur->symbol }} {{ $cur->name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div x-show="budgetType === 'negotiable'" x-cloak x-transition>
        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Target Currency <span class="text-red-500">*</span></label>
        <select name="currency_id" required
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition duration-150 sm:max-w-xs">
          @foreach($currencies as $cur)
            <option value="{{ $cur->id }}" {{ old('currency_id', $currencies->first()?->id) == $cur->id ? 'selected' : '' }}>
              {{ $cur->symbol }} {{ $cur->name }}
            </option>
          @endforeach
        </select>
        <p class="mt-2 text-[11px] text-slate-400 font-medium">Providers will pitch their customized cost quotes inside their proposals.</p>
      </div>
    </div>

    {{-- Location Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/60 p-6 space-y-4 shadow-sm hover:shadow-soft transition duration-200">
      <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
        <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
          <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </span>
        <h3 class="font-bold text-slate-800 text-sm">Service Location</h3>
      </div>

      {{-- Address Selector Cards --}}
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-3 uppercase tracking-wide">Select Service Address <span class="text-red-500">*</span></label>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3">
          @foreach($addresses as $addr)
            @php
              $isHome = in_array(strtolower($addr->address_type), ['home', 'house']);
              $isOffice = in_array(strtolower($addr->address_type), ['office', 'work', 'business']);
              $iconColor = $isHome ? 'text-primary-600 bg-primary-50' : ($isOffice ? 'text-emerald-600 bg-emerald-50' : 'text-slate-600 bg-slate-100');
            @endphp
            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 hover:bg-slate-50 select-none group"
                   :class="selectedAddressId == '{{ $addr->id }}' ? 'border-primary-500 bg-primary-50/20 text-primary-800 ring-2 ring-primary-100' : 'border-slate-200 bg-white hover:border-slate-300'">
              <input type="radio" name="selected_address_id_dummy" value="{{ $addr->id }}" x-model="selectedAddressId" @change="selectAddress('{{ $addr->id }}')" class="sr-only">
              
              <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                  <span class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 text-xs font-bold {{ $iconColor }}">
                    @if($isHome)
                      🏠
                    @elseif($isOffice)
                      🏢
                    @else
                      📍
                    @endif
                  </span>
                  <span class="text-xs font-black uppercase tracking-wider text-slate-800">{{ $addr->label ?: ucfirst($addr->address_type) }}</span>
                </div>
                <div class="w-5 h-5 rounded-full border flex items-center justify-center transition shrink-0"
                     :class="selectedAddressId == '{{ $addr->id }}' ? 'bg-primary-600 border-transparent text-white' : 'border-slate-200 group-hover:border-slate-300 bg-white'">
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" x-show="selectedAddressId == '{{ $addr->id }}'"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
              </div>
              <p class="text-[11px] text-slate-500 font-medium leading-relaxed line-clamp-2 group-hover:text-slate-600 transition"
                 :class="selectedAddressId == '{{ $addr->id }}' ? 'text-slate-700 font-semibold' : ''">
                {{ $addr->address }}
              </p>
            </label>
          @endforeach

          {{-- Others Option Card --}}
          <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 hover:bg-slate-50 select-none group"
                 :class="selectedAddressId == 'other' ? 'border-primary-500 bg-primary-50/20 text-primary-800 ring-2 ring-primary-100' : 'border-slate-200 bg-white hover:border-slate-300'">
            <input type="radio" name="selected_address_id_dummy" value="other" x-model="selectedAddressId" @change="selectAddress('other')" class="sr-only">
            
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 text-xs font-bold">
                  ➕
                </span>
                <span class="text-xs font-black uppercase tracking-wider text-slate-800">Others</span>
              </div>
              <div class="w-5 h-5 rounded-full border flex items-center justify-center transition shrink-0"
                   :class="selectedAddressId == 'other' ? 'bg-primary-600 border-transparent text-white' : 'border-slate-200 group-hover:border-slate-300 bg-white'">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" x-show="selectedAddressId == 'other'"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
              </div>
            </div>
            <p class="text-[11px] text-slate-400 font-semibold leading-relaxed group-hover:text-slate-500 transition"
               :class="selectedAddressId == 'other' ? 'text-slate-600 font-bold' : ''">
              Enter a custom address and target a dynamic service area location manually.
            </p>
          </label>
        </div>
      </div>

      {{-- Address Text Input (shown only if 'other' is selected) --}}
      <div x-show="showManualAddress" x-transition x-cloak class="space-y-1">
        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wide">Search Address or Landmark <span class="text-red-500">*</span></label>
        <input type="text" id="address_input" name="address" x-model="address" :required="showManualAddress"
          placeholder="Search for address or landmark..."
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition duration-150">
      </div>

      {{-- Read-only summary when using saved address --}}
      <div x-show="!showManualAddress" x-transition x-cloak class="bg-primary-50/30 rounded-2xl border border-primary-100 p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-primary-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <div>
          <p class="font-black text-slate-800 text-xs uppercase tracking-wider mb-0.5">Using Saved Profile Address</p>
          <p class="text-xs text-slate-600 leading-relaxed" x-text="address"></p>
          <input type="hidden" name="address" :value="address">
        </div>
      </div>

      {{-- Hidden Coordinates inputs --}}
      <input type="hidden" name="latitude" id="latitude" x-model="latitude">
      <input type="hidden" name="longitude" id="longitude" x-model="longitude">

      {{-- Coordinate display pill --}}
      <div id="coord_pill" class="flex items-center gap-2 rounded-xl bg-slate-50 border border-slate-200/80 px-3.5 py-2.5 w-fit" :class="latitude ? '' : 'hidden'">
        <svg class="w-4 h-4 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>
        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Lat:</span>
        <span id="display_lat" class="text-[11px] text-slate-700 font-mono font-semibold" x-text="latitude ? parseFloat(latitude).toFixed(6) : ''"></span>
        <span class="text-slate-300 text-[11px]">|</span>
        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Lng:</span>
        <span id="display_lng" class="text-[11px] text-slate-700 font-mono font-semibold" x-text="longitude ? parseFloat(longitude).toFixed(6) : ''"></span>
      </div>

      {{-- Interactive Map --}}
      <div class="relative">
        <div id="req_map" class="w-full h-64 rounded-2xl overflow-hidden border border-slate-200 shadow-inner"></div>
        <div class="absolute bottom-3 left-3 bg-white/95 backdrop-blur-md rounded-lg shadow-sm border border-slate-100 px-2.5 py-1.5 text-[10px] text-slate-500 font-bold select-none">
          Drag pin to pinpoint target location
        </div>
      </div>

      {{-- Search Radius Slider --}}
      <div class="pt-2">
        <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide flex justify-between">
          <span>Target Search Radius</span>
          <span id="radius_label" class="text-primary-600 font-black tracking-wide">10 km</span>
        </label>
        <input type="range" name="visibility_radius_km" id="radius_slider" min="1" max="50" value="{{ old('visibility_radius_km', 10) }}"
          class="w-full accent-primary-500 h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer"
          oninput="document.getElementById('radius_label').textContent = this.value + ' km'; if(window.reqRadiusCircle) window.reqRadiusCircle.setRadius(this.value * 1000)">
        <div class="flex justify-between text-[9px] text-slate-400 font-bold mt-1.5 uppercase tracking-wide">
          <span>1 km</span><span>25 km</span><span>50 km</span>
        </div>
      </div>
    </div>

    {{-- Attachments Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/60 p-6 space-y-4 shadow-sm hover:shadow-soft transition duration-200">
      <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
          <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        </span>
        <h3 class="font-bold text-slate-800 text-sm">Reference Photos / Documents</h3>
      </div>
      <p class="text-xs text-slate-500 -mt-2">Upload any related pictures, diagrams, or PDF details to facilitate exact vendor pricing.</p>

      <label class="flex flex-col items-center justify-center gap-3 border-2 border-dashed border-slate-200 hover:border-primary-400 hover:bg-primary-50/20 rounded-2xl py-8 px-4 cursor-pointer transition duration-200 group"
             x-on:dragover.prevent x-on:drop.prevent="handleDrop($event)">
        <div class="w-12 h-12 rounded-xl bg-slate-50 group-hover:bg-primary-50 group-hover:text-primary-600 flex items-center justify-center transition text-slate-400">
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
        </div>
        <div class="text-center">
          <span class="text-xs text-slate-700 font-bold block mb-0.5">Click to upload or drag and drop</span>
          <span class="text-[10px] text-slate-400 font-medium block">JPG, PNG, PDF · max 5 MB per file</span>
        </div>
        <input type="file" name="attachments[]" id="attachments" multiple accept=".jpg,.jpeg,.png,.pdf"
          class="hidden" @change="previewFiles($event)">
      </label>

      <div x-show="fileList.length" class="space-y-2 mt-3" x-cloak x-transition>
        <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Selected Files</p>
        <div class="grid sm:grid-cols-2 gap-2">
          <template x-for="(f, i) in fileList" :key="i">
            <div class="flex items-center gap-3 px-4 py-2.5 bg-slate-50/70 border border-slate-200/60 rounded-2xl hover:bg-slate-50 transition">
              <span class="w-8 h-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              </span>
              <div class="flex-1 min-w-0">
                <p class="text-xs text-slate-700 font-bold truncate" x-text="f.name"></p>
                <p class="text-[9px] text-slate-400 font-bold" x-text="(f.size/1024/1024).toFixed(2) + ' MB'"></p>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

    {{-- Form Submit Actions --}}
    <div class="flex items-center justify-end gap-3 pb-8">
      <a href="{{ route('customer.requirements.index') }}" 
         class="px-6 py-3.5 rounded-2xl border border-slate-200 text-sm font-bold text-slate-600 hover:bg-slate-50 transition duration-150">
        Cancel
      </a>
      <button type="submit" 
              class="inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl bg-primary-600 text-white text-sm font-black hover:bg-primary-700 shadow-sm hover:shadow transition duration-150 cursor-pointer">
        <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9l20-7z"/></svg>
        Post Requirement
      </button>
    </div>
  </form>
</div>

@push('scripts')
<script>
  let reqMap, reqMarker, reqRadiusCircle;

  function initReqMap() {
    const latInput = document.getElementById('latitude').value;
    const lngInput = document.getElementById('longitude').value;
    const defaultLat = latInput ? parseFloat(latInput) : 23.8103;
    const defaultLng = lngInput ? parseFloat(lngInput) : 90.4125;

    reqMap = new google.maps.Map(document.getElementById('req_map'), {
      center: { lat: defaultLat, lng: defaultLng },
      zoom: latInput ? 15 : 12,
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: false,
      styles: [
        { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
        { featureType: 'transit', elementType: 'labels', stylers: [{ visibility: 'off' }] }
      ]
    });

    reqMarker = new google.maps.Marker({
      position: { lat: defaultLat, lng: defaultLng },
      map: reqMap,
      draggable: true,
      title: 'Drag to set location',
      icon: {
        path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
        scale: 6,
        fillColor: '#F97316',
        fillOpacity: 1,
        strokeWeight: 2,
        strokeColor: '#FFFFFF'
      }
    });

    reqRadiusCircle = new google.maps.Circle({
      map: reqMap,
      center: { lat: defaultLat, lng: defaultLng },
      radius: {{ old('visibility_radius_km', 10) }} * 1000,
      fillColor: '#0F94EA',
      fillOpacity: 0.08,
      strokeColor: '#0F94EA',
      strokeOpacity: 0.35,
      strokeWeight: 1.5
    });

    reqMarker.addListener('dragend', function (e) {
      const lat = e.latLng.lat();
      const lng = e.latLng.lng();
      if (reqRadiusCircle) reqRadiusCircle.setCenter(e.latLng);
      updateReqCoords(lat, lng);
    });

    const addressInput = document.getElementById('address_input');
    if (addressInput) {
      const autocomplete = new google.maps.places.Autocomplete(addressInput);
      autocomplete.bindTo('bounds', reqMap);
      autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) return;
        if (place.geometry.viewport) {
          reqMap.fitBounds(place.geometry.viewport);
        } else {
          reqMap.setCenter(place.geometry.location);
          reqMap.setZoom(15);
        }
        reqMarker.setPosition(place.geometry.location);
        if (reqRadiusCircle) reqRadiusCircle.setCenter(place.geometry.location);
        updateReqCoords(place.geometry.location.lat(), place.geometry.location.lng());
        
        // Update Alpine model directly
        const alpElement = document.querySelector('[x-data]');
        if (alpElement && alpElement.__x) {
          const alp = alpElement.__x.$data;
          alp.address = place.formatted_address || place.name || addressInput.value;
          alp.latitude = place.geometry.location.lat().toFixed(8);
          alp.longitude = place.geometry.location.lng().toFixed(8);
        }
      });
    }

    if (latInput && lngInput) {
      updateReqCoords(parseFloat(latInput), parseFloat(lngInput));
    }
  }

  function updateReqCoords(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(8);
    document.getElementById('longitude').value = lng.toFixed(8);
    
    const displayLat = document.getElementById('display_lat');
    const displayLng = document.getElementById('display_lng');
    const coordPill = document.getElementById('coord_pill');
    
    if (displayLat) displayLat.textContent = lat.toFixed(6);
    if (displayLng) displayLng.textContent = lng.toFixed(6);
    if (coordPill) coordPill.classList.remove('hidden');

    const alpElement = document.querySelector('[x-data]');
    if (alpElement && alpElement.__x) {
      const alp = alpElement.__x.$data;
      alp.latitude = lat.toFixed(8);
      alp.longitude = lng.toFixed(8);
    }
  }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsdLSxCzJS1DypOOyGan4BWTZvZIhiS9M&libraries=places&callback=initReqMap" async defer></script>
@endpush
@endsection
