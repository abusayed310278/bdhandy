@extends('layouts.website')
@section('title', __('website/categories.title', ['app' => config('app.name')]))
@section('meta_description', __('website/categories.meta_description'))

@section('content')
<div class="max-w-7xl mx-auto px-4 lg:px-6 py-12">

  <div class="text-center mb-10">
    <h1 class="text-3xl font-bold text-slate-900">{{ __('website/categories.heading') }}</h1>
    <p class="mt-2 text-slate-500 max-w-xl mx-auto">{{ __('website/categories.subheading') }}</p>
  </div>

  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
    @foreach($categories as $cat)
    @php
      $name = $cat->getTranslation('translations', app()->getLocale()) ?: ($cat->getTranslation('translations', 'en') ?: $cat->slug);
    @endphp
    <a href="{{ route('home') }}" class="group bg-white rounded-2xl border border-slate-200 p-6 text-center hover:border-primary-300 hover:shadow-soft transition">
      @if($cat->icon)
        <div class="w-14 h-14 rounded-2xl bg-primary-50 flex items-center justify-center mx-auto mb-4 group-hover:bg-primary-100 transition">
          <img src="{{ asset('storage/'.$cat->icon) }}" class="w-8 h-8 object-contain">
        </div>
      @else
        <div class="w-14 h-14 rounded-2xl bg-primary-50 text-primary-500 flex items-center justify-center mx-auto mb-4 group-hover:bg-primary-100 transition">
          <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        </div>
      @endif
      <h3 class="font-semibold text-slate-900 group-hover:text-primary-600 transition">{{ $name }}</h3>
      <p class="text-xs text-slate-500 mt-1">{{ trans_choice('website/categories.services_count', $cat->services_count, ['count' => $cat->services_count]) }}</p>
    </a>
    @endforeach
  </div>

  @if($categories->isEmpty())
    <div class="text-center py-16">
      <p class="text-slate-500">{{ __('website/categories.no_categories') }}</p>
    </div>
  @endif
</div>
@endsection

