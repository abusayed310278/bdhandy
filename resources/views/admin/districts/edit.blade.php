@extends('layouts.dashboard')

@section('title', isset($district) ? 'Edit District' : 'Add New District')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ isset($district) ? 'Edit District' : 'Create New District' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Configure administrative districts for regional categorization</p>
        </div>
        <a href="{{ route('admin.districts.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
        <form action="{{ isset($district) ? route('admin.districts.update', $district->id) : route('admin.districts.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf
            @if(isset($district)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Division <span class="text-red-500">*</span></label>
                <select name="division_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                    <option value="">Select Division</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ old('division_id', $district->division_id ?? '') == $division->id ? 'selected' : '' }}>{{ $division->name }} ({{ $division->country->name }})</option>
                    @endforeach
                </select>
                @error('division_id') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">District Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $district->name ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. Dhaka District" required>
                @error('name') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Slug <span class="text-red-500">*</span></label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $district->slug ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. dhaka-district" required>
                <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-wide">Used for URL identification. Must be unique.</p>
                @error('slug') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <!-- Footer Actions -->
            <div class="pt-6 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.districts.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-10 py-3 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">
                    {{ isset($district) ? 'Save Changes' : 'Create District' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('name').addEventListener('input', function() {
        let name = this.value;
        let slug = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
        document.getElementById('slug').value = slug;
    });
</script>
@endsection
