@extends('layouts.onboarding')
@section('title', 'Complete Your Profile')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-primary-50 via-white to-accent-50 flex items-center justify-center py-12 px-4">
  <div class="w-full max-w-lg">

    {{-- Progress --}}
    <div class="mb-8 text-center">
      <div class="inline-flex items-center gap-3 mb-4">
        <span class="w-8 h-8 rounded-full bg-primary-500 text-white text-sm font-bold flex items-center justify-center">1</span>
        <div class="w-12 h-0.5 bg-primary-200"></div>
        <span class="w-8 h-8 rounded-full bg-slate-200 text-slate-400 text-sm font-bold flex items-center justify-center">2</span>
      </div>
      <h1 class="text-2xl font-bold text-slate-900">Complete your profile</h1>
      <p class="text-slate-500 mt-1">Just a few details to personalise your experience</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
      <form method="POST" action="{{ route('customer.onboarding.profile.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Avatar upload --}}
        <div class="flex items-center gap-4" x-data="{ preview: '{{ $user->photo ? asset('storage/'.$user->photo) : '' }}' }">
          <label class="relative cursor-pointer shrink-0">
            <div class="w-16 h-16 rounded-full bg-primary-100 border-2 border-dashed border-primary-300 flex items-center justify-center overflow-hidden">
              <template x-if="preview">
                <img :src="preview" class="w-full h-full object-cover rounded-full">
              </template>
              <template x-if="!preview">
                <svg class="w-6 h-6 text-primary-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </template>
            </div>
            <input type="file" name="photo" accept="image/*" class="sr-only"
                   @change="preview = URL.createObjectURL($event.target.files[0])">
            <span class="absolute bottom-0 end-0 w-5 h-5 bg-primary-500 rounded-full flex items-center justify-center">
              <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            </span>
          </label>
          <div>
            <p class="text-sm font-medium text-slate-700">Profile photo</p>
            <p class="text-xs text-slate-500">Optional · JPG or PNG, max 2 MB</p>
          </div>
        </div>

        {{-- Name --}}
        <label class="block">
          <span class="block text-sm font-medium text-slate-700 mb-1.5">Full name <span class="text-red-500">*</span></span>
          <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                 class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
          @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </label>

        {{-- Gender / DOB --}}
        <div class="grid grid-cols-2 gap-4">
          <label class="block">
            <span class="block text-sm font-medium text-slate-700 mb-1.5">Gender</span>
            <select name="gender" class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
              <option value="">Prefer not to say</option>
              <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>Male</option>
              <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
              <option value="other"  {{ old('gender', $user->gender) === 'other'  ? 'selected' : '' }}>Other</option>
            </select>
          </label>
          <label class="block">
            <span class="block text-sm font-medium text-slate-700 mb-1.5">Date of birth</span>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}"
                   max="{{ now()->subYears(16)->format('Y-m-d') }}"
                   class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
          </label>
        </div>

        {{-- Bio --}}
        <label class="block">
          <span class="block text-sm font-medium text-slate-700 mb-1.5">Short bio <span class="text-slate-400 font-normal">(optional)</span></span>
          <textarea name="bio" rows="2" maxlength="500"
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-slate-900 placeholder-slate-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition resize-none"
                    placeholder="A quick note about yourself…">{{ old('bio', $user->bio) }}</textarea>
        </label>

        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-primary-500 text-white font-semibold hover:bg-primary-600 active:bg-primary-700 transition">
          Continue
          <svg class="w-4 h-4 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>

      </form>
    </div>

  </div>
</div>
@endsection
