@extends('layouts.dashboard')
@section('title', 'Maintenance — ' . $equipment->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6 text-sm">
  <div class="flex items-center justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-900">{{ $equipment->name }}</h2>
      <p class="text-slate-500 text-xs mt-0.5">Maintenance Records · {{ $equipment->code }}</p>
    </div>
    <a href="{{ route('business.equipment.index') }}" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">← Back</a>
  </div>

  {{-- Add record form --}}
  <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
    <h3 class="font-semibold text-slate-900">Add Maintenance Record</h3>
    <form action="{{ route('business.equipment.maintenance.store', $equipment) }}" method="POST" class="grid sm:grid-cols-2 gap-4">
      @csrf
      @foreach([
        ['n'=>'maintenance_type','label'=>'Type *','type'=>'select','opts'=>['scheduled'=>'Scheduled','repair'=>'Repair','calibration'=>'Calibration','inspection'=>'Inspection']],
        ['n'=>'status','label'=>'Status *','type'=>'select','opts'=>['scheduled'=>'Scheduled','completed'=>'Completed','cancelled'=>'Cancelled']],
        ['n'=>'maintenance_date','label'=>'Date *','type'=>'date'],
        ['n'=>'next_maintenance_date','label'=>'Next Date','type'=>'date'],
        ['n'=>'performed_by','label'=>'Performed By','type'=>'text'],
        ['n'=>'cost','label'=>'Cost','type'=>'number'],
      ] as $f)
      <div>
        <label class="block text-xs font-semibold text-slate-700 mb-1">{{ $f['label'] }}</label>
        @if($f['type'] === 'select')
        <select name="{{ $f['n'] }}" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 outline-none appearance-none">
          @foreach($f['opts'] as $v => $l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
        </select>
        @else
        <input type="{{ $f['type'] }}" name="{{ $f['n'] }}" placeholder="" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 focus:border-primary-500 outline-none">
        @endif
      </div>
      @endforeach
      <div class="sm:col-span-2">
        <label class="block text-xs font-semibold text-slate-700 mb-1">Description</label>
        <textarea name="description" rows="2" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 focus:border-primary-500 outline-none resize-none"></textarea>
      </div>
      <div class="sm:col-span-2 flex justify-end">
        <button class="px-5 py-2 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition">Save Record</button>
      </div>
    </form>
  </div>

  {{-- History --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="border-b border-slate-100">
        <tr class="text-xs text-slate-500 uppercase tracking-wider">
          <th class="px-5 py-3 text-start font-semibold">Date</th>
          <th class="px-4 py-3 text-start font-semibold">Type</th>
          <th class="px-4 py-3 text-start font-semibold">Performed By</th>
          <th class="px-4 py-3 text-start font-semibold">Next Date</th>
          <th class="px-4 py-3 text-start font-semibold">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @forelse($records as $r)
        <tr>
          <td class="px-5 py-3 font-medium">{{ $r->maintenance_date->format('d M Y') }}</td>
          <td class="px-4 py-3 capitalize text-slate-600">{{ $r->maintenance_type }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $r->performed_by ?? '—' }}</td>
          <td class="px-4 py-3 text-slate-600">{{ $r->next_maintenance_date?->format('d M Y') ?? '—' }}</td>
          <td class="px-4 py-3">
            @php $c=['scheduled'=>'blue','completed'=>'green','cancelled'=>'slate'][$r->status]??'slate'; @endphp
            <span class="px-2 py-0.5 rounded-full bg-{{ $c }}-100 text-{{ $c }}-700 text-[11px] font-semibold capitalize">{{ $r->status }}</span>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400 italic">No maintenance records.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
