@extends('layouts.dashboard')
@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 text-sm">

  {{-- Header block --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Notifications</h2>
      <p class="text-slate-500 text-xs mt-0.5">Stay updated with your latest account activity, request status changes, and assignments.</p>
    </div>
    
    @if($notifications->isNotEmpty() && auth()->user()->unreadNotifications()->count() > 0)
      <form action="{{ route('notifications.read-all') }}" method="POST">
        @csrf
        <button class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shadow-sm cursor-pointer">
          ✓ Mark All as Read
        </button>
      </form>
    @endif
  </div>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-xs font-semibold">{{ session('success') }}</div>
  @endif

  {{-- Notification list --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    @if($notifications->isNotEmpty())
      <div class="divide-y divide-slate-100">
        @foreach($notifications as $notif)
          @php $data = $notif->data; @endphp
          <div class="flex items-start gap-4 px-6 py-4 hover:bg-slate-50/50 transition border-s-4 {{ $notif->read_at ? 'border-transparent' : 'border-primary-500 bg-primary-50/5' }}">
            {{-- Status indicator --}}
            <div class="shrink-0 mt-1">
              @if(!$notif->read_at)
                <span class="block w-2.5 h-2.5 rounded-full bg-primary-500 ring-4 ring-primary-100 animate-pulse"></span>
              @else
                <span class="block w-2.5 h-2.5 rounded-full bg-slate-200"></span>
              @endif
            </div>

            {{-- Body content --}}
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <h4 class="font-bold text-slate-900 text-sm">{{ $data['title'] ?? 'Notification' }}</h4>
                <span class="text-[10px] text-slate-400 font-medium">• {{ $notif->created_at->diffForHumans() }}</span>
              </div>
              <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $data['body'] ?? '' }}</p>
              
              {{-- Action links --}}
              <div class="flex items-center gap-4 mt-2.5">
                @if(!empty($data['url']))
                  <a href="{{ $data['url'] }}" 
                     @if(!$notif->read_at) onclick="event.preventDefault(); markReadAndNavigate('{{ $notif->id }}', '{{ $data['url'] }}')" @endif
                     class="text-[11px] font-bold text-primary-600 hover:text-primary-700 hover:underline">
                    View Details →
                  </a>
                @endif

                @if(!$notif->read_at)
                  <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="inline-block">
                    @csrf
                    <button class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 transition cursor-pointer">
                      Mark as read
                    </button>
                  </form>
                @endif

                <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="inline-block">
                  @csrf
                  @method('DELETE')
                  <button class="text-[11px] font-semibold text-rose-400 hover:text-rose-600 transition cursor-pointer" onclick="return confirm('Are you sure you want to delete this notification?')">
                    Delete
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Pagination footer --}}
      @if($notifications->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
          {{ $notifications->links() }}
        </div>
      @endif
    @else
      <div class="p-16 text-center text-slate-400 italic">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
          <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
        </div>
        <p class="text-sm font-semibold text-slate-600">No Notifications Yet</p>
        <p class="text-xs text-slate-400 mt-1">We'll alert you when there is activity on your account.</p>
      </div>
    @endif
  </div>

</div>

<script>
function markReadAndNavigate(id, url) {
    fetch('/notifications/' + id + '/read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .finally(() => {
        window.location.href = url;
    });
}
</script>
@endsection
