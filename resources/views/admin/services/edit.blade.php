@extends('layouts.dashboard')

@section('title', isset($service) ? 'Edit Service' : 'Create Service')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ isset($service) ? 'Edit Service' : 'Create New Service' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Configure service parameters and multi-language content</p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <form action="{{ isset($service) ? route('admin.services.update', $service->id) : route('admin.services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if(isset($service)) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Multi-language Sections -->
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
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Service Name ({{ $lang->name }}) @if($lang->is_default)<span class="text-red-500">*</span>@endif</label>
                                    <input type="text" name="translations[{{ $lang->code }}][name]" value="{{ old('translations.'.$lang->code.'.name', isset($service) ? ($service->getTranslation('translations', $lang->code)['name'] ?? '') : '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. House Cleaning" @if($lang->is_default) required @endif>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Short Description ({{ $lang->name }})</label>
                                    <textarea name="translations[{{ $lang->code }}][description]" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition resize-none" placeholder="Briefly describe what this service includes...">{{ old('translations.'.$lang->code.'.description', isset($service) ? ($service->getTranslation('translations', $lang->code)['description'] ?? '') : '') }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-soft border border-slate-200 p-6 md:p-8">
                    <h4 class="text-sm font-bold text-slate-900 uppercase tracking-widest mb-6">Classification</h4>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Parent Category <span class="text-red-500">*</span></label>
                        <select name="category_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('category_id', isset($service) ? $service->category_id : '') == $category->id) ? 'selected' : '' }}>
                                    {{ $category->getTranslation('translations', $languages->where('is_default', true)->first()->code ?? $languages->first()->code) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Right: Assets & Status -->
            <div class="space-y-6">
                <!-- Image Section -->
                <div class="bg-white rounded-3xl shadow-soft border border-slate-200 p-6">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Service Thumbnail</label>
                    <div class="relative group">
                        <div class="aspect-square w-full rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-primary-300">
                            @if(isset($service) && $service->image)
                                <img id="preview" src="{{ asset('storage/'.$service->image) }}" class="w-full h-full object-cover">
                            @else
                                <img id="preview" class="hidden w-full h-full object-cover">
                                <div id="placeholder" class="flex flex-col items-center">
                                    <svg class="w-10 h-10 text-slate-300 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">800 x 800 Rec.</span>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="image" id="imageInput" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                        <div class="absolute inset-0 bg-primary-600/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                            <span class="bg-white px-4 py-2 rounded-lg shadow-sm text-xs font-bold text-primary-600 uppercase">Upload</span>
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div class="bg-white rounded-3xl shadow-soft border border-slate-200 p-6">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Publishing</label>
                    <div class="space-y-4">
                        <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 transition appearance-none cursor-pointer">
                            <option value="active" {{ (old('status', isset($service) ? $service->status : '') == 'active') ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ (old('status', isset($service) ? $service->status : '') == 'inactive') ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 rounded-2xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    {{ isset($service) ? 'Update Service' : 'Create Service' }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('imageInput').onchange = evt => {
        const [file] = document.getElementById('imageInput').files
        if (file) {
            document.getElementById('preview').src = URL.createObjectURL(file)
            document.getElementById('preview').classList.remove('hidden')
            if(document.getElementById('placeholder')) document.getElementById('placeholder').classList.add('hidden')
        }
    }
</script>
@endsection
