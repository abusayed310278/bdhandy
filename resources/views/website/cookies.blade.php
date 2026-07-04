@extends('layouts.website')
@section('title', __('website/cookies.title', ['app' => config('app.name')]))

@section('content')
<div class="max-w-3xl mx-auto px-4 lg:px-6 py-12">
  <h1 class="text-3xl font-bold text-slate-900 mb-2">{{ __('website/cookies.heading') }}</h1>
  <p class="text-sm text-slate-500 mb-8">{{ __('website/cookies.last_updated') }}</p>

  <div class="space-y-6 text-sm text-slate-700 leading-relaxed">
    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/cookies.what_are.heading') }}</h2>
      <p>{{ __('website/cookies.what_are.desc') }}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/cookies.we_use.heading') }}</h2>
      <div class="overflow-x-auto">
        <table class="w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
          <thead class="bg-slate-50">
            <tr>
              <th class="text-start px-4 py-3 font-medium text-slate-600">{{ __('website/cookies.we_use.table.cookie') }}</th>
              <th class="text-start px-4 py-3 font-medium text-slate-600">{{ __('website/cookies.we_use.table.purpose') }}</th>
              <th class="text-start px-4 py-3 font-medium text-slate-600">{{ __('website/cookies.we_use.table.duration') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach([
              ['XSRF-TOKEN', __('website/cookies.we_use.xsrf.purpose'), __('website/cookies.we_use.xsrf.duration')],
              [config("session.cookie","laravel_session"), __('website/cookies.we_use.session.purpose'), __('website/cookies.we_use.session.duration')],
              ['app_locale', __('website/cookies.we_use.locale.purpose'), __('website/cookies.we_use.locale.duration')],
              ['remember_web_*', __('website/cookies.we_use.remember.purpose'), __('website/cookies.we_use.remember.duration')],
            ] as $c)
            <tr>
              <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ $c[0] }}</td>
              <td class="px-4 py-3 text-slate-600">{{ $c[1] }}</td>
              <td class="px-4 py-3 text-slate-500">{{ $c[2] }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/cookies.managing.heading') }}</h2>
      <p>{!! __('website/cookies.managing.desc', ['code' => '<code class="bg-slate-100 px-1 py-0.5 rounded text-xs">app_locale</code>']) !!}</p>
    </section>

    <section>
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ __('website/cookies.contact.heading') }}</h2>
      <p>{!! __('website/cookies.contact.desc', ['link' => '<a href="' . route('contact') . '" class="text-primary-600 hover:underline">' . __('website/cookies.contact.link_text') . '</a>']) !!}</p>
    </section>
  </div>
</div>
@endsection

