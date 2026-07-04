@extends('layouts.dashboard')
@section('title', 'Equipment')

@section('content')
<div class="space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Equipment & Tools</h2>
      <p class="text-slate-500 text-xs mt-0.5">Track all company-owned tools and assets</p>
    </div>
    <a href="{{ route('business.equipment.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition shadow-soft">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Equipment
    </a>
  </div>

  @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif

  <div class="bg-white rounded-2xl border border-slate-200">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Equipment</th>
          <th class="px-4 py-3 text-start font-semibold">Code</th>
          <th class="px-4 py-3 text-start font-semibold">Assigned To</th>
          <th class="px-4 py-3 text-start font-semibold">Condition</th>
          <th class="px-4 py-3 text-start font-semibold">Status</th>
          <th class="px-4 py-3 text-end font-semibold">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($equipment as $eq)
        <tr class="hover:bg-slate-50 transition">
          <td class="px-5 py-3">
            <p class="font-semibold text-slate-900">{{ $eq->name }}</p>
            <p class="text-[11px] text-slate-400">{{ $eq->brand }} {{ $eq->model }}</p>
          </td>
          <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $eq->code }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $eq->currentAssignment?->member?->full_name ?? '—' }}</td>
          <td class="px-4 py-3">
            @php $cc=['new'=>'blue','good'=>'green','fair'=>'amber','needs_repair'=>'red','retired'=>'slate'][$eq->condition]??'slate'; @endphp
            <span class="px-2 py-0.5 rounded-full bg-{{ $cc }}-100 text-{{ $cc }}-700 text-[11px] font-semibold capitalize">{{ str_replace('_',' ',$eq->condition) }}</span>
          </td>
          <td class="px-4 py-3">
            @php $sc=['available'=>'green','assigned'=>'blue','in_maintenance'=>'amber','lost'=>'red','retired'=>'slate'][$eq->status]??'slate'; @endphp
            <span class="px-2 py-0.5 rounded-full bg-{{ $sc }}-100 text-{{ $sc }}-700 text-[11px] font-semibold capitalize">{{ str_replace('_',' ',$eq->status) }}</span>
          </td>
          <td class="px-4 py-3 text-end">
            <div class="flex items-center justify-end gap-1" x-data="{ assignOpen: false, returnOpen: false }">

              {{-- Assign (available only) --}}
              @if($eq->status === 'available')
              <div class="relative">
                <button @click="assignOpen = !assignOpen; returnOpen = false"
                        class="px-2.5 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-xs font-bold hover:bg-primary-100 transition">
                  Assign
                </button>
                <div x-show="assignOpen" @click.outside="assignOpen = false" x-transition
                     class="absolute end-0 mt-1 z-50 bg-white border border-slate-200 rounded-xl shadow-lg p-3 w-56 space-y-2" style="display:none">
                  <form action="{{ route('business.equipment.assign', $eq) }}" method="POST" class="space-y-2">
                    @csrf
                    <select name="team_member_id" required
                            class="w-full text-xs rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 focus:border-primary-500 outline-none appearance-none">
                      <option value="">Select member…</option>
                      @foreach($members as $m)
                      <option value="{{ $m->id }}">{{ $m->full_name }}</option>
                      @endforeach
                    </select>
                    <button type="submit" class="w-full px-3 py-1.5 rounded-lg bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition">
                      Confirm Assign
                    </button>
                  </form>
                </div>
              </div>
              @endif

              {{-- Return (assigned only) --}}
              @if($eq->status === 'assigned')
              <div class="relative">
                <button @click="returnOpen = !returnOpen; assignOpen = false"
                        class="px-2.5 py-1.5 rounded-lg bg-green-50 text-green-700 text-xs font-bold hover:bg-green-100 transition">
                  Return
                </button>
                <div x-show="returnOpen" @click.outside="returnOpen = false" x-transition
                     class="absolute end-0 mt-1 z-50 bg-white border border-slate-200 rounded-xl shadow-lg p-3 w-56 space-y-2" style="display:none">
                  <form action="{{ route('business.equipment.return', $eq) }}" method="POST" class="space-y-2">
                    @csrf
                    <select name="returned_condition" required
                            class="w-full text-xs rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 focus:border-primary-500 outline-none appearance-none">
                      <option value="good">Good condition</option>
                      <option value="damaged">Damaged</option>
                      <option value="lost">Lost</option>
                    </select>
                    <input type="text" name="return_notes" placeholder="Notes (optional)"
                           class="w-full text-xs rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 focus:border-primary-500 outline-none">
                    <button type="submit" class="w-full px-3 py-1.5 rounded-lg bg-green-500 text-white text-xs font-bold hover:bg-green-600 transition">
                      Confirm Return
                    </button>
                  </form>
                </div>
              </div>
              <form action="{{ route('business.equipment.lost', $eq) }}" method="POST"
                    onsubmit="return confirm('Mark as lost?')">
                @csrf
                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100 transition">
                  Lost
                </button>
              </form>
              @endif

              {{-- Edit --}}
              <a href="{{ route('business.equipment.edit', $eq) }}"
                 class="p-1.5 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Edit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <a href="{{ route('business.equipment.maintenance.index', $eq) }}"
                 class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition" title="Maintenance">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
              </a>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-12 text-center text-slate-400 italic">No equipment added yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($equipment->hasPages())<div>{{ $equipment->links() }}</div>@endif
</div>
@endsection
