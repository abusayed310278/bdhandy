@extends('layouts.website')
@section('title', __('website/contact.title', ['app' => config('app.name')]))
@section('meta_description', __('website/contact.meta_description', ['app' => config('app.name')]))

@section('content')
<div class="bg-gradient-to-b from-primary-50 to-white border-b border-slate-100">
  <div class="max-w-3xl mx-auto px-4 py-14 text-center">
    <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">{{ __('website/contact.hero.heading') }}</h1>
    <p class="mt-3 text-slate-500 text-lg max-w-xl mx-auto">{{ __('website/contact.hero.subheading') }}</p>
  </div>
</div>

<div class="max-w-5xl mx-auto px-4 lg:px-6 py-14 grid md:grid-cols-2 gap-10">

  {{-- Contact form --}}
  <div>
    @if(session('success'))
      <div class="mb-5 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700 font-medium">
        ✓ {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">{{ __('website/contact.form.name') }} <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-400 focus:outline-none transition @error('name') border-red-300 ring-2 ring-red-50 @enderror" placeholder="{{ __('website/contact.form.name_placeholder') }}">
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">{{ __('website/contact.form.email') }} <span class="text-red-500">*</span></label>
        <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-400 focus:outline-none transition @error('email') border-red-300 ring-2 ring-red-50 @enderror" placeholder="{{ __('website/contact.form.email_placeholder') }}">
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">{{ __('website/contact.form.phone') }} <span class="text-slate-400 font-normal">{{ __('website/contact.form.phone_optional') }}</span></label>
        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-400 focus:outline-none transition @error('phone') border-red-300 ring-2 ring-red-50 @enderror" placeholder="{{ __('website/contact.form.phone_placeholder') }}">
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">{{ __('website/contact.form.subject') }} <span class="text-red-500">*</span></label>
        <select name="subject" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-400 focus:outline-none transition">
          <option value="General enquiry" {{ old('subject') === 'General enquiry' ? 'selected' : '' }}>{{ __('website/contact.form.subjects.general') }}</option>
          <option value="Technical issue" {{ old('subject') === 'Technical issue' ? 'selected' : '' }}>{{ __('website/contact.form.subjects.technical') }}</option>
          <option value="Provider verification" {{ old('subject') === 'Provider verification' ? 'selected' : '' }}>{{ __('website/contact.form.subjects.verification') }}</option>
          <option value="Billing & subscriptions" {{ old('subject') === 'Billing & subscriptions' ? 'selected' : '' }}>{{ __('website/contact.form.subjects.billing') }}</option>
          <option value="Safety report" {{ old('subject') === 'Safety report' ? 'selected' : '' }}>{{ __('website/contact.form.subjects.safety') }}</option>
          <option value="Partnership" {{ old('subject') === 'Partnership' ? 'selected' : '' }}>{{ __('website/contact.form.subjects.partnership') }}</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">{{ __('website/contact.form.message') }} <span class="text-red-500">*</span></label>
        <textarea name="message" rows="5" required class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-400 focus:outline-none resize-none transition @error('message') border-red-300 ring-2 ring-red-50 @enderror" placeholder="{{ __('website/contact.form.message_placeholder') }}">{{ old('message') }}</textarea>
      </div>
      <button type="submit" class="w-full px-4 py-3 rounded-xl bg-primary-500 text-white font-medium hover:bg-primary-600 transition text-sm">{{ __('website/contact.form.submit') }}</button>
    </form>
  </div>

  {{-- Contact info --}}
  <div class="space-y-6">
    <div>
      <h2 class="text-xl font-bold text-slate-900 mb-5">{{ __('website/contact.info.heading') }}</h2>
      <div class="space-y-4">
        @foreach([
          ['icon'=>'📧','label'=>__('website/contact.info.email_support'),'value'=>'info@bdhandy.com','href'=>'mailto:info@bdhandy.com'],
          ['icon'=>'📞','label'=>__('website/contact.info.phone'),'value'=>'+8801913907107','href'=>'tel:+8801913907107'],
          ['icon'=>'💬','label'=>__('website/contact.info.whatsapp'),'value'=>'+8801913907107','href'=>'https://wa.me/8801913907107'],
        ] as $c)
        <a href="{{ $c['href'] }}" class="flex items-start gap-3 bg-white border border-slate-200 rounded-xl p-4 hover:border-primary-300 hover:shadow-sm transition">
          <span class="text-xl">{{ $c['icon'] }}</span>
          <div>
            <p class="text-xs text-slate-500">{{ $c['label'] }}</p>
            <p class="text-sm font-medium text-slate-900 mt-0.5">{{ $c['value'] }}</p>
          </div>
        </a>
        @endforeach
      </div>
    </div>

    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
      <h3 class="font-semibold text-slate-900 mb-2">{{ __('website/contact.info.office_title') }}</h3>
      <p class="text-sm text-slate-600 leading-relaxed">
        {!! __('website/contact.info.office_details') !!}
      </p>
    </div>

    <div class="bg-primary-50 rounded-2xl p-5 border border-primary-100">
      <h3 class="font-semibold text-slate-900 mb-1">{{ __('website/contact.info.hours_title') }}</h3>
      <p class="text-sm text-slate-600">{{ __('website/contact.info.saturday_thursday') }}</p>
      <p class="text-sm text-slate-600">{{ __('website/contact.info.friday') }}</p>
    </div>
  </div>
</div>
@endsection

