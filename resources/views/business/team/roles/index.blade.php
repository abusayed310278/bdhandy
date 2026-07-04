@extends('layouts.dashboard')
@section('title', 'Team Roles')

@section('content')
<div class="space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Team Roles</h2>
      <p class="text-slate-500 text-xs mt-0.5">Define permission sets for your team members</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('business.team.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to Team
      </a>
      <a href="{{ route('business.team.roles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition shadow-soft">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Create Role
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
  @endif

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($roles as $role)
    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
      <div class="flex items-start justify-between">
        <div>
          <h3 class="font-bold text-slate-900">{{ $role->role_name }}</h3>
          <p class="text-xs text-slate-400 mt-0.5">{{ $role->members_count }} member{{ $role->members_count != 1 ? 's' : '' }}</p>
        </div>
        @if($role->is_default)
          <span class="px-2 py-0.5 rounded-full bg-primary-100 text-primary-700 text-[10px] font-bold uppercase">Default</span>
        @endif
      </div>

      {{-- Permission summary --}}
      <div class="flex flex-wrap gap-1">
        @foreach($role->permissions as $group => $perms)
          @php $enabled = collect($perms)->filter()->count(); @endphp
          @if($enabled > 0)
          <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-600 text-[11px] font-medium capitalize">{{ $group }} ({{ $enabled }})</span>
          @endif
        @endforeach
      </div>

      <div class="flex items-center gap-2 pt-2 border-t border-slate-50">
        <a href="{{ route('business.team.roles.edit', $role) }}" class="flex-1 text-center py-1.5 rounded-xl bg-slate-50 text-slate-600 hover:bg-primary-50 hover:text-primary-600 text-xs font-semibold transition">Edit</a>
        @if($role->members_count == 0)
        <form action="{{ route('business.team.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Delete this role?')">
          @csrf @method('DELETE')
          <button class="px-3 py-1.5 rounded-xl bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-600 text-xs font-semibold transition">Delete</button>
        </form>
        @endif
      </div>
    </div>
    @empty
    <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-dashed border-slate-200">
      <p class="text-slate-400 italic">No roles yet. <a href="{{ route('business.team.roles.create') }}" class="text-primary-600 font-semibold">Create your first role</a>.</p>
    </div>
    @endforelse
  </div>
</div>
@endsection
