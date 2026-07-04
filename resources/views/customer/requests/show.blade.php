@extends('layouts.dashboard')
@section('title', 'Request — ' . $request->request_number)

@section('content')
<div class="space-y-5 text-sm max-w-4xl">
  <div class="flex items-center justify-between gap-3">
    <div>
      <a href="{{ route('customer.requests.index') }}" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1 mb-1">
        <svg class="w-3.5 h-3.5 rtl-flip" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        My Requests
      </a>
      <h2 class="text-xl font-bold text-slate-900">{{ $request->title }}</h2>
      <p class="text-xs text-slate-400 mt-0.5">{{ $request->request_number }}</p>
    </div>
    @php $c = ['pending'=>'yellow','accepted'=>'primary','in_progress'=>'primary','completed'=>'green','cancelled'=>'slate','disputed'=>'red','expired'=>'slate'][$request->request_status] ?? 'slate'; @endphp
    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-{{ $c }}-50 text-{{ $c }}-700 ring-1 ring-{{ $c }}-200">
      {{ str_replace('_', ' ', $request->request_status) }}
    </span>
  </div>

  <div class="grid lg:grid-cols-3 gap-5">
    {{-- Main --}}
    <div class="lg:col-span-2 space-y-5">

      {{-- Details --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h3 class="font-bold text-slate-800 mb-4">Request Details</h3>
        <dl class="grid grid-cols-2 gap-4">
          <div>
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Service</dt>
            <dd class="mt-1 text-slate-800">{{ $request->service?->getTranslation('translations','en')['name'] ?? '—' }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Urgency</dt>
            <dd class="mt-1 text-slate-800 capitalize">{{ $request->urgency }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Preferred Date</dt>
            <dd class="mt-1 text-slate-800">{{ $request->preferred_date?->format('d M Y') ?? '—' }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Preferred Time</dt>
            <dd class="mt-1 text-slate-800">{{ $request->preferred_time ? \Carbon\Carbon::parse($request->preferred_time)->format('h:i A') : '—' }}</dd>
          </div>
          @if($request->estimated_price)
          <div>
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Estimated Price</dt>
            <dd class="mt-1 font-semibold text-slate-800">{{ $request->currency?->symbol }} {{ number_format($request->estimated_price, 2) }}</dd>
          </div>
          @endif
          @if($request->final_price)
          <div>
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Final Price</dt>
            <dd class="mt-1 font-semibold text-slate-800">{{ $request->currency?->symbol }} {{ number_format($request->final_price, 2) }}</dd>
          </div>
          @endif
          <div class="col-span-2">
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Address</dt>
            <dd class="mt-1 text-slate-800">{{ $request->address }}</dd>
          </div>
          @if($request->description)
          <div class="col-span-2">
            <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Description</dt>
            <dd class="mt-1 text-slate-800 leading-relaxed">{{ $request->description }}</dd>
          </div>
          @endif
        </dl>

        @if($request->attachments && $request->attachments->isNotEmpty())
        <div class="border-t border-slate-100 pt-4 mt-4">
          <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Attachments</h4>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($request->attachments as $attachment)
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
      </div>

      {{-- Status Timeline --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h3 class="font-bold text-slate-800 mb-4">Status History</h3>
        @if($request->statusLogs->isEmpty())
          <p class="text-slate-400 text-xs">No status updates yet.</p>
        @else
          <ol class="space-y-3">
            @foreach($request->statusLogs->sortByDesc('created_at') as $log)
            <li class="flex items-start gap-3">
              <span class="mt-1 w-2 h-2 rounded-full bg-primary-400 shrink-0"></span>
              <div>
                <p class="text-slate-700 font-medium capitalize">{{ str_replace('_', ' ', $log->new_status) }}</p>
                @if($log->notes)<p class="text-xs text-slate-500">{{ $log->notes }}</p>@endif
                <p class="text-xs text-slate-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
              </div>
            </li>
            @endforeach
          </ol>
        @endif
      </div>

      {{-- Leave Review --}}
      @if($request->request_status === 'completed' && !$review)
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h3 class="font-bold text-slate-800 mb-4">Leave a Review</h3>
        <form action="{{ route('customer.reviews.store') }}" method="POST" x-data="{ rating: 0, hover: 0 }">
          @csrf
          <input type="hidden" name="service_request_id" value="{{ $request->id }}">
          <input type="hidden" name="rating" x-bind:value="rating">
          <div class="flex items-center gap-1 mb-4">
            @for($i = 1; $i <= 5; $i++)
            <button type="button" @click="rating = {{ $i }}" @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0"
              class="text-2xl transition" :class="hover >= {{ $i }} || (hover === 0 && rating >= {{ $i }}) ? 'text-accent-500' : 'text-slate-200'">★</button>
            @endfor
            <span class="ms-2 text-xs text-slate-500" x-text="rating ? rating + ' star' + (rating > 1 ? 's' : '') : 'Click to rate'"></span>
          </div>
          <textarea name="review" rows="3" placeholder="Share your experience (optional)…"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition resize-none"></textarea>
          <button type="submit" class="mt-3 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition">Submit Review</button>
        </form>
      </div>
      @elseif($review)
      <div class="bg-green-50 rounded-2xl border border-green-200 p-5">
        <p class="text-green-700 font-semibold text-sm">Review submitted</p>
        <div class="flex items-center gap-0.5 mt-1">
          @for($i=1;$i<=5;$i++)<span class="text-{{ $i <= $review->rating ? 'accent' : 'slate' }}-400">★</span>@endfor
        </div>
        @if($review->review)<p class="text-xs text-green-700 mt-2">{{ $review->review }}</p>@endif
      </div>
      @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
      {{-- Provider --}}
      @if($request->provider?->providerProfile)
      <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h3 class="font-bold text-slate-800 mb-3 text-xs uppercase tracking-wider">Provider</h3>
        <div class="flex items-center gap-3">
          @if($request->provider->providerProfile->logo)
            <img src="{{ asset('storage/'.$request->provider->providerProfile->logo) }}" class="w-12 h-12 rounded-full object-cover shrink-0">
          @else
            <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-700 font-bold flex items-center justify-center shrink-0">
              {{ strtoupper(substr($request->provider->providerProfile->business_name, 0, 2)) }}
            </div>
          @endif
          <div>
            <p class="font-semibold text-slate-900">{{ $request->provider->providerProfile->business_name }}</p>
            <p class="text-xs text-slate-500">{{ $request->provider->name }}</p>
          </div>
        </div>
        @if($request->provider->providerProfile->primary_phone)
          <p class="mt-3 text-xs text-slate-500">📞 {{ $request->provider->providerProfile->primary_phone }}</p>
        @endif
      </div>
      @endif

      {{-- Invoice --}}
      @if($request->invoice)
        @php
          $inv = $request->invoice;
          $invStatusColors = ['draft'=>'slate','pending'=>'yellow','due'=>'orange','partial'=>'blue','paid'=>'green'];
          $invSc = $invStatusColors[$inv->payment_status] ?? 'slate';
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
          <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Invoice
          </h3>
          <div class="space-y-2 text-xs">
            <div class="flex justify-between">
              <span class="text-slate-500">Number</span>
              <span class="font-bold text-slate-900">{{ $inv->invoice_number }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">Total</span>
              <span class="font-bold text-slate-900">{{ $inv->currency?->symbol }} {{ number_format($inv->total, 2) }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-slate-500">Status</span>
              <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-{{ $invSc }}-50 text-{{ $invSc }}-700">{{ ucfirst($inv->payment_status) }}</span>
            </div>
            @if($inv->due_date)
            <div class="flex justify-between">
              <span class="text-slate-500">Due Date</span>
              <span class="font-semibold {{ $inv->due_date->isPast() && $inv->payment_status !== 'paid' ? 'text-red-600' : 'text-slate-700' }}">{{ $inv->due_date->format('d M Y') }}</span>
            </div>
            @endif
          </div>
          <a href="{{ route('customer.invoices.show', $inv) }}"
             class="mt-4 block w-full text-center py-2 rounded-xl bg-primary-50 text-primary-700 text-sm font-semibold hover:bg-primary-100 transition">
            View Invoice
          </a>
        </div>
      @endif

      {{-- Actions --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-3">
        <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-1">Actions</h3>
        @if($request->request_status === 'pending')
          <form action="{{ route('customer.requests.cancel', $request) }}" method="POST" onsubmit="return confirm('Cancel this request?')">
            @csrf
            <button type="submit" class="w-full py-2 rounded-xl border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition">Cancel Request</button>
          </form>
        @endif
        @php
          $conv = \App\Models\Conversation::firstOrCreate([
              'customer_id' => $request->customer_id,
              'provider_id' => $request->provider_id,
          ]);
        @endphp
        @if($conv)
          <a href="{{ route('customer.conversations.show', $conv) }}" class="block w-full text-center py-2 rounded-xl bg-primary-50 text-primary-700 text-sm font-semibold hover:bg-primary-100 transition">
            Message Provider
          </a>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
