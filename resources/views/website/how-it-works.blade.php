@extends('layouts.website')
@section('title', __('website/how_it_works.title', ['app' => config('app.name')]))
@section('meta_description', __('website/how_it_works.meta_description', ['app' => config('app.name')]))

@section('content')

{{-- Hero --}}
<div class="bg-gradient-to-b from-primary-50 to-white border-b border-slate-100">
  <div class="max-w-4xl mx-auto px-4 py-16 text-center">
    <h1 class="text-4xl font-bold text-slate-900 mb-4">{{ __('website/how_it_works.hero.heading', ['app' => config('app.name')]) }}</h1>
    <p class="text-lg text-slate-600 max-w-2xl mx-auto">{{ __('website/how_it_works.hero.subheading') }}</p>
  </div>
</div>

{{-- For Customers --}}
<div class="max-w-6xl mx-auto px-4 lg:px-6 py-16">
  <div class="text-center mb-12">
    <span class="inline-block text-xs font-bold uppercase tracking-wider text-primary-600 bg-primary-50 px-3 py-1 rounded-full mb-3">{{ __('website/how_it_works.customers.badge') }}</span>
    <h2 class="text-2xl font-bold text-slate-900">{{ __('website/how_it_works.customers.heading') }}</h2>
  </div>

  <div class="grid md:grid-cols-3 gap-8">
    @foreach([
      ['step'=>'1', 'title'=>__('website/how_it_works.customers.step1_title'), 'desc'=>__('website/how_it_works.customers.step1_desc'), 'icon'=>'<path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9l20-7z"/>'],
      ['step'=>'2', 'title'=>__('website/how_it_works.customers.step2_title'), 'desc'=>__('website/how_it_works.customers.step2_desc'), 'icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
      ['step'=>'3', 'title'=>__('website/how_it_works.customers.step3_title'), 'desc'=>__('website/how_it_works.customers.step3_desc'), 'icon'=>'<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
    ] as $s)
    <div class="relative text-center">
      <div class="w-14 h-14 rounded-2xl bg-primary-500 text-white flex items-center justify-center mx-auto mb-5 shadow-soft">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $s['icon'] !!}</svg>
      </div>
      <span class="absolute top-0 right-1/2 translate-x-10 -translate-y-1 text-5xl font-black text-slate-100 select-none leading-none">{{ $s['step'] }}</span>
      <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $s['title'] }}</h3>
      <p class="text-sm text-slate-500 leading-relaxed">{{ $s['desc'] }}</p>
    </div>
    @endforeach
  </div>

  <div class="mt-10 text-center">
    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-primary-500 text-white font-semibold hover:bg-primary-600 transition shadow-soft">
      {{ __('website/how_it_works.customers.cta') }}
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>
</div>

<div class="border-t border-slate-100"></div>

{{-- For Providers --}}
<div class="max-w-6xl mx-auto px-4 lg:px-6 py-16">
  <div class="text-center mb-12">
    <span class="inline-block text-xs font-bold uppercase tracking-wider text-accent-600 bg-accent-50 px-3 py-1 rounded-full mb-3">{{ __('website/how_it_works.providers.badge') }}</span>
    <h2 class="text-2xl font-bold text-slate-900">{{ __('website/how_it_works.providers.heading', ['app' => config('app.name')]) }}</h2>
  </div>

  <div class="grid md:grid-cols-3 gap-8">
    @foreach([
      ['step'=>'1', 'title'=>__('website/how_it_works.providers.step1_title'), 'desc'=>__('website/how_it_works.providers.step1_desc'), 'icon'=>'<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
      ['step'=>'2', 'title'=>__('website/how_it_works.providers.step2_title'), 'desc'=>__('website/how_it_works.providers.step2_desc'), 'icon'=>'<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>'],
      ['step'=>'3', 'title'=>__('website/how_it_works.providers.step3_title'), 'desc'=>__('website/how_it_works.providers.step3_desc'), 'icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
    ] as $s)
    <div class="relative text-center">
      <div class="w-14 h-14 rounded-2xl bg-accent-500 text-white flex items-center justify-center mx-auto mb-5 shadow-warm">
        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $s['icon'] !!}</svg>
      </div>
      <span class="absolute top-0 right-1/2 translate-x-10 -translate-y-1 text-5xl font-black text-slate-100 select-none leading-none">{{ $s['step'] }}</span>
      <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $s['title'] }}</h3>
      <p class="text-sm text-slate-500 leading-relaxed">{{ $s['desc'] }}</p>
    </div>
    @endforeach
  </div>

  <div class="mt-10 text-center">
    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-accent-500 text-white font-semibold hover:bg-accent-600 transition shadow-warm">
      {{ __('website/how_it_works.providers.cta') }}
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>
</div>

{{-- Trust strip --}}
<div class="bg-slate-50 border-t border-slate-100 py-12">
  <div class="max-w-5xl mx-auto px-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
      @foreach([
        ['label'=>__('website/how_it_works.trust.verified'),'icon'=>'<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
        ['label'=>__('website/how_it_works.trust.pricing'),'icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
        ['label'=>__('website/how_it_works.trust.payments'),'icon'=>'<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>'],
        ['label'=>__('website/how_it_works.trust.support'),'icon'=>'<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>'],
      ] as $t)
      <div class="flex flex-col items-center gap-3">
        <div class="w-12 h-12 rounded-2xl bg-primary-100 text-primary-600 flex items-center justify-center">
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">{!! $t['icon'] !!}</svg>
        </div>
        <p class="text-sm font-semibold text-slate-800">{{ $t['label'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</div>

@endsection

