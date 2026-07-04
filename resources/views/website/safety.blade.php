@extends('layouts.website')
@section('title', __('website/safety.title') . ' — ' . config('app.name'))
@section('meta_description', __('website/safety.meta_description', ['app' => config('app.name')]))

@section('content')
<div class="bg-gradient-to-b from-primary-50 to-white border-b border-slate-100">
  <div class="max-w-3xl mx-auto px-4 py-14 text-center">
    <div class="w-14 h-14 rounded-2xl bg-primary-100 text-primary-600 flex items-center justify-center mx-auto mb-4 text-2xl">🛡️</div>
    <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">{{ __('website/safety.heading', ['app' => config('app.name')]) }}</h1>
    <p class="mt-3 text-slate-500 text-lg max-w-xl mx-auto">{{ __('website/safety.subheading') }}</p>
  </div>
</div>

<div class="max-w-4xl mx-auto px-4 lg:px-6 py-14 space-y-12">
  <section>
    <h2 class="text-xl font-bold text-slate-900 mb-5">{{ __('website/safety.verify_title') }}</h2>
    <div class="grid sm:grid-cols-3 gap-5">
      @foreach([
        ['step'=>'1','title'=>__('website/safety.steps.identity.title'),'desc'=>__('website/safety.steps.identity.desc')],
        ['step'=>'2','title'=>__('website/safety.steps.document.title'),'desc'=>__('website/safety.steps.document.desc')],
        ['step'=>'3','title'=>__('website/safety.steps.monitoring.title'),'desc'=>__('website/safety.steps.monitoring.desc')],
      ] as $s)
      <div class="bg-white border border-slate-200 rounded-2xl p-5">
        <div class="w-8 h-8 rounded-full bg-primary-500 text-white font-bold text-sm flex items-center justify-center mb-3">{{ $s['step'] }}</div>
        <h3 class="font-semibold text-slate-900 mb-2">{{ $s['title'] }}</h3>
        <p class="text-sm text-slate-500 leading-relaxed">{{ $s['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </section>

  <section>
    <h2 class="text-xl font-bold text-slate-900 mb-5">{{ __('website/safety.tips_title') }}</h2>
    <div class="space-y-3">
      @foreach(__('website/safety.tips') as $tip)
      <div class="flex items-start gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3">
        <svg class="w-5 h-5 text-primary-500 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <p class="text-sm text-slate-700">{{ $tip }}</p>
      </div>
      @endforeach
    </div>
  </section>

  <section class="bg-red-50 rounded-2xl p-6 border border-red-100">
    <h2 class="text-lg font-bold text-slate-900 mb-2">{{ __('website/safety.report_title') }}</h2>
    <p class="text-sm text-slate-600 mb-4">{{ __('website/safety.report_desc', ['app' => config('app.name')]) }}</p>
    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition">{{ __('website/safety.report_btn') }}</a>
  </section>
</div>
@endsection
