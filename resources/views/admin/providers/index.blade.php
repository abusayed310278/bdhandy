@extends('layouts.dashboard')
@section('title', 'Provider Verification Queue')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-semibold text-slate-900">Provider Verification</h2>
      <p class="text-sm text-slate-500 mt-0.5">Review and approve provider applications</p>
    </div>
  </div>

  {{-- Status Tabs --}}
  <nav class="flex gap-1 border-b border-slate-200 overflow-x-auto">
    @foreach([
      'in_review' => ['label' => 'In Review', 'count' => $counts['in_review'], 'color' => 'text-primary-600 border-primary-500'],
      'pending'   => ['label' => 'Pending',   'count' => $counts['pending'],   'color' => 'text-yellow-600 border-yellow-500'],
      'approved'  => ['label' => 'Approved',  'count' => $counts['approved'],  'color' => 'text-green-600 border-green-500'],
      'rejected'  => ['label' => 'Rejected',  'count' => $counts['rejected'],  'color' => 'text-red-600 border-red-500'],
      'all'       => ['label' => 'All',        'count' => null,                 'color' => 'text-slate-600 border-slate-500'],
    ] as $key => $tab)
    <a href="{{ route('admin.providers.index', ['status' => $key]) }}"
       class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium border-b-2 whitespace-nowrap transition
              {{ $status === $key ? $tab['color'] : 'border-transparent text-slate-500 hover:text-slate-700' }}">
      {{ $tab['label'] }}
      @if($tab['count'] !== null)
        <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold rounded-full
                     {{ $status === $key ? 'bg-primary-100 text-primary-700' : 'bg-slate-100 text-slate-500' }}">
          {{ $tab['count'] }}
        </span>
      @endif
    </a>
    @endforeach
  </nav>

  {{-- Table --}}
  <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-slate-50 border-b border-slate-100">
          <th class="px-5 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">Provider</th>
          <th class="px-5 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">Type</th>
          <th class="px-5 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">Documents</th>
          <th class="px-5 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">Submitted</th>
          <th class="px-5 py-3 text-start text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
          <th class="px-5 py-3 text-end text-xs font-semibold uppercase tracking-wider text-slate-500">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($profiles as $profile)
        <tr class="hover:bg-slate-50/50 transition">
          <td class="px-5 py-3.5">
            <div class="flex items-center gap-3">
              <span class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-sm shrink-0">
                {{ substr($profile->business_name, 0, 1) }}
              </span>
              <div>
                <p class="font-semibold text-slate-900">{{ $profile->business_name }}</p>
                <p class="text-xs text-slate-500">{{ $profile->user->email }}</p>
              </div>
            </div>
          </td>
          <td class="px-5 py-3.5">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
              {{ $profile->provider_type === 'business' ? 'bg-purple-50 text-purple-700' : 'bg-sky-50 text-sky-700' }}">
              {{ ucfirst($profile->provider_type) }}
            </span>
          </td>
          <td class="px-5 py-3.5 text-slate-700">{{ $profile->documents->count() }} uploaded</td>
          <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $profile->updated_at->diffForHumans() }}</td>
          <td class="px-5 py-3.5">
            @php
              $badges = [
                'pending'   => 'bg-yellow-50 text-yellow-700',
                'in_review' => 'bg-primary-50 text-primary-700',
                'approved'  => 'bg-green-50 text-green-700',
                'rejected'  => 'bg-red-50 text-red-700',
              ];
            @endphp
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badges[$profile->verification_status] ?? '' }}">
              <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
              {{ ucfirst(str_replace('_', ' ', $profile->verification_status)) }}
            </span>
          </td>
          <td class="px-5 py-3.5 text-end">
            <a href="{{ route('admin.providers.show', $profile) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary-50 text-primary-700 hover:bg-primary-100 transition">
              Review
              <svg class="w-3.5 h-3.5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="px-5 py-16 text-center">
            <div class="flex flex-col items-center gap-3">
              <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </div>
              <p class="text-sm text-slate-500">No providers with this status</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

    @if($profiles->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
      {{ $profiles->withQueryString()->links() }}
    </div>
    @endif
  </div>
</div>
@endsection
