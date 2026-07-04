@extends('layouts.website')
@section('title', __('website/resources.title') . ' — ' . config('app.name'))
@section('meta_description', __('website/resources.meta_description', ['app' => config('app.name')]))

@section('content')
<div class="bg-gradient-to-b from-primary-50 to-white border-b border-slate-100">
  <div class="max-w-3xl mx-auto px-4 py-14 text-center">
    <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">{{ __('website/resources.heading') }}</h1>
    <p class="mt-3 text-slate-500 text-lg max-w-xl mx-auto">{{ __('website/resources.subheading', ['app' => config('app.name')]) }}</p>
  </div>
</div>

<div class="max-w-5xl mx-auto px-4 lg:px-6 py-14 space-y-12">

  <section>
    <h2 class="text-xl font-bold text-slate-900 mb-5">{{ __('website/resources.getting_started_title') }}</h2>
    <div class="grid sm:grid-cols-2 gap-5">
      @foreach([
        ['icon'=>'📋','title'=>__('website/resources.guides.profile.title'),'desc'=>__('website/resources.guides.profile.desc'),'tag'=>__('website/resources.guides.profile.tag')],
        ['icon'=>'🗺️','title'=>__('website/resources.guides.areas.title'),'desc'=>__('website/resources.guides.areas.desc'),'tag'=>__('website/resources.guides.areas.tag')],
        ['icon'=>'📸','title'=>__('website/resources.guides.gallery.title'),'desc'=>__('website/resources.guides.gallery.desc'),'tag'=>__('website/resources.guides.gallery.tag')],
        ['icon'=>'💬','title'=>__('website/resources.guides.leads.title'),'desc'=>__('website/resources.guides.leads.desc'),'tag'=>__('website/resources.guides.leads.tag')],
        ['icon'=>'⭐','title'=>__('website/resources.guides.reviews.title'),'desc'=>__('website/resources.guides.reviews.desc'),'tag'=>__('website/resources.guides.reviews.tag')],
        ['icon'=>'📊','title'=>__('website/resources.guides.analytics.title'),'desc'=>__('website/resources.guides.analytics.desc'),'tag'=>__('website/resources.guides.analytics.tag')],
      ] as $g)
      <div class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-primary-300 hover:shadow-sm transition">
        <div class="flex items-start gap-3">
          <span class="text-2xl">{{ $g['icon'] }}</span>
          <div class="flex-1">
            <div class="flex items-start justify-between gap-2">
              <h3 class="font-semibold text-slate-900 text-sm">{{ $g['title'] }}</h3>
              <span class="text-[11px] text-slate-400 shrink-0">{{ $g['tag'] }}</span>
            </div>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">{{ $g['desc'] }}</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </section>

  <section>
    <h2 class="text-xl font-bold text-slate-900 mb-5">{{ __('website/resources.video_walkthroughs_title') }}</h2>
    <div class="grid sm:grid-cols-3 gap-5">
      @foreach([
        ['title'=>__('website/resources.videos.profile.title'),'duration'=>__('website/resources.videos.profile.duration')],
        ['title'=>__('website/resources.videos.leads.title'),'duration'=>__('website/resources.videos.leads.duration')],
        ['title'=>__('website/resources.videos.subscription.title'),'duration'=>__('website/resources.videos.subscription.duration')],
      ] as $v)
      <div class="bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden">
        <div class="aspect-video bg-slate-200 flex items-center justify-center">
          <div class="w-12 h-12 rounded-full bg-white/80 flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5 text-primary-600 ms-0.5" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
          </div>
        </div>
        <div class="p-3">
          <p class="text-sm font-medium text-slate-900">{{ $v['title'] }}</p>
          <p class="text-xs text-slate-400 mt-0.5">{{ $v['duration'] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </section>

  <section class="bg-primary-50 rounded-2xl p-8 border border-primary-100 text-center">
    <h3 class="text-xl font-bold text-slate-900 mb-2">{{ __('website/resources.help.heading') }}</h3>
    <p class="text-sm text-slate-600 mb-5">{{ __('website/resources.help.desc') }}</p>
    <div class="flex flex-wrap gap-3 justify-center">
      <a href="{{ route('help') }}" class="px-5 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50">{{ __('website/resources.help.btn_help') }}</a>
      <a href="{{ route('contact') }}" class="px-5 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-medium hover:bg-primary-600 transition">{{ __('website/resources.help.btn_contact') }}</a>
    </div>
  </section>
</div>
@endsection
