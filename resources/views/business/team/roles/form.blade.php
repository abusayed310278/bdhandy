@extends('layouts.dashboard')
@section('title', isset($role) ? 'Edit Role' : 'Create Role')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-bold text-slate-900">{{ isset($role) ? 'Edit Role' : 'Create Role' }}</h2>
    <a href="{{ route('business.team.roles.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back
    </a>
  </div>

  <form action="{{ isset($role) ? route('business.team.roles.update', $role) : route('business.team.roles.store') }}" method="POST" class="space-y-6">
    @csrf @if(isset($role)) @method('PUT') @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
      <div class="grid sm:grid-cols-2 gap-5">
        <div class="sm:col-span-2">
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">Role Name <span class="text-red-500">*</span></label>
          <input type="text" name="role_name" value="{{ old('role_name', $role->role_name ?? '') }}" required
            placeholder="e.g. Senior Technician"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          @error('role_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
          <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:bg-slate-50 cursor-pointer">
            <input type="checkbox" name="is_default" value="1" {{ old('is_default', $role->is_default ?? false) ? 'checked' : '' }} class="w-4 h-4 text-primary-500 rounded border-slate-300">
            <div>
              <p class="font-semibold text-slate-800">Default Role</p>
              <p class="text-[11px] text-slate-400">Automatically assigned to new members</p>
            </div>
          </label>
        </div>
      </div>
    </div>

    {{-- Permissions --}}
    @php
    $permGroups = [
      'jobs'       => ['view_assigned', 'view_all_team_jobs', 'accept_reject', 'update_status', 'reassign'],
      'attendance' => ['clock_in_out', 'view_own_history', 'view_team_attendance', 'edit_attendance'],
      'schedule'   => ['view_daily_schedule', 'request_changes', 'create_schedule', 'optimize_route'],
      'earnings'   => ['view_own_earnings', 'view_team_earnings'],
      'profile'    => ['edit_own_profile', 'edit_team_member_profiles', 'invite_members', 'terminate_members'],
      'reports'    => ['view_own_performance', 'view_team_performance', 'export_reports'],
      'equipment'  => ['view_assigned_equipment', 'report_lost', 'manage_equipment'],
      'inventory'  => ['log_material_usage', 'view_inventory', 'manage_inventory'],
      'vehicles'   => ['view_assigned_vehicle', 'log_fuel', 'manage_vehicles'],
    ];
    $existing = old('permissions', $role->permissions ?? []);
    @endphp

    @foreach($permGroups as $group => $perms)
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
      <h4 class="font-semibold text-slate-900 capitalize mb-4">{{ $group }}</h4>
      <div class="grid sm:grid-cols-2 gap-2">
        @foreach($perms as $perm)
        <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 cursor-pointer">
          <input type="checkbox" name="permissions[{{ $group }}][{{ $perm }}]" value="1"
            {{ ($existing[$group][$perm] ?? false) ? 'checked' : '' }}
            class="w-4 h-4 text-primary-500 rounded border-slate-300">
          <span class="text-xs text-slate-700">{{ str_replace('_', ' ', $perm) }}</span>
        </label>
        @endforeach
      </div>
    </div>
    @endforeach

    <div class="flex justify-end gap-3">
      <a href="{{ route('business.team.roles.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200 transition">Cancel</a>
      <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">
        {{ isset($role) ? 'Save Changes' : 'Create Role' }}
      </button>
    </div>
  </form>
</div>
@endsection
