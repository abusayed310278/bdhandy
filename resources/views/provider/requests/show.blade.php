@extends('layouts.dashboard')
@section('title', 'Request — ' . $serviceRequest->request_number)

@section('content')
<div class="max-w-3xl space-y-5 text-sm">

  <div class="flex items-start justify-between gap-3">
    <div>
      <a href="{{ route('provider.requests.index') }}" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1 mb-1">
        <svg class="w-3.5 h-3.5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        Requests
      </a>
      <h2 class="text-xl font-bold text-slate-900">{{ $serviceRequest->title }}</h2>
      <p class="text-xs text-slate-400 mt-0.5">{{ $serviceRequest->request_number }}</p>
    </div>
    @php
      $statusColors = ['pending'=>'yellow','accepted'=>'primary','in_progress'=>'blue','completed'=>'green','cancelled'=>'slate','disputed'=>'red','expired'=>'slate'];
      $urgColors    = ['emergency'=>'red','urgent'=>'yellow','normal'=>'slate'];
      $sc = $statusColors[$serviceRequest->request_status] ?? 'slate';
      $uc = $urgColors[$serviceRequest->urgency] ?? 'slate';
    @endphp
    <div class="flex items-center gap-2 shrink-0">
      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-{{ $uc }}-50 text-{{ $uc }}-700">{{ $serviceRequest->urgency }}</span>
      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-{{ $sc }}-50 text-{{ $sc }}-700">{{ str_replace('_',' ',$serviceRequest->request_status) }}</span>
    </div>
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-xs font-medium">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
      <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  {{-- Details --}}
  <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <p class="text-[11px] font-semibold text-slate-500 uppercase mb-1">Customer</p>
        <p class="font-semibold text-slate-900">{{ $serviceRequest->customer?->name ?? '—' }}</p>
      </div>
      @if($serviceRequest->service)
      <div>
        <p class="text-[11px] font-semibold text-slate-500 uppercase mb-1">Service</p>
        <p class="font-semibold text-slate-900">{{ ($serviceRequest->service->getTranslation('translations','en')['name'] ?? null) ?: $serviceRequest->service->slug }}</p>
      </div>
      @endif
      @if($serviceRequest->preferred_date)
      <div>
        <p class="text-[11px] font-semibold text-slate-500 uppercase mb-1">Preferred Date</p>
        <p class="font-semibold text-slate-900">{{ $serviceRequest->preferred_date->format('d F Y') }}</p>
      </div>
      @endif
      @if($serviceRequest->estimated_price || $serviceRequest->final_price)
      <div>
        <p class="text-[11px] font-semibold text-slate-500 uppercase mb-1">Price</p>
        <p class="font-semibold text-slate-900">
          {{ $serviceRequest->currency?->symbol }}{{ number_format($serviceRequest->final_price ?? $serviceRequest->estimated_price, 0) }}
          <span class="text-xs text-slate-400 font-normal">{{ $serviceRequest->final_price ? '(final)' : '(estimate)' }}</span>
        </p>
      </div>
      @endif
    </div>

    @if($serviceRequest->address)
    <div>
      <p class="text-[11px] font-semibold text-slate-500 uppercase mb-1">Address</p>
      <p class="text-slate-700">{{ $serviceRequest->address }}</p>
    </div>
    @endif

    @if($serviceRequest->description)
    <div>
      <p class="text-[11px] font-semibold text-slate-500 uppercase mb-1">Description</p>
      <p class="text-slate-700 leading-relaxed whitespace-pre-line">{{ $serviceRequest->description }}</p>
    </div>
    @endif

    @if($serviceRequest->attachments && $serviceRequest->attachments->isNotEmpty())
    <div class="border-t border-slate-100 pt-4">
      <p class="text-[11px] font-semibold text-slate-500 uppercase mb-2">Attachments</p>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
        @foreach($serviceRequest->attachments as $attachment)
          <div class="relative group rounded-xl border border-slate-200 overflow-hidden bg-slate-50 aspect-video flex flex-col items-center justify-center p-2 text-center">
            @if($attachment->file_type === 'image')
              <img src="{{ asset('storage/' . $attachment->file) }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-200" alt="Attachment">
              <a href="{{ asset('storage/' . $attachment->file) }}" target="_blank" class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition duration-150">
                View Full Image
              </a>
            @elseif($attachment->file_type === 'video')
              <video src="{{ asset('storage/' . $attachment->file) }}" class="absolute inset-0 w-full h-full object-cover" muted></video>
              <div class="absolute inset-0 bg-slate-900/20 flex items-center justify-center text-white">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
              </div>
              <a href="{{ asset('storage/' . $attachment->file) }}" target="_blank" class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition duration-150">
                Play Video
              </a>
            @else
              {{-- Document / PDF --}}
              <svg class="w-8 h-8 text-red-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
              </svg>
              <span class="text-[10px] font-semibold text-slate-600 truncate max-w-full px-1">Document</span>
              <a href="{{ asset('storage/' . $attachment->file) }}" target="_blank" class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition duration-150">
                Open File
              </a>
            @endif
          </div>
        @endforeach
      </div>
    </div>
    @endif

    @if($serviceRequest->cancellation_reason)
    <div class="rounded-xl bg-red-50 border border-red-100 p-3">
      <p class="text-[11px] font-semibold text-red-600 uppercase mb-1">Cancellation Reason</p>
      <p class="text-red-700 text-xs">{{ $serviceRequest->cancellation_reason }}</p>
    </div>
    @endif
  </div>

  @php
    $conv = \App\Models\Conversation::firstOrCreate([
        'customer_id' => $serviceRequest->customer_id,
        'provider_id' => $serviceRequest->provider_id,
    ]);
  @endphp
  @if($conv)
  <div class="flex">
    <a href="{{ route('provider.conversations.show', $conv) }}"
      class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-50 text-primary-700 text-sm font-semibold hover:bg-primary-100 transition">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Open Conversation
    </a>
  </div>
  @endif

  {{-- Invoice --}}
  @if($serviceRequest->request_status === 'completed')
    @if($serviceRequest->invoice)
      @php
        $inv = $serviceRequest->invoice;
        $invStatusColors = ['draft'=>'slate','pending'=>'yellow','due'=>'orange','partial'=>'blue','paid'=>'green'];
        $invSc = $invStatusColors[$inv->payment_status] ?? 'slate';
      @endphp
      <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </span>
          <div>
            <p class="font-bold text-slate-900 text-sm">{{ $inv->invoice_number }}</p>
            <div class="flex items-center gap-2 mt-0.5">
              <span class="text-xs text-slate-500">{{ $inv->currency?->symbol }} {{ number_format($inv->total, 2) }}</span>
              <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-{{ $invSc }}-50 text-{{ $invSc }}-700">{{ ucfirst($inv->payment_status) }}</span>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          @if($inv->isEditable())
            <a href="{{ route('provider.invoices.edit', $inv) }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">Edit</a>
          @endif
          <a href="{{ route('provider.invoices.show', $inv) }}" class="px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-xs font-semibold hover:bg-primary-100 transition">View Invoice</a>
        </div>
      </div>
    @else
      <div class="bg-primary-50 border border-primary-100 rounded-2xl p-5 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </span>
          <div>
            <p class="font-bold text-slate-900 text-sm">Create Invoice / Receipt</p>
            <p class="text-xs text-slate-500 mt-0.5">Issue a receipt with pricing, tax, discount and payment details</p>
          </div>
        </div>
        <a href="{{ route('provider.invoices.create', $serviceRequest) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold shadow-sm transition shrink-0">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          Create Invoice
        </a>
      </div>
    @endif
  @endif

  {{-- Team Assignment (business-only, view-only) --}}
  @role('business')
  @php $primaryAssignment = $serviceRequest->teamAssignments->first(); @endphp
  @if($primaryAssignment)
  <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3">
    <h3 class="font-semibold text-slate-900 text-sm">Assigned Team Member</h3>
    @php
      $tm = $primaryAssignment->member;
      $aColours = [
        'assigned'    => ['bg-slate-100',   'text-slate-700'],
        'accepted'    => ['bg-blue-100',    'text-blue-700'],
        'en_route'    => ['bg-yellow-100',  'text-yellow-700'],
        'arrived'     => ['bg-cyan-100',    'text-cyan-700'],
        'in_progress' => ['bg-orange-100',  'text-orange-700'],
        'completed'   => ['bg-green-100',   'text-green-700'],
        'rejected'    => ['bg-red-100',     'text-red-700'],
        'reassigned'  => ['bg-purple-100',  'text-purple-700'],
      ];
      [$aBg, $aText] = $aColours[$primaryAssignment->status] ?? ['bg-slate-100','text-slate-600'];
    @endphp
    <div class="flex items-center gap-4">
      <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center text-primary-700 font-black text-sm shrink-0">
        {{ strtoupper(substr($tm?->full_name ?? '?', 0, 2)) }}
      </div>
      <div class="flex-1 min-w-0">
        <p class="font-bold text-slate-900">{{ $tm?->full_name ?? '—' }}</p>
        <p class="text-xs text-slate-500">{{ $tm?->designation ?? '' }}{{ $tm?->employee_code ? ' · '.$tm->employee_code : '' }}</p>
      </div>
      <span class="px-2.5 py-1 rounded-full text-[11px] font-bold capitalize {{ $aBg }} {{ $aText }}">
        {{ str_replace('_', ' ', $primaryAssignment->status) }}
      </span>
    </div>
    @if($primaryAssignment->scheduled_start_time || $primaryAssignment->arrived_at_location || $primaryAssignment->started_at || $primaryAssignment->completed_at)
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
      @foreach([
        'Scheduled'  => $primaryAssignment->scheduled_start_time,
        'Arrived'    => $primaryAssignment->arrived_at_location,
        'Started'    => $primaryAssignment->started_at,
        'Completed'  => $primaryAssignment->completed_at,
      ] as $lbl => $ts)
      @if($ts)
      <div class="bg-slate-50 rounded-xl p-2.5">
        <p class="text-[10px] font-semibold text-slate-400 uppercase">{{ $lbl }}</p>
        <p class="text-xs font-bold text-slate-700 mt-0.5">{{ $ts->format('d M · H:i') }}</p>
      </div>
      @endif
      @endforeach
    </div>
    @endif
  </div>
  @endif
  @endrole

  {{-- Status update --}}
  @php
    $allowedTransitions = match($serviceRequest->request_status) {
      'pending'     => ['accepted', 'cancelled'],
      'accepted'    => ['in_progress', 'cancelled'],
      'in_progress' => ['completed', 'cancelled'],
      default       => [],
    };
  @endphp
  @if(count($allowedTransitions) > 0)
  <div class="bg-white rounded-2xl border border-slate-200 p-5">
    <h3 class="font-semibold text-slate-900 mb-4">Update Status</h3>
    <form action="{{ route('provider.requests.status', $serviceRequest) }}" method="POST" class="space-y-4">
      @csrf
      <div class="flex flex-wrap gap-3">
        @foreach($allowedTransitions as $status)
        @php
          $btnColors = ['accepted'=>'bg-green-50 text-green-700 border-green-200 hover:bg-green-100','in_progress'=>'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100','completed'=>'bg-primary-50 text-primary-700 border-primary-200 hover:bg-primary-100','cancelled'=>'bg-red-50 text-red-700 border-red-200 hover:bg-red-100'];
          $bc = $btnColors[$status] ?? 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100';
        @endphp
        <button type="submit" name="status" value="{{ $status }}"
          class="px-4 py-2 rounded-xl border text-sm font-semibold transition {{ $bc }}"
          {{ $status === 'cancelled' ? "onclick=\"return confirm('Cancel this request?')\"" : '' }}>
          Mark as {{ ucfirst(str_replace('_', ' ', $status)) }}
        </button>
        @endforeach
      </div>
      @if(in_array('cancelled', $allowedTransitions))
      <div x-data="{ show: false }">
        <button type="button" @click="show = !show" class="text-xs text-slate-500 hover:text-slate-700 underline">Add cancellation reason</button>
        <div x-show="show" x-cloak class="mt-2">
          <textarea name="cancellation_reason" rows="2" placeholder="Optional reason for cancellation"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition resize-none"></textarea>
        </div>
      </div>
      @endif
    </form>
  </div>
  @endif

</div>
@endsection
