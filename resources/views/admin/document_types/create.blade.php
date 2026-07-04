@extends('layouts.dashboard')

@section('title', isset($documentType) ? 'Edit Document Type' : 'Add New Document Type')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">{{ isset($documentType) ? 'Edit Document Type' : 'Create New Document Type' }}</h3>
            <p class="text-sm text-slate-500 mt-1">Configure required onboarding documents for service providers</p>
        </div>
        <a href="{{ route('admin.document_types.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
        <form action="{{ isset($documentType) ? route('admin.document_types.update', $documentType->id) : route('admin.document_types.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf
            @if(isset($documentType)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Document Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $documentType->name ?? '') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition" placeholder="e.g. National ID, Trade License" required>
                @error('name') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Required For <span class="text-red-500">*</span></label>
                <select name="provider_type" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition appearance-none cursor-pointer" required>
                    <option value="both" {{ old('provider_type', $documentType->provider_type ?? '') == 'both' ? 'selected' : '' }}>Both Freelancer & Business</option>
                    <option value="freelancer" {{ old('provider_type', $documentType->provider_type ?? '') == 'freelancer' ? 'selected' : '' }}>Freelancer Only</option>
                    <option value="business" {{ old('provider_type', $documentType->provider_type ?? '') == 'business' ? 'selected' : '' }}>Business Only</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-wide">Specify which provider group must upload this document.</p>
                @error('provider_type') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Instructions</label>
                <textarea name="instruction" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 outline-none transition resize-none" placeholder="e.g. You need to submit a pdf file that contain both part of your NID">{{ old('instruction', $documentType->instruction ?? '') }}</textarea>
                <p class="text-[10px] text-slate-400 mt-1.5 uppercase tracking-wide">Guidelines shown to the provider during upload.</p>
                @error('instruction') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>


            <!-- Footer Actions -->
            <div class="pt-8 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.document_types.index') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                    Cancel
                </a>
                <button type="submit" class="px-10 py-3 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft">
                    {{ isset($documentType) ? 'Save Changes' : 'Create Type' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
