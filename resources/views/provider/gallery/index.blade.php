@extends('layouts.dashboard')
@section('title', 'Gallery')

@section('content')
<div class="space-y-5 text-sm">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h2 class="text-xl font-bold text-slate-900">Gallery</h2>
      <p class="text-slate-500 text-xs mt-0.5">{{ $items->count() }} / {{ $limit }} items used</p>
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

  {{-- Upload form --}}
  @if($items->count() < $limit)
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden" x-data="{ tab: 'photo' }">
    <div class="flex border-b border-slate-100 bg-slate-50/50">
      <button @click="tab = 'photo'" :class="tab === 'photo' ? 'bg-white text-primary-600 border-b-2 border-primary-500' : 'text-slate-500 hover:bg-slate-100'" class="px-6 py-3 text-xs font-bold transition">Upload Photo</button>
      <button @click="tab = 'video'" :class="tab === 'video' ? 'bg-white text-primary-600 border-b-2 border-primary-500' : 'text-slate-500 hover:bg-slate-100'" class="px-6 py-3 text-xs font-bold transition">YouTube Video</button>
    </div>

    <form action="{{ route('provider.gallery.store') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
      @csrf
      
      <div x-show="tab === 'photo'" class="space-y-4">
        <div x-data="{ name: '' }"
          class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-primary-300 transition cursor-pointer bg-slate-50/30"
          @click="$refs.fileInput.click()">
          <input type="file" name="image" accept="image/*" :required="tab === 'photo'" x-ref="fileInput"
            @change="name = $event.target.files[0]?.name ?? ''" class="sr-only">
          <div class="w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center mx-auto mb-3 text-primary-500">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          </div>
          <p class="text-xs font-semibold text-slate-700" x-text="name || 'Choose a photo to upload'"></p>
          <p class="text-[11px] text-slate-400 mt-1.5">JPEG, PNG, WEBP — max 20 MB</p>
        </div>
      </div>

      <div x-show="tab === 'video'" class="space-y-4">
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1.5">YouTube URL <span class="text-red-500">*</span></label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-red-500">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.612 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
            </div>
            <input type="url" name="video_url" :required="tab === 'video'" placeholder="https://www.youtube.com/watch?v=..."
              class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
          </div>
          <p class="text-[10px] text-slate-400 mt-1.5">Paste the full YouTube link here. We'll handle the embedding.</p>
        </div>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1.5">Caption (Optional)</label>
          <input type="text" name="caption" placeholder="Short description"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-700 mb-1.5">Sort Order</label>
          <input type="number" name="sort_order" placeholder="Auto"
            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition">
        </div>
      </div>

      <div class="flex justify-end pt-2">
        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-500 text-white text-sm font-bold hover:bg-primary-600 transition shadow-sm">
          Add to Gallery
        </button>
      </div>
    </form>
  </div>
  @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 text-yellow-700 text-xs font-medium">
      Plan limit reached ({{ $limit }} items). <a href="{{ route('provider.subscription.index') }}" class="underline font-semibold">Upgrade</a> to add more.
    </div>
  @endif

  {{-- Gallery sections --}}
  @php
    $photos = $items->where('is_video', false);
    $videos = $items->where('is_video', true);
  @endphp

  {{-- Photos Section --}}
  <div class="space-y-4">
    <div class="flex items-center gap-2">
      <h3 class="font-bold text-slate-900">Photos</h3>
      <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">{{ $photos->count() }}</span>
    </div>
    
    @if($photos->isEmpty())
      <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 italic text-xs">No photos added yet.</div>
    @else
      <div id="photo-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($photos as $item)
        <div data-id="{{ $item->id }}" class="group relative bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition cursor-move">
          <img src="{{ asset('storage/'.$item->url) }}" alt="{{ $item->caption }}" class="w-full h-40 object-cover pointer-events-none">
          <div class="p-3">
            <p class="text-xs text-slate-600 truncate font-medium">{{ $item->caption ?: 'No caption' }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 8h16M4 16h16"/></svg>
              Drag to reorder
            </p>
          </div>
          <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
            <form action="{{ route('provider.gallery.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove this photo?')">
              @csrf @method('DELETE')
              <button type="submit" class="w-7 h-7 rounded-full bg-white text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition shadow border border-slate-100">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </form>
          </div>
        </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Videos Section --}}
  <div class="space-y-4">
    <div class="flex items-center gap-2">
      <h3 class="font-bold text-slate-900">Videos</h3>
      <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">{{ $videos->count() }}</span>
    </div>

    @if($videos->isEmpty())
      <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 italic text-xs">No videos added yet.</div>
    @else
      <div id="video-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($videos as $item)
        @php
            $videoId = '';
            if (strpos($item->url, 'youtube.com') !== false || strpos($item->url, 'youtu.be') !== false) {
                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $item->url, $match);
                $videoId = $match[1] ?? '';
            }
        @endphp
        <div data-id="{{ $item->id }}" class="group relative bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition cursor-move">
          <div class="aspect-video bg-slate-900 pointer-events-none">
            @if($videoId)
              <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            @else
              <video src="{{ asset('storage/'.$item->url) }}" class="w-full h-full"></video>
            @endif
          </div>
          <div class="p-3">
            <p class="text-xs text-slate-600 truncate font-medium">{{ $item->caption ?: 'No caption' }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 8h16M4 16h16"/></svg>
              Drag to reorder
            </p>
          </div>
          <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition z-10">
            <form action="{{ route('provider.gallery.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove this video?')">
              @csrf @method('DELETE')
              <button type="submit" class="w-7 h-7 rounded-full bg-white text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition shadow border border-slate-100">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
            </form>
          </div>
        </div>
        @endforeach
      </div>
    @endif
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoGrid = document.getElementById('photo-grid');
    const videoGrid = document.getElementById('video-grid');

    const sortableOptions = {
        animation: 150,
        ghostClass: 'opacity-50',
        onEnd: function() {
            const order = Array.from(photoGrid?.children || []).concat(Array.from(videoGrid?.children || []))
                .map(el => el.dataset.id)
                .filter(Boolean);

            fetch("{{ route('provider.gallery.reorder') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            });
        }
    };

    if (photoGrid) new Sortable(photoGrid, sortableOptions);
    if (videoGrid) new Sortable(videoGrid, sortableOptions);
});
</script>
@endsection
