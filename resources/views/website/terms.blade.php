@extends('layouts.website')
@section('title', __('website/terms.title') . ' — ' . config('app.name'))
@section('meta_description', __('website/terms.meta_description', ['app' => config('app.name')]))

@section('content')
<div class="max-w-3xl mx-auto px-4 lg:px-6 py-12">
  <h1 class="text-3xl font-bold text-slate-900 mb-2">{{ __('website/terms.heading') }}</h1>
  <p class="text-sm text-slate-500 mb-8">{{ __('website/terms.last_updated') }}</p>

  <div class="space-y-6 text-sm text-slate-700 leading-relaxed">
    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/terms.sections.acceptance.title') }}</h2>
      <p>{{ __('website/terms.sections.acceptance.desc', ['app' => config('app.name')]) }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/terms.sections.service.title') }}</h2>
      <p>{{ __('website/terms.sections.service.desc', ['app' => config('app.name')]) }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/terms.sections.accounts.title') }}</h2>
      <p>{{ __('website/terms.sections.accounts.desc') }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/terms.sections.verification.title') }}</h2>
      <p>{{ __('website/terms.sections.verification.desc', ['app' => config('app.name')]) }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/terms.sections.subscriptions.title') }}</h2>
      <p>{{ __('website/terms.sections.subscriptions.desc') }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/terms.sections.conduct.title') }}</h2>
      <p>{{ __('website/terms.sections.conduct.desc') }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/terms.sections.liability.title') }}</h2>
      <p>{{ __('website/terms.sections.liability.desc', ['app' => config('app.name')]) }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/terms.sections.governing_law.title') }}</h2>
      <p>{{ __('website/terms.sections.governing_law.desc') }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/terms.sections.contact.title') }}</h2>
      <p>{!! __('website/terms.sections.contact.desc', ['link' => '<a href="' . route('contact') . '" class="text-primary-600 hover:underline">' . __('website/terms.sections.contact.link_text') . '</a>']) !!}</p>
    </section>
  </div>
</div>
@endsection
