@extends('layouts.website')
@section('title', __('website/privacy.title', ['app' => config('app.name')]))
@section('meta_description', __('website/privacy.meta_description', ['app' => config('app.name')]))

@section('content')
<div class="max-w-3xl mx-auto px-4 lg:px-6 py-12">
  <h1 class="text-3xl font-bold text-slate-900 mb-2">{{ __('website/privacy.heading') }}</h1>
  <p class="text-sm text-slate-500 mb-8">{{ __('website/privacy.last_updated') }}</p>

  <div class="prose prose-slate max-w-none space-y-6 text-sm text-slate-700 leading-relaxed">
    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/privacy.sections.collect.heading') }}</h2>
      <p>{{ __('website/privacy.sections.collect.desc', ['app' => config('app.name')]) }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/privacy.sections.use.heading') }}</h2>
      <p>{{ __('website/privacy.sections.use.desc') }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/privacy.sections.sharing.heading') }}</h2>
      <p>{{ __('website/privacy.sections.sharing.desc1') }}</p>
      <p class="mt-2">{{ __('website/privacy.sections.sharing.desc2') }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/privacy.sections.cookies.heading') }}</h2>
      <p>{!! __('website/privacy.sections.cookies.desc', ['link' => '<a href="' . route('cookies') . '" class="text-primary-600 hover:underline">' . __('website/privacy.sections.cookies.link_text') . '</a>']) !!}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/privacy.sections.security.heading') }}</h2>
      <p>{{ __('website/privacy.sections.security.desc') }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/privacy.sections.rights.heading') }}</h2>
      <p>{{ __('website/privacy.sections.rights.desc', ['email' => 'info@bdhandy.com']) }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/privacy.sections.contact.heading') }}</h2>
      <p>{!! __('website/privacy.sections.contact.desc', ['link' => '<a href="' . route('contact') . '" class="text-primary-600 hover:underline">' . __('website/privacy.sections.contact.link_text') . '</a>']) !!}</p>
    </section>
  </div>
</div>
@endsection

