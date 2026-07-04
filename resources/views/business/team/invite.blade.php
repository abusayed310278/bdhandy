@extends('layouts.dashboard')
@section('title', 'Add Team Member')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 text-sm">

  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Add Team Member</h2>
      <p class="text-slate-500 text-xs mt-0.5">Invite a technician or staff member to your team</p>
    </div>
    <a href="{{ route('business.team.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back
    </a>
  </div>

  <form action="{{ route('business.team.invite.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- Identity --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
      <h3 class="font-semibold text-slate-900 text-sm uppercase tracking-wider">Personal Information</h3>

      <div class="grid sm:grid-cols-2 gap-5">
        <div class="sm:col-span-2">
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
          <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="e.g. Rahim Uddin"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          @error('full_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Phone <span class="text-red-500">*</span></label>
          <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+880 1X XXXX XXXX"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
          <input type="email" name="email" value="{{ old('email') }}" required placeholder="member@example.com"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          <p class="text-[11px] text-slate-400 mt-1">Used for login — credentials/invite will be sent here.</p>
          @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Designation</label>
          <input type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. Senior AC Technician"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Joining Date</label>
          <input type="date" name="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Renewal Date</label>
          <input type="date" name="renewal_date" value="{{ old('renewal_date') }}"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          @error('renewal_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- Identification --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
      <h3 class="font-semibold text-slate-900 text-sm uppercase tracking-wider">Identification Documents</h3>
      <div class="grid sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">NID Number</label>
          <input type="text" name="nid_number" value="{{ old('nid_number') }}" placeholder="National ID number"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          @error('nid_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">NID Photo</label>
          <input type="file" name="nid_photo" accept="image/*"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 outline-none transition">
          @error('nid_photo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Passport Number</label>
          <input type="text" name="passport_number" value="{{ old('passport_number') }}" placeholder="Passport number"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          @error('passport_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Passport Photo</label>
          <input type="file" name="passport_photo" accept="image/*"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 outline-none transition">
          @error('passport_photo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- Role & Compensation --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5"
         x-data="{ compType: '{{ old('compensation_type', 'salary') }}' }">
      <h3 class="font-semibold text-slate-900 text-sm uppercase tracking-wider">Role & Compensation</h3>
      <div class="grid sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Role</label>
          <select name="team_role_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
            <option value="">— No role —</option>
            @foreach($roles as $role)
              <option value="{{ $role->id }}" @selected(old('team_role_id') == $role->id)>{{ $role->role_name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Compensation Type <span class="text-red-500">*</span></label>
          <select name="compensation_type" required x-model="compType"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
            <option value="salary">Salary</option>
            <option value="commission">Commission</option>
            <option value="hybrid">Hybrid</option>
          </select>
        </div>
      </div>

      {{-- Salary fields --}}
      <div x-show="compType === 'salary' || compType === 'hybrid'" class="space-y-4 pt-1 border-t border-slate-100">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider pt-2">Salary Details</p>
        <div class="grid sm:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Monthly Base Salary</label>
            <input type="number" name="base_salary_monthly" value="{{ old('base_salary_monthly') }}" min="0" step="0.01" placeholder="0.00"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
            @error('base_salary_monthly') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Salary Currency</label>
            <select name="salary_currency_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
              @foreach($currencies as $cur)
              <option value="{{ $cur->id }}" @selected(old('salary_currency_id') == $cur->id)>{{ $cur->symbol }} {{ $cur->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      {{-- Commission fields --}}
      <div x-show="compType === 'commission' || compType === 'hybrid'" class="space-y-4 pt-1 border-t border-slate-100">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider pt-2">Commission Details</p>
        <div class="grid sm:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Commission Type</label>
            <select name="commission_type" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
              <option value="">— Select —</option>
              <option value="fixed_per_job" @selected(old('commission_type') == 'fixed_per_job')>Fixed per Job</option>
              <option value="percentage"    @selected(old('commission_type') == 'percentage')>Percentage of Job Value</option>
              <option value="tiered"        @selected(old('commission_type') == 'tiered')>Tiered</option>
            </select>
            @error('commission_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Commission Value</label>
            <input type="number" name="commission_value" value="{{ old('commission_value') }}" min="0" step="0.01" placeholder="e.g. 500 or 10"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
            @error('commission_value') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Commission Currency</label>
            <select name="commission_currency_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
              @foreach($currencies as $cur)
              <option value="{{ $cur->id }}" @selected(old('commission_currency_id') == $cur->id)>{{ $cur->symbol }} {{ $cur->name }}</option>
              @endforeach
            </select>
          </div>
          <div x-show="compType === 'hybrid'">
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Weekly Guarantee Amount</label>
            <input type="number" name="weekly_guarantee_amount" value="{{ old('weekly_guarantee_amount') }}" min="0" step="0.01" placeholder="0.00"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          </div>
        </div>
      </div>

      {{-- Payment frequency --}}
      <div class="grid sm:grid-cols-2 gap-5 pt-1 border-t border-slate-100">
        <div class="pt-2">
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Payment Frequency</label>
          <select name="payment_frequency" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 outline-none transition appearance-none">
            <option value="monthly"   @selected(old('payment_frequency','monthly') == 'monthly')>Monthly</option>
            <option value="biweekly"  @selected(old('payment_frequency') == 'biweekly')>Bi-weekly</option>
            <option value="weekly"    @selected(old('payment_frequency') == 'weekly')>Weekly</option>
          </select>
        </div>
      </div>
    </div>

    {{-- Login Setup --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4"
         x-data="{ method: '{{ old('invite_method', 'email_link') }}' }">
      <h3 class="font-semibold text-slate-900 text-sm uppercase tracking-wider">Login Setup</h3>
      <p class="text-xs text-slate-500">Choose how this team member will get their login credentials.</p>

      <div class="grid sm:grid-cols-2 gap-3">
        <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition"
               :class="method === 'email_link' ? 'border-primary-300 bg-primary-50' : 'border-slate-200 bg-white hover:border-primary-200'">
          <input type="radio" name="invite_method" value="email_link" x-model="method" class="mt-0.5 w-4 h-4 text-primary-500 border-slate-300 focus:ring-primary-500">
          <div class="flex-1">
            <p class="font-semibold text-slate-900 text-sm">Email Setup Link</p>
            <p class="text-[11px] text-slate-500 mt-0.5">System sends a branded invite email; the member clicks the link to set their own password.</p>
          </div>
        </label>

        <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition"
               :class="method === 'manual_password' ? 'border-primary-300 bg-primary-50' : 'border-slate-200 bg-white hover:border-primary-200'">
          <input type="radio" name="invite_method" value="manual_password" x-model="method" class="mt-0.5 w-4 h-4 text-primary-500 border-slate-300 focus:ring-primary-500">
          <div class="flex-1">
            <p class="font-semibold text-slate-900 text-sm">Manual Password</p>
            <p class="text-[11px] text-slate-500 mt-0.5">You set a password now; we email the credentials to the member and they can change it later.</p>
          </div>
        </label>
      </div>

      <div x-show="method === 'manual_password'" x-cloak class="grid sm:grid-cols-2 gap-5 pt-2">
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Initial Password <span class="text-red-500">*</span></label>
          <input type="text" name="manual_password" value="{{ old('manual_password') }}" minlength="8" maxlength="50"
            placeholder="At least 8 characters"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition font-mono text-sm">
          <p class="text-[11px] text-slate-400 mt-1">Will be emailed to the member along with login URL.</p>
          @error('manual_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-end">
          <button type="button" onclick="this.previousElementSibling.querySelector('input').value = Math.random().toString(36).slice(-10) + 'A1!'"
            class="text-xs text-primary-600 hover:text-primary-700 font-semibold underline-offset-2 hover:underline">
            🎲 Generate random
          </button>
        </div>
      </div>
    </div>

    {{-- Service Skills --}}
    @if($services->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4"
         x-data="{ selected: {{ Js::from(old('service_ids', [])) }}, primary: {{ old('primary_service', 'null') }} }">
      <h3 class="font-semibold text-slate-900 text-sm uppercase tracking-wider">Service Skills</h3>
      <p class="text-xs text-slate-500">Select which services this member can handle. Mark one as primary specialty.</p>

      <div class="space-y-2">
        @foreach($services as $service)
        @php $name = $service->getTranslation('translations', 'en')['name'] ?? $service->slug; @endphp
        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-primary-200 transition"
             :class="selected.includes({{ $service->id }}) ? 'bg-primary-50 border-primary-200' : 'bg-white'">
          <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" id="svc_{{ $service->id }}"
            x-model="selected" class="w-4 h-4 text-primary-500 rounded border-slate-300">
          <label for="svc_{{ $service->id }}" class="flex-1 font-medium text-slate-700 cursor-pointer">{{ $name }}</label>

          <template x-if="selected.includes({{ $service->id }})">
            <div class="flex items-center gap-3">
              <select name="skill_levels[{{ $service->id }}]" class="text-xs rounded-lg border border-slate-200 bg-white px-2 py-1 outline-none">
                <option value="junior">Junior</option>
                <option value="mid" selected>Mid</option>
                <option value="senior">Senior</option>
              </select>
              <label class="flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer">
                <input type="radio" name="primary_service" value="{{ $service->id }}"
                  x-model="primary" class="w-3.5 h-3.5 text-primary-500">
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
      <a href="{{ route('business.team.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Cancel</a>
      <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">Add Member</button>
    </div>
  </form>
</div>
@endsection
