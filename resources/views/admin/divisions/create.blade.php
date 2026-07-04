@extends('layouts.dashboard')

@section('title', isset($division) ? 'Edit Division' : 'Add New Division')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ isset($division) ? 'Edit Division' : 'Create New Division' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Manage administrative divisions for regional categorization</p>
        </div>
        <a href="{{ route('admin.divisions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
        <form action="{{ isset($division) ? route('admin.divisions.update', $division->id) : route('admin.divisions.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf
            @if(isset($division)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Country <span class="text-red-500">*</span></label>
                <select name="country_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ old('country_id', $division->country_id ?? '') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
                @error('country_id') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Division Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $division->name ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. Dhaka" required>
                @error('name') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Slug <span class="text-red-500">*</span></label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $division->slug ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. dhaka" required>
                <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-wide">Used for URL identification. Must be unique.</p>
                @error('slug') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <!-- Footer Actions -->
            <div class="pt-6 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.divisions.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-10 py-3 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">
                    {{ isset($division) ? 'Save Changes' : 'Create Division' }}
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
