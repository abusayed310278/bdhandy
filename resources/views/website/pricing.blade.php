@extends('layouts.website')
@section('title', __('web.nav.pricing') . ' — ' . config('app.name'))
@section('meta_description', __('website/pricing.meta_description'))

@section('content')

{{-- Hero --}}
<div class="bg-gradient-to-b from-primary-50 to-white border-b border-slate-100">
  <div class="max-w-3xl mx-auto px-4 py-14 text-center">
    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-primary-100 text-primary-700 text-xs font-medium shadow-sm mb-4">
      <span class="w-1.5 h-1.5 rounded-full bg-accent-500"></span>
      {{ __('website/pricing.hero.badge') }}
    </span>
    <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">{{ __('web.pricing.heading') }}</h1>
    <p class="mt-3 text-slate-500 text-lg max-w-xl mx-auto">{{ __('web.pricing.subtext') }}</p>
  </div>
</div>

{{-- Plans grid --}}
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-14">

  @if($plans->count())
  <div class="grid sm:grid-cols-2 lg:grid-cols-{{ min($plans->count(), 3) }} gap-6 max-w-5xl mx-auto">
    @foreach($plans as $plan)
    @php
      $isPopular = $plan->duration_months >= 12;
      $sym = $plan->currency?->symbol ?? '৳';
    @endphp
    <div class="relative bg-white rounded-2xl border {{ $isPopular ? 'border-accent-300 ring-2 ring-accent-200' : 'border-slate-200' }} p-6 lg:p-7 flex flex-col">
      @if($isPopular)
        <div class="absolute -top-3 start-1/2 -translate-x-1/2">
          <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-accent-500 text-white shadow-warm">⭐ {{ __('web.pricing.most_popular') }}</span>
        </div>
      @endif

      <div class="mb-5">
        <p class="text-xs font-semibold uppercase tracking-wider text-primary-600">
          {{ $plan->duration_months >= 12 ? __('web.pricing.billed_yearly') : __('web.pricing.billed_monthly') }}
        </p>
        <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $plan->name }}</h2>
        @if($plan->discount_percent)
          <span class="mt-1 inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full bg-accent-100 text-accent-700">SAVE {{ $plan->discount_percent }}%</span>
        @endif
      </div>

      <div class="flex items-baseline gap-1.5 mb-6">
        <span class="text-4xl font-black text-slate-900">{{ $sym }} {{ number_format($plan->price) }}</span>
        <span class="text-sm text-slate-500">{{ __('web.pricing.per_month') }}</span>
      </div>

      <ul class="space-y-3 text-sm text-slate-700 border-t border-slate-100 pt-5 mb-6 flex-1">
        <li class="flex items-center gap-2.5">
          <svg class="w-4 h-4 text-primary-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          {{ $plan->lead_limit ? __('web.pricing.leads_limit', ['n' => $plan->lead_limit]) : __('web.pricing.leads_unlimited') }}
        </li>
        <li class="flex items-center gap-2.5">
          <svg class="w-4 h-4 text-primary-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          {{ __('web.pricing.areas_limit', ['n' => $plan->service_area_limit]) }}
        </li>
        <li class="flex items-center gap-2.5">
          <svg class="w-4 h-4 text-primary-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          {{ __('web.pricing.gallery_limit', ['n' => $plan->gallery_limit]) }}
        </li>
        @if($plan->is_featured)
        <li class="flex items-center gap-2.5">
          <svg class="w-4 h-4 text-primary-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          {{ __('web.pricing.featured') }}
        </li>
        @endif
        @if($plan->is_verified_badge_included)
        <li class="flex items-center gap-2.5">
          <svg class="w-4 h-4 text-primary-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          {{ __('web.pricing.badge') }}
        </li>
        @endif
        <li class="flex items-center gap-2.5">
          <svg class="w-4 h-4 text-primary-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          {{ __('website/pricing.plan.priority_support') }}
        </li>
      </ul>

      <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 rounded-xl {{ $isPopular ? 'bg-accent-500 hover:bg-accent-600 text-white shadow-warm' : 'bg-primary-500 hover:bg-primary-600 text-white shadow-soft' }} font-medium transition text-sm">
        {{ __('web.pricing.get_started') }}
      </a>
    </div>
    @endforeach
  </div>

  @else
  {{-- Fallback static plans --}}
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
    @foreach([
      ['name'=>__('website/pricing.plan.monthly'),'months'=>1,'price'=>'1,500','featured'=>false,'leads'=>'30','areas'=>2,'gallery'=>10,'badge'=>false],
      ['name'=>__('website/pricing.plan.yearly'),'months'=>12,'price'=>'800','featured'=>true,'leads'=>__('website/pricing.comparison.unlimited'),'areas'=>5,'gallery'=>30,'badge'=>true],
      ['name'=>__('website/pricing.plan.biannual'),'months'=>6,'price'=>'1,100','featured'=>false,'leads'=>'100','areas'=>3,'gallery'=>20,'badge'=>false],
    ] as $p)
    <div class="relative bg-white rounded-2xl border {{ $p['featured'] ? 'border-accent-300 ring-2 ring-accent-200' : 'border-slate-200' }} p-6 lg:p-7 flex flex-col">
      @if($p['featured'])
        <div class="absolute -top-3 start-1/2 -translate-x-1/2">
          <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-accent-500 text-white">⭐ {{ __('web.pricing.most_popular') }}</span>
        </div>
      @endif
      <p class="text-xs font-semibold uppercase tracking-wider text-primary-600">{{ $p['months'] >= 12 ? __('web.pricing.billed_yearly') : __('web.pricing.billed_monthly') }}</p>
      <h2 class="text-xl font-bold text-slate-900 mt-1">{{ $p['name'] }}</h2>
      <div class="flex items-baseline gap-1.5 mt-4 mb-6">
        <span class="text-4xl font-black text-slate-900">৳ {{ $p['price'] }}</span>
        <span class="text-sm text-slate-500">{{ __('web.pricing.per_month') }}</span>
      </div>
      <ul class="space-y-3 text-sm text-slate-700 border-t border-slate-100 pt-5 mb-6 flex-1">
        <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ __('website/pricing.plan.leads_per_month', ['leads' => $p['leads']]) }}</li>
        <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ __('website/pricing.plan.service_zones', ['zones' => $p['areas']]) }}</li>
        <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ __('website/pricing.plan.gallery_photos', ['photos' => $p['gallery']]) }}</li>
        @if($p['badge'])<li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ __('website/pricing.plan.featured_badge') }}</li>@endif
        <li class="flex items-center gap-2.5"><svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>{{ __('website/pricing.plan.priority_support_short') }}</li>
      </ul>
      <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 rounded-xl {{ $p['featured'] ? 'bg-accent-500 hover:bg-accent-600 text-white shadow-warm' : 'bg-primary-500 hover:bg-primary-600 text-white shadow-soft' }} font-medium transition text-sm">{{ __('web.pricing.get_started') }}</a>
    </div>
    @endforeach
  </div>
  @endif

  {{-- Coupon input --}}
  <div class="mt-10 max-w-md mx-auto">
    <p class="text-center text-sm text-slate-500 mb-3">{{ __('web.pricing.coupon_label') }}</p>
    <div class="flex gap-2">
      <input type="text" placeholder="{{ __('web.pricing.coupon_ph') }}" class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-400 focus:outline-none">
      <button class="px-5 py-3 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 transition">{{ __('web.pricing.coupon_apply') }}</button>
    </div>
  </div>

  {{-- Comparison table --}}
  <div class="mt-16 overflow-x-auto">
    <h2 class="text-xl font-bold text-slate-900 text-center mb-6">{{ __('website/pricing.comparison.title') }}</h2>
    <table class="w-full text-sm">
      <thead>
        <tr class="border-b border-slate-200">
          <th class="text-start py-3 pe-6 text-slate-500 font-medium">{{ __('website/pricing.comparison.feature') }}</th>
          @foreach($plans->take(3) as $p)
          <th class="py-3 px-4 text-center font-semibold text-slate-900">{{ $p->name }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @foreach([
          ['label'=>__('website/pricing.comparison.leads'), 'key'=>'lead_limit'],
          ['label'=>__('website/pricing.comparison.zones'),  'key'=>'service_area_limit'],
          ['label'=>__('website/pricing.comparison.photos'), 'key'=>'gallery_limit'],
          ['label'=>__('website/pricing.comparison.featured'),'key'=>'is_featured'],
          ['label'=>__('website/pricing.comparison.badge'), 'key'=>'is_verified_badge_included'],
        ] as $row)
        <tr>
          <td class="py-3 pe-6 text-slate-700">{{ $row['label'] }}</td>
          @foreach($plans->take(3) as $p)
          <td class="py-3 px-4 text-center text-slate-700">
            @php $val = $p->{$row['key']}; @endphp
            @if(is_bool($val))
              @if($val)
                <svg class="w-5 h-5 text-primary-500 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              @else
                <span class="text-slate-300">—</span>
              @endif
            @else
              {{ $val ?? __('website/pricing.comparison.unlimited') }}
            @endif
          </td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- FAQ strip --}}
  <div class="mt-14 max-w-2xl mx-auto">
    <h2 class="text-xl font-bold text-slate-900 text-center mb-6">{{ __('website/pricing.faq.heading') }}</h2>
    <div x-data="{ open: null }" class="space-y-3">
      @foreach([
        ['q'=>__('website/pricing.faq.trial.q'),'a'=>__('website/pricing.faq.trial.a')],
        ['q'=>__('website/pricing.faq.commission.q'),'a'=>__('website/pricing.faq.commission.a')],
        ['q'=>__('website/pricing.faq.cancel.q'),'a'=>__('website/pricing.faq.cancel.a')],
        ['q'=>__('website/pricing.faq.limit.q'),'a'=>__('website/pricing.faq.limit.a')],
      ] as $i => $faq)
      <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <button @click="open = open === {{ $i }} ? null : {{ $i }}" class="w-full flex items-center justify-between px-5 py-4 text-start text-sm font-medium text-slate-900">
          {{ $faq['q'] }}
          <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform" :class="open === {{ $i }} && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div x-show="open === {{ $i }}" x-collapse class="px-5 pb-4 text-sm text-slate-600 leading-relaxed">{{ $faq['a'] }}</div>
      </div>
      @endforeach
    </div>
  </div>
</div>

@endsection

