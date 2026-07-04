@extends('layouts.dashboard')
@section('title', 'My Services')

@section('content')
<div class="space-y-5 text-sm">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h2 class="text-xl font-bold text-slate-900">My Services</h2>
      <p class="text-slate-500 text-xs mt-0.5">Manage the services you offer to customers</p>
    </div>
    <a href="{{ route('provider.services.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Service
    </a>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif

  @if($services->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
      <p class="text-slate-500 font-medium">No services added yet</p>
      <p class="text-slate-400 text-xs mt-1">Add the services you offer to start receiving requests</p>
      <a href="{{ route('provider.services.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition">Add Your First Service</a>
    </div>
  @else
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3.5">Service</th>
              <th class="px-5 py-3.5">Pricing</th>
              <th class="px-5 py-3.5 text-center">Emergency</th>
              <th class="px-5 py-3.5 text-center">Status</th>
              <th class="px-5 py-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($services as $svc)
            @php
              $serviceName = ($svc->service ? (($svc->service->getTranslation('translations','en')['name'] ?? null) ?: $svc->service->slug) : '—');
            @endphp
            <tr class="hover:bg-slate-50/50 transition">
              <td class="px-5 py-4">
                <p class="font-semibold text-slate-900">{{ $svc->title }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $serviceName }}</p>
                @if($svc->duration_minutes)
                  <p class="text-[11px] text-slate-400 mt-0.5">{{ $svc->duration_minutes }} min</p>
                @endif
              </td>
              <td class="px-5 py-4">
                @if($svc->pricing_type === 'fixed')
                  <p class="font-semibold text-slate-800">{{ $svc->currency?->symbol }}{{ number_format($svc->price_fixed, 0) }}</p>
                  <p class="text-[11px] text-slate-400">Fixed</p>
                @elseif($svc->pricing_type === 'range')
                  <p class="font-semibold text-slate-800">{{ $svc->currency?->symbol }}{{ number_format($svc->price_min, 0) }} – {{ $svc->currency?->symbol }}{{ number_format($svc->price_max, 0) }}</p>
                  <p class="text-[11px] text-slate-400">Range</p>
                @elseif($svc->pricing_type === 'hourly')
                  <p class="font-semibold text-slate-800">{{ $svc->currency?->symbol }}{{ number_format($svc->price_fixed, 0) }}/hr</p>
                  <p class="text-[11px] text-slate-400">Hourly</p>
                @else
                  <p class="text-slate-500 italic">Quote on request</p>
                @endif
              </td>
              <td class="px-5 py-4 text-center">
                @if($svc->is_emergency)
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700">Emergency</span>
                @else
                  <span class="text-slate-300 text-xs">—</span>
                @endif
              </td>
              <td class="px-5 py-4 text-center">
                @if($svc->status === 'active')
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700">Active</span>
                @else
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Inactive</span>
                @endif
              </td>
              <td class="px-5 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('provider.services.edit', $svc) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-medium hover:bg-primary-50 hover:text-primary-700 transition">Edit</a>
                  <form action="{{ route('provider.services.destroy', $svc) }}" method="POST" onsubmit="return confirm('Remove this service?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition">Remove</button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($services->hasPages())
        <div class="px-5 py-4 bg-slate-50 border-t border-slate-100">{{ $services->links() }}</div>
      @endif
    </div>
  @endif
</div>
@endsection
