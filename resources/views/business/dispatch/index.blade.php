@extends('layouts.dashboard')
@section('title', 'Job Dispatch')

@section('content')
<div class="space-y-6 text-sm">
  <div>
    <h2 class="text-xl font-bold text-slate-900">Job Dispatch</h2>
    <p class="text-slate-500 text-xs mt-0.5">Assign incoming service requests to your team members</p>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>
  @endif

  <div class="grid lg:grid-cols-2 gap-6">

    {{-- Unassigned jobs --}}
    <div class="space-y-3">
      <h3 class="font-bold text-slate-700 uppercase text-xs tracking-wider">Unassigned Jobs ({{ $unassigned->count() }})</h3>
      @forelse($unassigned as $job)
      <div class="bg-white rounded-2xl border border-slate-200 p-4 space-y-3">
        <div class="flex items-start justify-between">
          <div>
            <p class="font-bold text-slate-900">
              <a href="{{ route('provider.requests.show', $job) }}" class="hover:text-primary-600 hover:underline transition">
                {{ $job->request_number }}
              </a>
            </p>
            <p class="text-xs text-slate-500 mt-0.5">{{ $job->service?->getTranslation('translations','en')['name'] ?? '—' }}</p>
          </div>
          @php $uc=['normal'=>'slate','urgent'=>'amber','emergency'=>'red'][$job->urgency??'normal']??'slate'; @endphp
          <span class="px-2 py-0.5 rounded-full bg-{{ $uc }}-100 text-{{ $uc }}-700 text-[10px] font-bold uppercase">{{ $job->urgency ?? 'normal' }}</span>
        </div>
        <p class="text-xs text-slate-500">{{ $job->address ?? 'No address' }}</p>
        <form action="{{ route('business.dispatch.assign', $job) }}" method="POST" class="flex items-center gap-2">
          @csrf
          <select name="team_member_id" required class="flex-1 text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 focus:border-primary-500 outline-none appearance-none">
            <option value="">Select member...</option>
            @foreach($members as $m)
            <option value="{{ $m->id }}">{{ $m->full_name }} ({{ $m->assignments->count() }} active)</option>
            @endforeach
          </select>
          <input type="datetime-local" name="scheduled_start_time" required class="text-xs rounded-xl border border-slate-200 bg-slate-50 px-2 py-2 focus:border-primary-500 outline-none">
          <button class="px-3 py-2 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition">Assign</button>
        </form>
      </div>
      @empty
      <div class="bg-white rounded-2xl border border-dashed border-slate-200 py-10 text-center text-slate-400 italic">All jobs assigned.</div>
      @endforelse
    </div>

    {{-- Team load --}}
    <div class="space-y-3">
      <h3 class="font-bold text-slate-700 uppercase text-xs tracking-wider">Team Load</h3>
      @foreach($members as $member)
      <div class="bg-white rounded-2xl border border-slate-200 p-4">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-xs">
            {{ strtoupper(substr($member->full_name, 0, 2)) }}
          </div>
          <div class="flex-1">
            <p class="font-semibold text-slate-900">{{ $member->full_name }}</p>
            <p class="text-xs text-slate-400">{{ $member->employee_code }}</p>
          </div>
          <span class="px-2.5 py-1 rounded-xl {{ $member->assignments->count() >= 3 ? 'bg-red-100 text-red-700' : ($member->assignments->count() > 0 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }} text-xs font-bold">
            {{ $member->assignments->count() }} active
          </span>
        </div>
        @if($member->assignments->isNotEmpty())
        <div class="mt-3 space-y-1.5">
          @foreach($member->assignments as $a)
          <div class="flex items-center gap-2 text-xs text-slate-500">
            <span class="w-1.5 h-1.5 rounded-full bg-primary-400 shrink-0"></span>
            <span class="flex-1">
              @if($a->request)
                <a href="{{ route('provider.requests.show', $a->request) }}" class="hover:text-primary-600 hover:underline transition">
                  {{ $a->request->request_number }}
                </a>
              @else
                —
              @endif
              @if($a->scheduled_start_time) · {{ $a->scheduled_start_time->format('d M Y, H:i') }} @endif
            </span>
            @if($a->status === 'assigned')
            <form action="{{ route('business.dispatch.unassign', $a) }}" method="POST"
                  onsubmit="return confirm('Remove this assignment? The job will return to the unassigned queue.')">
              @csrf
              <button type="submit"
                      class="px-2 py-0.5 rounded-lg bg-red-50 text-red-600 text-[10px] font-bold hover:bg-red-100 transition">
                Unassign
              </button>
            </form>
            @endif
          </div>
          @endforeach
        </div>
        @endif
      </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
