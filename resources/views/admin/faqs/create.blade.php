@extends('layouts.dashboard')

@section('title', isset($faq) ? 'Edit FAQ' : 'Add New FAQ')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ isset($faq) ? 'Edit FAQ' : 'Create New FAQ' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Provide clear answers to frequently asked questions</p>
        </div>
        <a href="{{ route('admin.faqs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
        <form action="{{ isset($faq) ? route('admin.faqs.update', $faq->id) : route('admin.faqs.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf
            @if(isset($faq)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Question <span class="text-red-500">*</span></label>
                <input type="text" name="question" value="{{ old('question', $faq->question ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. How do I book a service?" required>
                @error('question') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Answer <span class="text-red-500">*</span></label>
                <textarea name="answer" rows="6" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition resize-none" placeholder="Provide a detailed and helpful answer..." required>{{ old('answer', $faq->answer ?? '') }}</textarea>
                @error('answer') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $faq->sort_order ?? 0) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="0">
                    <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-wide">Lower numbers appear first.</p>
                    @error('sort_order') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                        <option value="active" {{ old('status', $faq->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $faq->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="pt-8 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.faqs.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-10 py-3 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">
                    {{ isset($faq) ? 'Save Changes' : 'Create FAQ' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
