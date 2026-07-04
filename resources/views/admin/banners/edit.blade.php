@extends('layouts.dashboard')

@section('title', isset($banner) ? 'Edit Banner' : 'Add New Banner')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ isset($banner) ? 'Edit Banner' : 'Create New Banner' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Design and publish promotional banners across the platform</p>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
        <form action="{{ isset($banner) ? route('admin.banners.update', $banner->id) : route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-8">
            @csrf
            @if(isset($banner)) @method('PUT') @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Image Upload Section -->
                <div class="lg:col-span-1 space-y-4">
                    <label class="block text-sm font-bold text-slate-700">Banner Image <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <div class="aspect-[3/1] lg:aspect-square w-full rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-primary-300">
                            @if(isset($banner) && $banner->image)
                                <img id="preview" src="{{ asset('storage/'.$banner->image) }}" class="w-full h-full object-cover">
                            @else
                                <img id="preview" class="hidden w-full h-full object-cover">
                                <div id="placeholder" class="flex flex-col items-center">
                                    <svg class="w-10 h-10 text-slate-300 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">1200 x 400 Recommended</span>
                                </div>
                            @endif
                        </div>
                        <input type="file" name="image" id="imageInput" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" {{ isset($banner) ? '' : 'required' }}>
                        <div class="absolute inset-0 bg-primary-600/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                            <span class="bg-white px-4 py-2 rounded-lg shadow-sm text-xs font-bold text-primary-600">Change Image</span>
                        </div>
                    </div>
                    @error('image') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <!-- Fields Section -->
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Banner Title</label>
                        <input type="text" name="title" value="{{ old('title', $banner->title ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. Summer Mega Sale 2024">
                        @error('title') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Target Link (URL)</label>
                        <input type="text" name="link" value="{{ old('link', $banner->link ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition font-mono text-xs" placeholder="e.g. https://example.com/offers">
                        @error('link') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Position <span class="text-red-500">*</span></label>
                            <select name="position" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                                <option value="main" {{ old('position', $banner->position ?? '') == 'main' ? 'selected' : '' }}>Main Slider</option>
                                <option value="sidebar" {{ old('position', $banner->position ?? '') == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                                <option value="popup" {{ old('position', $banner->position ?? '') == 'popup' ? 'selected' : '' }}>Popup Modal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Platform <span class="text-red-500">*</span></label>
                            <select name="type" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                                <option value="web" {{ old('type', $banner->type ?? '') == 'web' ? 'selected' : '' }}>Website Only</option>
                                <option value="app" {{ old('type', $banner->type ?? '') == 'app' ? 'selected' : '' }}>Mobile App Only</option>
                                <option value="both" {{ old('type', $banner->type ?? '') == 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date', isset($banner->start_date) ? $banner->start_date->format('Y-m-d') : '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">End Date</label>
                            <input type="date" name="end_date" value="{{ old('end_date', isset($banner->end_date) ? $banner->end_date->format('Y-m-d') : '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                                <option value="active" {{ old('status', $banner->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $banner->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="pt-8 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.banners.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-10 py-3 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">
                    {{ isset($banner) ? 'Save Changes' : 'Create Banner' }}
                </button>
            </div>
        </form>
    </div>
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
