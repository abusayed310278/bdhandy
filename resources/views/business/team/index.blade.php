@extends('layouts.dashboard')
@section('title', 'Team Members')

@section('content')
<div class="space-y-6 text-sm">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Team Members</h2>
      <p class="text-slate-500 text-xs mt-0.5">Manage your field technicians and staff</p>
    </div>
    <div class="flex items-center gap-2">
      @if($terminatedCount > 0)
      <a href="{{ route('business.team.terminated') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 transition shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
        Terminated ({{ $terminatedCount }})
      </a>
      @endif
      <a href="{{ route('business.team.roles.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Roles
      </a>
      <a href="{{ route('business.team.invite') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition shadow-soft">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Member
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
  @endif

  {{-- Stats bar --}}
  @php
    $counts = $members->getCollection()->groupBy('status');
    $active = ($counts['active'] ?? collect())->count();
    $inactive = $members->total() - $active;
  @endphp
  <div class="grid grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Total (active &amp; inactive)</p>
      <p class="text-2xl font-black text-slate-900 mt-1">{{ $members->total() }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Active</p>
      <p class="text-2xl font-black text-green-600 mt-1">{{ $active }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Inactive / Suspended</p>
      <p class="text-2xl font-black text-slate-400 mt-1">{{ $inactive }}</p>
    </div>
  </div>

  {{-- Member table --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Member</th>
          <th class="px-4 py-3 text-start font-semibold">Role</th>
          <th class="px-4 py-3 text-start font-semibold">Services</th>
          <th class="px-4 py-3 text-start font-semibold">Renewal Date</th>
          <th class="px-4 py-3 text-start font-semibold">Status</th>
          <th class="px-4 py-3 text-end font-semibold">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($members as $member)
        <tr class="hover:bg-slate-50 transition">
          <td class="px-5 py-3">
            <div class="flex items-center gap-3">
              @if($member->profile_photo)
                <img src="{{ Storage::url($member->profile_photo) }}" class="w-8 h-8 rounded-full object-cover">
              @else
                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-xs">
                  {{ strtoupper(substr($member->full_name, 0, 2)) }}
                </div>
              @endif
              <div>
                <p class="font-semibold text-slate-900">{{ $member->full_name }}</p>
                <p class="text-[11px] text-slate-400">{{ $member->employee_code }} · {{ $member->designation ?: '—' }}</p>
              </div>
            </div>
          </td>
          <td class="px-4 py-3 text-slate-600">{{ $member->role?->role_name ?? '—' }}</td>
          <td class="px-4 py-3">
            <div class="flex flex-wrap gap-1">
              @forelse($member->services->take(3) as $ms)
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-medium">
                  {{ $ms->service?->getTranslation('translations', 'en')['name'] ?? $ms->service?->slug ?? '?' }}
                </span>
              @empty
                <span class="text-slate-400">—</span>
              @endforelse
              @if($member->services->count() > 3)
                <span class="text-[11px] text-slate-400">+{{ $member->services->count() - 3 }}</span>
              @endif
            </div>
          </td>
          <td class="px-4 py-3">
            @if($member->renewal_date)
              @php
                $days = now()->startOfDay()->diffInDays($member->renewal_date->startOfDay(), false);
              @endphp
              <div>
                <p class="font-medium text-slate-700 text-xs">{{ $member->renewal_date->format('d M Y') }}</p>
                <div class="mt-0.5">
                  @if($days < 0)
                    <span class="inline-flex items-center text-[10px] font-bold text-red-700 bg-red-50 px-1.5 py-0.5 rounded">
                      Expired {{ abs($days) }} {{ Str::plural('day', abs($days)) }} ago
                    </span>
                  @elseif($days == 0)
                    <span class="inline-flex items-center text-[10px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded animate-pulse">
                      Expires today
                    </span>
                  @elseif($days <= 30)
                    <span class="inline-flex items-center text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">
                      {{ $days }} {{ Str::plural('day', $days) }} left
                    </span>
                  @else
                    <span class="inline-flex items-center text-[10px] font-bold text-green-700 bg-green-50 px-1.5 py-0.5 rounded">
                      {{ $days }} {{ Str::plural('day', $days) }} left
                    </span>
                  @endif
                </div>
              </div>
            @else
              <span class="text-slate-400">—</span>
            @endif
          </td>
          <td class="px-4 py-3">
            @php
              $colors = ['active'=>'green','inactive'=>'slate','suspended'=>'amber','terminated'=>'red'];
              $c = $colors[$member->status] ?? 'slate';
            @endphp
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700 text-[11px] font-semibold capitalize">
              <span class="w-1.5 h-1.5 rounded-full bg-{{ $c }}-500"></span>
              {{ $member->status }}
            </span>
          </td>
          <td class="px-4 py-3 text-end">
            <div class="flex items-center justify-end gap-1">
              <a href="{{ route('business.team.show', $member) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </a>
              <a href="{{ route('business.team.edit', $member) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              @if($member->status !== 'terminated')
              <form action="{{ route('business.team.terminate', $member) }}" method="POST" onsubmit="return confirm('Terminate {{ $member->full_name }}? All incomplete jobs will be unassigned.')">
                @csrf
                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition" title="Terminate member">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="px-5 py-12 text-center text-slate-400 italic">No team members yet. <a href="{{ route('business.team.invite') }}" class="text-primary-600 font-semibold">Add your first member</a>.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($members->hasPages())
    <div>{{ $members->links() }}</div>
  @endif

</div>
@endsection
