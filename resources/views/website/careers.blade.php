@extends('layouts.website')
@section('title', __('website/careers.title', ['app' => config('app.name')]))
@section('meta_description', __('website/careers.meta_description', ['app' => config('app.name')]))

@section('content')
<div class="bg-gradient-to-b from-primary-50 to-white border-b border-slate-100">
  <div class="max-w-3xl mx-auto px-4 py-14 text-center">
    <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">{{ __('website/careers.hero.heading') }}</h1>
    <p class="mt-3 text-slate-500 text-lg max-w-xl mx-auto">{{ __('website/careers.hero.subheading') }}</p>
  </div>
</div>

<div class="max-w-4xl mx-auto px-4 lg:px-6 py-14">
  <div class="bg-accent-50 rounded-2xl p-8 mb-10 border border-accent-100">
    <h2 class="text-xl font-bold text-slate-900 mb-2">{{ __('website/careers.why_work.heading') }}</h2>
    <div class="grid sm:grid-cols-3 gap-5 mt-4">
      @foreach([
        ['icon'=>'🌍','title'=>__('website/careers.why_work.impact.title'),'desc'=>__('website/careers.why_work.impact.desc')],
        ['icon'=>'🚀','title'=>__('website/careers.why_work.fast.title'),'desc'=>__('website/careers.why_work.fast.desc')],
        ['icon'=>'🏡','title'=>__('website/careers.why_work.remote.title'),'desc'=>__('website/careers.why_work.remote.desc')],
      ] as $w)
      <div>
        <div class="text-2xl mb-2">{{ $w['icon'] }}</div>
        <h3 class="font-semibold text-slate-900 text-sm">{{ $w['title'] }}</h3>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $w['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </div>

  <h2 class="text-xl font-bold text-slate-900 mb-5">{{ __('website/careers.positions.heading') }}</h2>
  <div class="space-y-4">
    @foreach([
      [
        'title'=>__('website/careers.positions.jobs.laravel.title'),
        'type'=>__('website/careers.positions.jobs.laravel.type'),
        'location'=>__('website/careers.positions.jobs.laravel.location'),
        'dept'=>__('website/careers.positions.jobs.laravel.dept')
      ],
      [
        'title'=>__('website/careers.positions.jobs.designer.title'),
        'type'=>__('website/careers.positions.jobs.designer.type'),
        'location'=>__('website/careers.positions.jobs.designer.location'),
        'dept'=>__('website/careers.positions.jobs.designer.dept')
      ],
      [
        'title'=>__('website/careers.positions.jobs.support.title'),
        'type'=>__('website/careers.positions.jobs.support.type'),
        'location'=>__('website/careers.positions.jobs.support.location'),
        'dept'=>__('website/careers.positions.jobs.support.dept')
      ],
      [
        'title'=>__('website/careers.positions.jobs.marketing.title'),
        'type'=>__('website/careers.positions.jobs.marketing.type'),
        'location'=>__('website/careers.positions.jobs.marketing.location'),
        'dept'=>__('website/careers.positions.jobs.marketing.dept')
      ],
    ] as $job)
    <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-start justify-between gap-4 hover:border-primary-300 hover:shadow-sm transition">
      <div>
        <h3 class="font-semibold text-slate-900">{{ $job['title'] }}</h3>
        <div class="mt-1.5 flex flex-wrap gap-2">
          <span class="text-xs px-2 py-0.5 rounded-full bg-primary-50 text-primary-700">{{ $job['dept'] }}</span>
          <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $job['type'] }}</span>
          <span class="text-xs text-slate-500 flex items-center gap-1">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ $job['location'] }}
          </span>
        </div>
      </div>
      <a href="{{ route('contact') }}" class="shrink-0 px-4 py-2 rounded-lg bg-primary-50 text-primary-700 text-sm font-medium hover:bg-primary-100 transition">{{ __('website/careers.positions.apply') }}</a>
    </div>
    @endforeach
  </div>

  <div class="mt-10 text-center bg-slate-50 rounded-2xl p-8">
    <p class="text-slate-600">{{ __('website/careers.footer.desc') }}</p>
    <a href="{{ route('contact') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition">{{ __('website/careers.footer.cv') }}</a>
  </div>
</div>
@endsection

