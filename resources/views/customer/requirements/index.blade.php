@extends('layouts.dashboard')
@section('title', 'My Requirements')

@section('content')
<div class="space-y-5 text-sm">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h2 class="text-xl font-bold text-slate-900">My Requirements</h2>
      <p class="text-slate-500 text-xs mt-0.5">Requirements you've posted for providers to respond</p>
    </div>
    <a href="{{ route('customer.requirements.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Post Requirement
    </a>
  </div>

  <nav class="flex gap-1 border-b border-slate-200 overflow-x-auto no-scrollbar">
    @foreach(['open'=>'Open','assigned'=>'Assigned','completed'=>'Completed','closed'=>'Closed'] as $key=>$label)
      <a href="{{ route('customer.requirements.index', ['tab'=>$key]) }}"
         class="px-4 py-2.5 text-sm font-medium border-b-2 whitespace-nowrap transition {{ $tab===$key ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
        {{ $label }}
      </a>
    @endforeach
  </nav>

  @if($requirements->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
      <p class="text-slate-500 font-medium">No {{ $tab }} requirements</p>
      <p class="text-slate-400 text-xs mt-1">Post a requirement and nearby providers will respond</p>
      <a href="{{ route('customer.requirements.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 transition">Post Now</a>
    </div>
  @else
    <div class="space-y-3">
      @foreach($requirements as $req)
      <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:border-primary-200 transition">
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="font-semibold text-slate-900">{{ $req->title }}</h3>
              <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider
                {{ $req->urgency==='emergency' ? 'bg-red-50 text-red-700' : ($req->urgency==='urgent' ? 'bg-yellow-50 text-yellow-700' : 'bg-slate-100 text-slate-600') }}">
                {{ $req->urgency }}
              </span>
            </div>
            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $req->description }}</p>
            <div class="mt-2 flex flex-wrap items-center gap-4 text-xs text-slate-500">
              <span class="flex items-center gap-1">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                {{ $req->category->getTranslation('translations','en') ?: $req->category->slug }}
              </span>
              <span>{{ $req->proposals->count() }} proposals</span>
              @if($req->expiry_at)<span>Expires {{ $req->expiry_at->diffForHumans() }}</span>@endif
              <span>{{ $req->created_at->diffForHumans() }}</span>
            </div>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('customer.requirements.show', $req) }}" class="px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 text-xs font-medium hover:bg-primary-100 transition">View</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @if($requirements->hasPages())
      <div class="py-4">{{ $requirements->appends(['tab'=>$tab])->links() }}</div>
    @endif
  @endif
</div>
@endsection
