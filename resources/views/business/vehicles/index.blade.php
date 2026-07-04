@extends('layouts.dashboard')
@section('title', 'Vehicles')

@section('content')
<div class="space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Vehicle Fleet</h2>
      <p class="text-slate-500 text-xs mt-0.5">Company vehicles and assignments</p>
    </div>
    <a href="{{ route('business.vehicles.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition shadow-soft">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Vehicle
    </a>
  </div>

  @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($vehicles as $vehicle)
    <div x-data="{ showAssign: false, showReturn: false }" class="bg-white rounded-2xl border {{ $vehicle->isExpiringSoon() ? 'border-amber-300' : 'border-slate-200' }} overflow-hidden">
      <div class="p-5 space-y-3">
        <div class="flex items-start justify-between">
          <div>
            <p class="font-bold text-slate-900">{{ $vehicle->plate_number }}</p>
            <p class="text-xs text-slate-500 mt-0.5">{{ $vehicle->make }} {{ $vehicle->model }} · {{ $vehicle->year }}</p>
          </div>
          @php $sc=['available'=>'green','assigned'=>'blue','in_maintenance'=>'amber','retired'=>'slate'][$vehicle->status]??'slate'; @endphp
          <span class="px-2 py-0.5 rounded-full bg-{{ $sc }}-100 text-{{ $sc }}-700 text-[11px] font-semibold capitalize">{{ str_replace('_',' ',$vehicle->status) }}</span>
        </div>

        <div x-show="!showAssign && !showReturn" class="space-y-3">
          <div class="flex items-center gap-2 text-xs text-slate-500">
            <span class="capitalize">{{ $vehicle->vehicle_type }}</span>
            <span class="text-slate-300">·</span>
            <span class="capitalize">{{ $vehicle->fuel_type }}</span>
            @if($vehicle->current_odometer_km)<span class="text-slate-300">·</span><span>{{ number_format($vehicle->current_odometer_km) }} km</span>@endif
          </div>

          @if($vehicle->currentAssignment)
          <div class="text-xs p-2.5 rounded-xl bg-slate-50 border border-slate-100">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Assigned to</p>
            <p class="font-semibold text-slate-700 mt-0.5">{{ $vehicle->currentAssignment->member?->full_name }}</p>
            @if($vehicle->currentAssignment->assigned_at)
              <p class="text-[10px] text-slate-400 mt-0.5">Since {{ $vehicle->currentAssignment->assigned_at->format('d M Y, H:i') }}</p>
            @endif
          </div>
          @endif

          @if($vehicle->isExpiringSoon())
          <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-50 border border-amber-100">
            <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
            <p class="text-[11px] text-amber-700 font-semibold">Expiry alert within 30 days</p>
          </div>
          @endif

          <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
            @if($vehicle->status === 'available')
              <button @click="showAssign = true" class="flex-1 text-center py-1.5 rounded-xl bg-primary-500 text-white hover:bg-primary-600 text-xs font-semibold transition shadow-soft">Assign</button>
            @elseif($vehicle->status === 'assigned')
              <button @click="showReturn = true" class="flex-1 text-center py-1.5 rounded-xl bg-green-600 text-white hover:bg-green-700 text-xs font-semibold transition shadow-soft">Return</button>
            @endif
            <a href="{{ route('business.vehicles.edit', $vehicle) }}" class="px-3 py-1.5 rounded-xl bg-slate-50 text-slate-600 hover:bg-primary-50 hover:text-primary-600 text-xs font-semibold transition border border-slate-100">Edit</a>
            <a href="{{ route('business.vehicles.fuel.index', $vehicle) }}" class="px-2.5 py-1.5 rounded-xl bg-slate-50 text-slate-500 hover:bg-amber-50 hover:text-amber-600 text-xs font-semibold transition" title="Fuel logs">Fuel</a>
            <a href="{{ route('business.vehicles.maintenance.index', $vehicle) }}" class="px-2.5 py-1.5 rounded-xl bg-slate-50 text-slate-500 hover:bg-blue-50 hover:text-blue-600 text-xs font-semibold transition" title="Service logs">Service</a>
          </div>
        </div>

        <!-- INLINE ASSIGNMENT FORM -->
        <div x-show="showAssign" x-cloak class="pt-2 border-t border-slate-100 space-y-3">
          <p class="font-bold text-slate-700 text-xs uppercase tracking-wider">Assign Vehicle</p>
          <form action="{{ route('business.vehicles.assign', $vehicle) }}" method="POST" class="space-y-3">
            @csrf
            <div>
              <label class="block text-[10px] font-semibold text-slate-500 mb-1">Select Driver *</label>
              <select name="team_member_id" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-xs outline-none focus:border-primary-500 transition appearance-none">
                <option value="">— Select Member —</option>
                @foreach($teamMembers as $member)
                  <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->designation ?: 'Staff' }})</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-[10px] font-semibold text-slate-500 mb-1">Odometer (km)</label>
              <input type="number" name="odometer" value="{{ $vehicle->current_odometer_km }}" step="1" min="0"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-xs outline-none focus:border-primary-500 transition">
            </div>
            <div class="flex gap-2">
              <button type="submit" class="flex-1 py-1.5 rounded-lg bg-primary-500 text-white font-bold text-xs hover:bg-primary-600 transition shadow-soft">Confirm</button>
              <button type="button" @click="showAssign = false" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition">Cancel</button>
            </div>
          </form>
        </div>

        <!-- INLINE RETURN FORM -->
        <div x-show="showReturn" x-cloak class="pt-2 border-t border-slate-100 space-y-3">
          <p class="font-bold text-slate-700 text-xs uppercase tracking-wider">Return Vehicle</p>
          <form action="{{ route('business.vehicles.return', $vehicle) }}" method="POST" class="space-y-3">
            @csrf
            <div>
              <label class="block text-[10px] font-semibold text-slate-500 mb-1">Current Odometer (km) *</label>
              <input type="number" name="odometer" value="{{ $vehicle->current_odometer_km }}" required step="1" min="{{ $vehicle->current_odometer_km }}"
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-xs outline-none focus:border-primary-500 transition">
              <p class="text-[9px] text-slate-400 mt-0.5">Must be at least {{ number_format($vehicle->current_odometer_km) }} km</p>
            </div>
            <div>
              <label class="block text-[10px] font-semibold text-slate-500 mb-1">Return Notes</label>
              <textarea name="notes" rows="1" placeholder="Condition, issues, etc."
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-xs outline-none focus:border-primary-500 transition resize-none"></textarea>
            </div>
            <div class="flex gap-2">
              <button type="submit" class="flex-1 py-1.5 rounded-lg bg-green-600 text-white font-bold text-xs hover:bg-green-700 transition shadow-soft">Confirm Return</button>
              <button type="button" @click="showReturn = false" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition">Cancel</button>
            </div>
          </form>
        </div>

      </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-2xl border border-dashed border-slate-200 py-12 text-center text-slate-400 italic">No vehicles added yet.</div>
    @endforelse
  </div>
  @if($vehicles->hasPages())<div>{{ $vehicles->links() }}</div>@endif

  {{-- History Report --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden mt-8">
    <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h3 class="text-base font-bold text-slate-900">Vehicle Assignment History Report</h3>
        <p class="text-slate-500 text-xs mt-0.5">Historical logs of all driver assignments, return dates, and odometer readings</p>
      </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('business.vehicles.index') }}" class="p-5 bg-slate-50/50 border-b border-slate-100 grid sm:grid-cols-4 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Filter by Vehicle</label>
        <select name="vehicle_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs outline-none focus:border-primary-500">
          <option value="">All Vehicles</option>
          @foreach($allVehiclesForFilter as $v)
            <option value="{{ $v->id }}" @selected(request('vehicle_id') == $v->id)>{{ $v->plate_number }} · {{ $v->make }} {{ $v->model }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Filter by Driver</label>
        <select name="team_member_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs outline-none focus:border-primary-500">
          <option value="">All Drivers</option>
          @foreach($allMembersForFilter as $m)
            <option value="{{ $m->id }}" @selected(request('team_member_id') == $m->id)>{{ $m->full_name }} ({{ $m->employee_code }})</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Filter by Status</label>
        <select name="assignment_status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs outline-none focus:border-primary-500">
          <option value="">All Statuses</option>
          <option value="active" @selected(request('assignment_status') === 'active')>Active (Currently Assigned)</option>
          <option value="returned" @selected(request('assignment_status') === 'returned')>Returned</option>
        </select>
      </div>

      <div class="flex items-end gap-2">
        <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition shadow-soft">Filter</button>
        <a href="{{ route('business.vehicles.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition shadow-sm text-center">Reset</a>
      </div>
    </form>

    {{-- Report Table --}}
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100 bg-slate-50/50">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Vehicle</th>
          <th class="px-4 py-3 text-start font-semibold">Driver</th>
          <th class="px-4 py-3 text-start font-semibold">Assigned At</th>
          <th class="px-4 py-3 text-start font-semibold">Returned At</th>
          <th class="px-4 py-3 text-start font-semibold">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($assignments as $assignment)
        <tr class="hover:bg-slate-50 transition">
          <td class="px-5 py-3">
            <p class="font-bold text-slate-900">{{ $assignment->vehicle?->plate_number }}</p>
            <p class="text-[11px] text-slate-400 capitalize">{{ $assignment->vehicle?->make }} {{ $assignment->vehicle?->model }}</p>
          </td>
          <td class="px-4 py-3">
            <p class="font-semibold text-slate-900">{{ $assignment->member?->full_name }}</p>
            <p class="text-[11px] text-slate-400">{{ $assignment->member?->employee_code }}</p>
          </td>
          <td class="px-4 py-3">
            <p class="font-medium text-slate-700 text-xs">{{ $assignment->assigned_at->format('d M Y, H:i') }}</p>
            <p class="text-[11px] text-slate-400">Odometer: {{ number_format($assignment->odometer_at_assignment) }} km</p>
            <p class="text-[10px] text-slate-400 italic">By: {{ $assignment->assignedBy?->name }}</p>
          </td>
          <td class="px-4 py-3">
            @if($assignment->returned_at)
              <p class="font-medium text-slate-700 text-xs">{{ $assignment->returned_at->format('d M Y, H:i') }}</p>
              @if($assignment->odometer_at_return)
                <p class="text-[11px] text-slate-400">Odometer: {{ number_format($assignment->odometer_at_return) }} km</p>
              @endif
              @if($assignment->odometer_at_return && $assignment->odometer_at_assignment)
                <p class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded inline-block mt-0.5" title="Distance driven during assignment">
                  ⚡ {{ number_format($assignment->odometer_at_return - $assignment->odometer_at_assignment) }} km driven
                </p>
              @endif
              @if($assignment->notes)
                <p class="text-[10px] text-slate-500 bg-slate-50 border border-slate-100 rounded px-1.5 py-0.5 mt-0.5 italic max-w-xs truncate" title="{{ $assignment->notes }}">
                  {{ $assignment->notes }}
                </p>
              @endif
            @else
              <span class="text-slate-400 italic text-xs">Currently assigned</span>
            @endif
          </td>
          <td class="px-4 py-3">
            @if($assignment->status === 'active')
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[11px] font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                Active
              </span>
            @else
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 text-[11px] font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                Returned
              </span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="px-5 py-12 text-center text-slate-400 italic">No vehicle assignment history records found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
    
    @if($assignments->hasPages())
      <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $assignments->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
