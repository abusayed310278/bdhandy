@extends('layouts.dashboard')
@section('title', 'Terminated Members')

@section('content')
<div class="space-y-6 text-sm">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-0.5">
        <a href="{{ route('business.team.index') }}" class="text-slate-400 hover:text-slate-600 transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-900">Terminated Members</h2>
      </div>
      <p class="text-slate-500 text-xs mt-0.5">Former team members whose access has been revoked</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('business.team.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Active Team
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
  @endif

  {{-- Stats --}}
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
      <p class="text-xs text-slate-500">Total Terminated</p>
      <p class="text-2xl font-black text-red-500 mt-1">{{ $members->total() }}</p>
    </div>
    <div class="bg-red-50 rounded-2xl border border-red-100 p-4 flex items-center gap-3">
      <svg class="w-8 h-8 text-red-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
      <p class="text-xs text-red-600 leading-relaxed">Terminated members cannot log in. Their incomplete jobs were automatically unassigned when terminated.</p>
    </div>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Member</th>
          <th class="px-4 py-3 text-start font-semibold">Role</th>
          <th class="px-4 py-3 text-start font-semibold">Services</th>
          <th class="px-4 py-3 text-start font-semibold">Terminated On</th>
          <th class="px-4 py-3 text-end font-semibold">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($members as $member)
        <tr class="hover:bg-slate-50 transition opacity-75">
          <td class="px-5 py-3">
            <div class="flex items-center gap-3">
              @if($member->profile_photo)
                <img src="{{ Storage::url($member->profile_photo) }}" class="w-8 h-8 rounded-full object-cover grayscale">
              @else
                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-xs">
                  {{ strtoupper(substr($member->full_name, 0, 2)) }}
                </div>
              @endif
              <div>
                <p class="font-semibold text-slate-600">{{ $member->full_name }}</p>
                <p class="text-[11px] text-slate-400">{{ $member->employee_code }} · {{ $member->designation ?: '—' }}</p>
              </div>
            </div>
          </td>
          <td class="px-4 py-3 text-slate-400">{{ $member->role?->role_name ?? '—' }}</td>
          <td class="px-4 py-3">
            <div class="flex flex-wrap gap-1">
              @forelse($member->services->take(3) as $ms)
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-400 text-[11px] font-medium">
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
          <td class="px-4 py-3 text-slate-400 text-[11px]">
            {{ $member->updated_at->format('d M Y') }}
          </td>
          <td class="px-4 py-3 text-end">
            <a href="{{ route('business.team.show', $member) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition inline-flex" title="View profile">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="px-5 py-12 text-center text-slate-400 italic">No terminated members.</td>
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
