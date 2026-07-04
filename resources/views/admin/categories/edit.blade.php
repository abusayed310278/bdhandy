@extends('layouts.dashboard')

@section('title', isset($category) ? 'Edit Category' : 'Add New Category')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ isset($category) ? 'Edit Category' : 'Create New Category' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Configure service categories and translations</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <form action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" 
          method="POST" 
          enctype="multipart/form-data"
          class="space-y-6"
          x-data="{ 
              slug: '{{ old('slug', $category->slug ?? '') }}',
              @foreach($languages as $lang)
              name_{{ $lang->code }}: '{{ old('name_'.$lang->code, isset($category) ? $category->getTranslation('translations', $lang->code) : '') }}',
              @endforeach
              generateSlug() {
                  const defaultLang = '{{ $languages->where('is_default', true)->first()->code ?? $languages->first()->code }}';
                  const text = this['name_' + defaultLang];
                  if(text) {
                      this.slug = text.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
                  }
              }
          }">
        @csrf
        @if(isset($category)) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Translations -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl shadow-soft border border-slate-200 overflow-hidden" x-data="{ tab: '{{ $languages->where('is_default', true)->first()->code ?? $languages->first()->code }}' }">
                    <div class="flex border-b border-slate-100 bg-slate-50/50 p-1">
                        @foreach($languages as $lang)
                            <button type="button" @click="tab = '{{ $lang->code }}'" 
                                :class="tab === '{{ $lang->code }}' ? 'bg-white text-primary-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'"
                                class="flex-1 py-2 rounded-xl text-xs font-bold transition-all duration-200 uppercase tracking-widest">
                                {{ $lang->name }}
                            </button>
                        @endforeach
                    </div>

                    <div class="p-6 md:p-8 space-y-6">
                        @foreach($languages as $lang)
                            <div x-show="tab === '{{ $lang->code }}'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" style="{{ $lang->code != ($languages->where('is_default', true)->first()->code ?? $languages->first()->code) ? 'display: none;' : '' }}">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Category Name ({{ $lang->name }}) @if($lang->is_default)<span class="text-red-500">*</span>@endif</label>
                                    <input type="text" name="name_{{ $lang->code }}" x-model="name_{{ $lang->code }}" @if($lang->is_default) @input="generateSlug()" @endif class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. Electrician" @if($lang->is_default) required @endif>
                                    @error('name_'.$lang->code) <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endforeach
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Slug</label>
                            <input type="text" name="slug" x-model="slug" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-100 focus:outline-none transition" placeholder="automatic-slug" readonly>
                            @error('slug') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-soft border border-slate-200 p-6 md:p-8">
                    <h4 class="text-sm font-bold text-slate-900 uppercase tracking-widest mb-6">Classification & Order</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 transition outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 transition appearance-none cursor-pointer">
                                <option value="active" {{ (old('status', $category->status ?? 'active') == 'active') ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ (old('status', $category->status ?? '') == 'inactive') ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Assets -->
            <div class="space-y-6">
                <!-- Icon Upload -->
                <div class="bg-white rounded-3xl shadow-soft border border-slate-200 p-6">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Category Icon</label>
                    <div class="relative group">
                        <div class="aspect-square w-full rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-primary-300">
                            @if(isset($category) && $category->icon)
                                <img id="iconPreview" src="{{ asset('storage/'.$category->icon) }}" class="w-full h-full object-cover">
                            @else
                                <img id="iconPreview" class="hidden w-full h-full object-cover">
                                <div id="iconPlaceholder" class="flex flex-col items-center {{ isset($category) && $category->icon ? 'hidden' : '' }}">
                                    <svg class="w-10 h-10 text-slate-300 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">SVG/PNG</span>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="icon" id="iconInput" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="bg-white rounded-3xl shadow-soft border border-slate-200 p-6">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Cover Image</label>
                    <div class="relative group">
                        <div class="aspect-video w-full rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-primary-300">
                            @if(isset($category) && $category->image)
                                <img id="imagePreview" src="{{ asset('storage/'.$category->image) }}" class="w-full h-full object-cover">
                            @else
                                <img id="imagePreview" class="hidden w-full h-full object-cover">
                                <div id="imagePlaceholder" class="flex flex-col items-center {{ isset($category) && $category->image ? 'hidden' : '' }}">
                                    <svg class="w-10 h-10 text-slate-300 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">800x600</span>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="image" id="imageInput" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                    </div>
                </div>

                <button type="submit" class="w-full py-4 rounded-2xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    {{ isset($category) ? 'Update Category' : 'Create Category' }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('iconInput').onchange = evt => {
        const [file] = document.getElementById('iconInput').files
        if (file) {
            document.getElementById('iconPreview').src = URL.createObjectURL(file)
            document.getElementById('iconPreview').classList.remove('hidden')
            if(document.getElementById('iconPlaceholder')) document.getElementById('iconPlaceholder').classList.add('hidden')
        }
    }
    document.getElementById('imageInput').onchange = evt => {
        const [file] = document.getElementById('imageInput').files
        if (file) {
            document.getElementById('imagePreview').src = URL.createObjectURL(file)
            document.getElementById('imagePreview').classList.remove('hidden')
            if(document.getElementById('imagePlaceholder')) document.getElementById('imagePlaceholder').classList.add('hidden')
        }
    }
</script>
@endsection
