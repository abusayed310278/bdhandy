@extends('layouts.dashboard')
@section('title', 'Edit — ' . $member->full_name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6 text-sm">

  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Edit Member</h2>
      <p class="text-slate-500 text-xs mt-0.5">{{ $member->full_name }} · {{ $member->employee_code }}</p>
    </div>
    <a href="{{ route('business.team.show', $member) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back
    </a>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
  @endif

  <form action="{{ route('business.team.update', $member) }}" method="POST" class="space-y-6">
    @csrf @method('PUT')

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
      <h3 class="font-semibold text-slate-900 text-sm uppercase tracking-wider">Personal Information</h3>
      <div class="grid sm:grid-cols-2 gap-5">
        <div class="sm:col-span-2">
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
          <input type="text" name="full_name" value="{{ old('full_name', $member->full_name) }}" required
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          @error('full_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Phone <span class="text-red-500">*</span></label>
          <input type="tel" name="phone" value="{{ old('phone', $member->phone) }}" required
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email</label>
          <input type="email" name="email" value="{{ old('email', $member->email) }}"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Designation</label>
          <input type="text" name="designation" value="{{ old('designation', $member->designation) }}"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Joining Date</label>
          <input type="date" name="joining_date" value="{{ old('joining_date', $member->joining_date?->format('Y-m-d')) }}"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Renewal Date</label>
          <input type="date" name="renewal_date" value="{{ old('renewal_date', $member->renewal_date?->format('Y-m-d')) }}"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          @error('renewal_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status <span class="text-red-500">*</span></label>
          <select name="status" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
            @foreach(['active','inactive','suspended','terminated'] as $s)
              <option value="{{ $s }}" @selected(old('status', $member->status) === $s)>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
      <h3 class="font-semibold text-slate-900 text-sm uppercase tracking-wider">Role & Compensation</h3>
      <div class="grid sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Role</label>
          <select name="team_role_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
            <option value="">— No role —</option>
            @foreach($roles as $role)
              <option value="{{ $role->id }}" @selected(old('team_role_id', $member->team_role_id) == $role->id)>{{ $role->role_name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Compensation Type <span class="text-red-500">*</span></label>
          <select name="compensation_type" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
            @foreach(['salary','commission','hybrid'] as $ct)
              <option value="{{ $ct }}" @selected(old('compensation_type', $member->compensation_type) === $ct)>{{ ucfirst($ct) }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    @if($services->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4"
         x-data="{ selected: {{ Js::from(old('service_ids', array_keys($memberServiceIds))) }}, primary: {{ old('primary_service', $primaryServiceId ?? 'null') }} }">
      <h3 class="font-semibold text-slate-900 text-sm uppercase tracking-wider">Service Skills</h3>
      <div class="space-y-2">
        @foreach($services as $service)
        @php $name = $service->getTranslation('translations', 'en')['name'] ?? $service->slug; @endphp
        <div class="flex items-center gap-3 p-3 rounded-xl border transition"
             :class="selected.includes({{ $service->id }}) ? 'bg-primary-50 border-primary-200' : 'bg-white border-slate-100'">
          <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" id="svc_{{ $service->id }}"
            x-model="selected" class="w-4 h-4 text-primary-500 rounded border-slate-300">
          <label for="svc_{{ $service->id }}" class="flex-1 font-medium text-slate-700 cursor-pointer">{{ $name }}</label>
          <template x-if="selected.includes({{ $service->id }})">
            <div class="flex items-center gap-3">
              <select name="skill_levels[{{ $service->id }}]" class="text-xs rounded-lg border border-slate-200 bg-white px-2 py-1 outline-none">
                @foreach(['junior','mid','senior'] as $sl)
                <option value="{{ $sl }}" {{ ($skillLevels[$service->id] ?? 'mid') === $sl ? 'selected' : '' }}>{{ ucfirst($sl) }}</option>
                @endforeach
              </select>
              <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer">
                <input type="radio" name="primary_service" value="{{ $service->id }}" x-model="primary" class="w-3.5 h-3.5 text-primary-500">
                Primary
              </label>
            </div>
          </template>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    <div class="flex justify-end gap-3">
      <a href="{{ route('business.team.show', $member) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Cancel</a>
      <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">Save Changes</button>
    </div>
  </form>
</div>
@endsection
