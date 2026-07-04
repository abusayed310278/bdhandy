@extends('layouts.website')
@section('title', __('website/help.title', ['app' => config('app.name')]))
@section('meta_description', __('website/help.meta_description', ['app' => config('app.name')]))

@section('content')
<div class="bg-gradient-to-b from-primary-50 to-white border-b border-slate-100">
  <div class="max-w-2xl mx-auto px-4 py-12 text-center">
    <h1 class="text-3xl font-bold text-slate-900">{{ __('website/help.hero.heading') }}</h1>
    <p class="mt-2 text-slate-500">{{ __('website/help.hero.subheading') }}</p>
    <div class="mt-5 flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-4 py-3 max-w-lg mx-auto shadow-sm">
      <svg class="w-5 h-5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" placeholder="{{ __('website/help.hero.search_placeholder') }}" class="flex-1 bg-transparent border-0 p-0 text-sm focus:ring-0 focus:outline-none text-slate-900 placeholder-slate-400">
    </div>
  </div>
</div>

<div class="max-w-4xl mx-auto px-4 lg:px-6 py-12">
  <div class="grid sm:grid-cols-3 gap-5 mb-12">
    @foreach([
      ['icon'=>'📋','title'=>__('website/help.categories.customers.title'),'desc'=>__('website/help.categories.customers.desc'),'color'=>'primary'],
      ['icon'=>'💼','title'=>__('website/help.categories.providers.title'),'desc'=>__('website/help.categories.providers.desc'),'color'=>'accent'],
      ['icon'=>'🔐','title'=>__('website/help.categories.security.title'),'desc'=>__('website/help.categories.security.desc'),'color'=>'primary'],
    ] as $c)
    <a href="#" class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-{{ $c['color'] }}-300 hover:shadow-sm transition text-center">
      <div class="text-3xl mb-3">{{ $c['icon'] }}</div>
      <h3 class="font-semibold text-slate-900">{{ $c['title'] }}</h3>
      <p class="text-xs text-slate-500 mt-1">{{ $c['desc'] }}</p>
    </a>
    @endforeach
  </div>

  <h2 class="text-xl font-bold text-slate-900 mb-5">{{ __('website/help.faq.heading') }}</h2>
  <div x-data="{ open: null }" class="space-y-3">
    @foreach([
      ['q'=>__('website/help.faq.book.q'),'a'=>__('website/help.faq.book.a')],
      ['q'=>__('website/help.faq.free.q'),'a'=>__('website/help.faq.free.a', ['app' => config('app.name')])],
      ['q'=>__('website/help.faq.verification.q'),'a'=>__('website/help.faq.verification.a')],
      ['q'=>__('website/help.faq.unhappy.q'),'a'=>__('website/help.faq.unhappy.a')],
      ['q'=>__('website/help.faq.cancel.q'),'a'=>__('website/help.faq.cancel.a')],
      ['q'=>__('website/help.faq.become_provider.q'),'a'=>__('website/help.faq.become_provider.a')],
    ] as $i => $faq)
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
      <button @click="open = open === {{ $i }} ? null : {{ $i }}" class="w-full flex items-center justify-between px-5 py-4 text-start text-sm font-medium text-slate-900">
        {{ $faq['q'] }}
        <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform" :class="open === {{ $i }} && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div x-show="open === {{ $i }}" class="px-5 pb-4 text-sm text-slate-600 leading-relaxed">{{ $faq['a'] }}</div>
    </div>
    @endforeach
  </div>

  <div class="mt-12 bg-primary-50 rounded-2xl p-8 text-center border border-primary-100">
    <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/help.footer.heading') }}</h3>
    <p class="text-sm text-slate-600 mb-5">{{ __('website/help.footer.subheading') }}</p>
    <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition">{{ __('website/help.footer.contact_btn') }}</a>
  </div>
</div>
@endsection

