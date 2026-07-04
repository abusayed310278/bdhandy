@extends('layouts.website')
@section('title', __('website/about.title') . ' — ' . config('app.name'))
@section('meta_description', __('website/about.meta_description', ['app' => config('app.name')]))

@section('content')
<div class="bg-gradient-to-b from-primary-50 to-white border-b border-slate-100">
  <div class="max-w-3xl mx-auto px-4 py-14 text-center">
    <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">{{ __('website/about.heading', ['app' => config('app.name')]) }}</h1>
    <p class="mt-3 text-slate-500 text-lg max-w-xl mx-auto">{{ __('website/about.tagline') }}</p>
  </div>
</div>

<div class="max-w-4xl mx-auto px-4 lg:px-6 py-14 space-y-14">
  <div class="grid md:grid-cols-2 gap-8 items-center">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('website/about.mission.heading') }}</h2>
      <p class="text-slate-600 leading-relaxed">{{ __('website/about.mission.desc1', ['app' => config('app.name')]) }}</p>
      <p class="mt-4 text-slate-600 leading-relaxed">{{ __('website/about.mission.desc2') }}</p>
    </div>
    <div class="bg-primary-50 rounded-2xl p-8 text-center">
      <div class="text-5xl mb-3">🔧</div>
      <p class="text-3xl font-bold text-slate-900">5,000+</p>
      <p class="text-slate-500 mt-1">{{ __('website/about.stats.providers') }}</p>
      <div class="mt-4 grid grid-cols-2 gap-4 text-center">
        <div><p class="text-2xl font-bold text-primary-600">20+</p><p class="text-xs text-slate-500">{{ __('website/about.stats.cities') }}</p></div>
        <div><p class="text-2xl font-bold text-accent-600">4.8★</p><p class="text-xs text-slate-500">{{ __('website/about.stats.rating') }}</p></div>
      </div>
    </div>
  </div>

  <div>
    <h2 class="text-2xl font-bold text-slate-900 mb-6">{{ __('website/about.values.heading') }}</h2>
    <div class="grid sm:grid-cols-3 gap-5">
      @foreach([
        ['icon'=>'🔍','title'=>__('website/about.values.transparency.title'),'desc'=>__('website/about.values.transparency.desc')],
        ['icon'=>'🛡️','title'=>__('website/about.values.trust.title'),'desc'=>__('website/about.values.trust.desc')],
        ['icon'=>'⚡','title'=>__('website/about.values.speed.title'),'desc'=>__('website/about.values.speed.desc')],
      ] as $v)
      <div class="bg-white border border-slate-200 rounded-2xl p-5">
        <div class="text-3xl mb-3">{{ $v['icon'] }}</div>
        <h3 class="font-semibold text-slate-900 mb-2">{{ $v['title'] }}</h3>
        <p class="text-sm text-slate-500 leading-relaxed">{{ $v['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </div>

  <div class="bg-slate-50 rounded-2xl p-8">
    <h2 class="text-2xl font-bold text-slate-900 mb-4">{{ __('website/about.company.heading') }}</h2>
    <p class="text-slate-600 leading-relaxed">{{ __('website/about.company.desc', ['app' => config('app.name')]) }}</p>
    <div class="mt-5">
      <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition">{{ __('website/about.company.contact') }} →</a>
    </div>
  </div>
</div>
@endsection
