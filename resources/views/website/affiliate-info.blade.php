@extends('layouts.website')
@section('title', __('website/affiliate_info.title') . ' — ' . config('app.name'))
@section('meta_description', __('website/affiliate_info.meta_description', ['app' => config('app.name')]))

@section('content')
<div class="bg-gradient-to-b from-accent-50 to-white border-b border-accent-100">
  <div class="max-w-3xl mx-auto px-4 py-14 text-center">
    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-accent-200 text-accent-700 text-xs font-medium shadow-sm mb-4">
      <span class="w-1.5 h-1.5 rounded-full bg-accent-500"></span>
      {{ __('web.affiliate.label') }}
    </span>
    <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">{{ __('web.affiliate.heading') }}</h1>
    <p class="mt-3 text-slate-500 text-lg max-w-xl mx-auto">{{ __('web.affiliate.subtext', ['app' => config('app.name')]) }}</p>
    <a href="{{ route('register') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-accent-500 text-white font-semibold hover:bg-accent-600 transition shadow-warm text-sm">{{ __('web.affiliate.cta') }} →</a>
  </div>
</div>

<div class="max-w-5xl mx-auto px-4 lg:px-6 py-14 space-y-14">
  <section>
    <h2 class="text-2xl font-bold text-slate-900 text-center mb-8">{{ __('web.affiliate.how_it_works') }}</h2>
    <div class="grid md:grid-cols-3 gap-6">
      <div class="text-center">
        <div class="w-14 h-14 rounded-2xl bg-accent-50 text-accent-600 flex items-center justify-center mx-auto mb-4 text-2xl">🔗</div>
        <h3 class="font-semibold text-slate-900 mb-2">{{ __('web.affiliate.step1_title') }}</h3>
        <p class="text-sm text-slate-500 leading-relaxed">{{ __('web.affiliate.step1_desc') }}</p>
      </div>
      <div class="text-center">
        <div class="w-14 h-14 rounded-2xl bg-accent-50 text-accent-600 flex items-center justify-center mx-auto mb-4 text-2xl">👥</div>
        <h3 class="font-semibold text-slate-900 mb-2">{{ __('web.affiliate.step2_title') }}</h3>
        <p class="text-sm text-slate-500 leading-relaxed">{{ __('web.affiliate.step2_desc') }}</p>
      </div>
      <div class="text-center">
        <div class="w-14 h-14 rounded-2xl bg-accent-50 text-accent-600 flex items-center justify-center mx-auto mb-4 text-2xl">💰</div>
        <h3 class="font-semibold text-slate-900 mb-2">{{ __('web.affiliate.step3_title') }}</h3>
        <p class="text-sm text-slate-500 leading-relaxed">{{ __('web.affiliate.step3_desc') }}</p>
      </div>
    </div>
  </section>

  <section class="bg-white border border-slate-200 rounded-2xl p-8 max-w-2xl mx-auto">
    <h2 class="text-xl font-bold text-slate-900 mb-5 text-center">{{ __('web.affiliate.structure') }}</h2>
    <div class="rounded-xl border border-slate-100 bg-slate-50 p-8 text-center">
      <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">{{ __('web.affiliate.rate_label') }}</p>
      <p class="text-5xl font-black text-accent-600">{{ __('web.affiliate.rate_value') }}</p>
      <p class="text-sm text-slate-500 mt-3 font-medium">{{ __('web.affiliate.rate_desc') }}</p>
    </div>
    <p class="mt-6 text-xs text-slate-500 text-center italic">{{ __('web.affiliate.payout_info') }}</p>
  </section>

  <section>
    <h2 class="text-xl font-bold text-slate-900 mb-5">{{ __('web.affiliate.who_can_join') }}</h2>
    <div class="grid sm:grid-cols-2 gap-5">
      @foreach([
        ['icon'=>'📱','title'=>__('website/affiliate_info.audiences.social.title'),'desc'=>__('website/affiliate_info.audiences.social.desc')],
        ['icon'=>'🏢','title'=>__('website/affiliate_info.audiences.business.title'),'desc'=>__('website/affiliate_info.audiences.business.desc')],
        ['icon'=>'📰','title'=>__('website/affiliate_info.audiences.bloggers.title'),'desc'=>__('website/affiliate_info.audiences.bloggers.desc')],
        ['icon'=>'👤','title'=>__('website/affiliate_info.audiences.individuals.title'),'desc'=>__('website/affiliate_info.audiences.individuals.desc')],
      ] as $w)
      <div class="bg-white border border-slate-200 rounded-xl p-5 flex gap-3">
        <span class="text-2xl">{{ $w['icon'] }}</span>
        <div>
          <h3 class="font-semibold text-slate-900 text-sm">{{ $w['title'] }}</h3>
          <p class="text-xs text-slate-500 mt-1">{{ $w['desc'] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </section>

  <div class="text-center bg-accent-50 rounded-2xl p-8 border border-accent-100">
    <h3 class="text-xl font-bold text-slate-900 mb-2">{{ __('web.affiliate.ready_title') }}</h3>
    <p class="text-sm text-slate-600 mb-5">{{ __('web.affiliate.ready_desc', ['app' => config('app.name')]) }}</p>
    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-accent-500 text-white font-semibold hover:bg-accent-600 transition shadow-warm text-sm">{{ __('web.affiliate.cta') }} →</a>
  </div>
</div>
@endsection
