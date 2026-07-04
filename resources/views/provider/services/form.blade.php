@extends('layouts.dashboard')
@section('title', isset($service) ? 'Edit Service' : 'Add Service')

@section('content')
<div class="max-w-2xl space-y-5 text-sm">

  <div>
    <a href="{{ route('provider.services.index') }}" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1 mb-1">
      <svg class="w-3.5 h-3.5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
      My Services
    </a>
    <h2 class="text-xl font-bold text-slate-900">{{ isset($service) ? 'Edit Service' : 'Add New Service' }}</h2>
    <p class="text-slate-500 text-xs mt-0.5">{{ isset($service) ? 'Update service details and pricing' : 'Add a service you offer to customers' }}</p>
  </div>

  @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
      <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <script>
    function serviceForm() {
      return {
        categories: @json($categories),
        selectedCategoryId: '{{ old('category_id', $service->service->category_id ?? '') }}',
        selectedServiceId: '{{ old('service_id', $service->service_id ?? '') }}',
        pType: '{{ old('pricing_type', $service->pricing_type ?? 'fixed') }}',
        servicesFor(catId) {
          const cat = this.categories.find(c => c.id == catId);
          return cat ? cat.services : [];
        }
      }
    }
  </script>

  <form action="{{ isset($service) ? route('provider.services.update', $service) : route('provider.services.store') }}" 
        method="POST" class="space-y-5" x-data="serviceForm()">
    @csrf
    @if(isset($service)) @method('PUT') @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">

      <div class="grid sm:grid-cols-2 gap-4">
        {{-- Category --}}
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Category <span class="text-red-500">*</span></label>
          <select x-model="selectedCategoryId" @change="selectedServiceId = ''"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
            <option value="">Select category</option>
            <template x-for="cat in categories" :key="cat.id">
              <option :value="cat.id" x-text="cat.name" :selected="selectedCategoryId == cat.id"></option>
            </template>
          </select>
        </div>

        {{-- Service --}}
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1">Service <span class="text-red-500">*</span></label>
          <select name="service_id" x-model="selectedServiceId" required
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
            <option value="">Select service</option>
            <template x-for="svc in servicesFor(selectedCategoryId)" :key="svc.id">
              <option :value="svc.id" x-text="svc.name" :selected="selectedServiceId == svc.id"></option>
            </template>
          </select>
        </div>
      </div>

      {{-- Title --}}
      <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Service Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $service->title ?? '') }}" required
          placeholder="e.g. Full House Deep Cleaning"
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
      </div>

      {{-- Description --}}
      <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Description</label>
        <textarea name="description" rows="3"
          placeholder="Briefly describe what's included in this service"
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition resize-none">{{ old('description', $service->description ?? '') }}</textarea>
      </div>

      {{-- Pricing Type --}}
      <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Pricing Type <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-4 gap-2 mb-4">
          @foreach(['fixed' => 'Fixed', 'range' => 'Range', 'hourly' => 'Hourly', 'quote' => 'Quote'] as $val => $label)
          <label class="cursor-pointer">
            <input type="radio" name="pricing_type" value="{{ $val }}" x-model="pType" class="sr-only">
            <span :class="pType === '{{ $val }}' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-slate-200 bg-white text-slate-600'"
              class="block text-center text-xs font-semibold border rounded-xl py-2.5 transition">{{ $label }}</span>
          </label>
          @endforeach
        </div>

        {{-- Fixed price / Hourly price --}}
        <div x-show="pType === 'fixed' || pType === 'hourly'" class="grid sm:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Price <span class="text-red-500">*</span></label>
            <input type="number" name="price_fixed" value="{{ old('price_fixed', $service->price_fixed ?? '') }}" min="0" step="0.01"
              placeholder="0.00"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          </div>
        </div>

        {{-- Price range --}}
        <div x-show="pType === 'range'" class="grid sm:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Min Price <span class="text-red-500">*</span></label>
            <input type="number" name="price_min" value="{{ old('price_min', $service->price_min ?? '') }}" min="0" step="0.01"
              placeholder="Min"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Max Price <span class="text-red-500">*</span></label>
            <input type="number" name="price_max" value="{{ old('price_max', $service->price_max ?? '') }}" min="0" step="0.01"
              placeholder="Max"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          </div>
        </div>

        {{-- Shared Currency Field --}}
        <div x-show="pType !== 'quote'">
            <label class="block text-xs font-medium text-slate-700 mb-1">Currency <span class="text-red-500">*</span></label>
            <select name="currency_id" :required="pType !== 'quote'"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
                <option value="">Select currency</option>
                @foreach($currencies as $cur)
                    <option value="{{ $cur->id }}" {{ old('currency_id', $service->currency_id ?? '') == $cur->id ? 'selected' : '' }}>
                        {{ $cur->name }} ({{ $cur->symbol }})
                    </option>
                @endforeach
            </select>
        </div>
      </div>

      {{-- Duration --}}
      <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Estimated Duration (minutes)</label>
        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes ?? '') }}" min="0"
          placeholder="e.g. 60"
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
      </div>

      {{-- Options --}}
      <div class="flex flex-wrap gap-6 pt-1">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="is_emergency" value="1" {{ old('is_emergency', $service->is_emergency ?? false) ? 'checked' : '' }}
            class="w-4 h-4 rounded border-slate-300 text-red-500 focus:ring-red-200">
          <span class="text-xs font-medium text-slate-700">Available for emergencies</span>
        </label>
      </div>

      {{-- Status --}}
      <div>
        <label class="block text-xs font-medium text-slate-700 mb-1">Status</label>
        <select name="status"
          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          <option value="active" {{ old('status', $service->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ old('status', $service->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 pb-4">
      <a href="{{ route('provider.services.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">Cancel</a>
      <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition">
        {{ isset($service) ? 'Update Service' : 'Add Service' }}
      </button>
    </div>
  </form>
</div>
@endsection
