@extends('layouts.dashboard')
@section('title', 'My Equipment')
@section('content')
<div class="space-y-6 text-sm">
  <h2 class="text-xl font-bold text-slate-900">My Assigned Equipment</h2>
  @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('success') }}</div>@endif
  <div class="space-y-3">
    @forelse($assignments as $a)
    <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-4">
      <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
      </div>
      <div class="flex-1">
        <p class="font-semibold text-slate-900">{{ $a->equipment?->name }}</p>
        <p class="text-xs text-slate-400">{{ $a->equipment?->code }} · Assigned {{ $a->assigned_at->diffForHumans() }}</p>
      </div>
      <div x-data="{ open: false }">
        <button @click="open=!open" class="px-3 py-1.5 rounded-xl bg-red-50 text-red-600 text-xs font-semibold hover:bg-red-100 transition">Report Issue</button>
        <div x-show="open" @click.outside="open=false" class="mt-2 bg-white border border-slate-200 rounded-xl p-3 shadow-lg absolute z-10 w-64">
          <form action="{{ route('tech.equipment.report-issue', $a) }}" method="POST" class="space-y-2">
            @csrf
            <select name="condition" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 outline-none">
              <option value="damaged">Damaged</option>
              <option value="lost">Lost</option>
            </select>
            <textarea name="notes" rows="2" placeholder="Describe the issue..." class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 outline-none resize-none"></textarea>
            <button class="w-full py-1.5 rounded-xl bg-red-500 text-white text-xs font-bold">Submit Report</button>
          </form>
        </div>
      </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-dashed border-slate-200 py-12 text-center text-slate-400 italic">No equipment assigned to you.</div>
    @endforelse
  </div>
</div>
@endsection
