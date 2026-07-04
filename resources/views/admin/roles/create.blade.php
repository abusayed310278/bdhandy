@extends('layouts.dashboard')

@section('title', 'Create Role')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Create New Role</h3>
            <p class="text-sm text-slate-500 mt-1">Define a role and assign module permissions</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to List
        </a>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Role Name -->
        <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h4 class="text-sm font-bold text-slate-900">Role Identity</h4>
                <p class="text-xs text-slate-500 mt-0.5">Define the primary name for this system role</p>
            </div>
            <div class="p-6">
                <label class="block">
                    <span class="block text-sm font-semibold text-slate-700 mb-1.5">Role Name <span class="text-red-500">*</span></span>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-100 transition outline-none"
                        placeholder="e.g. content-manager" required>
                    @error('name')
                        <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </label>
            </div>
        </div>

        <!-- Permissions Matrix -->
        <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Module Permissions</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Assign granular access levels across system modules</p>
                </div>
                <div class="flex items-center gap-3" x-data="{ globalAll: false }">
                    <button type="button" 
                        @click="globalAll = !globalAll; document.querySelectorAll('input[name=\'permissions[]\']').forEach(el => { el.checked = globalAll; el.dispatchEvent(new Event('change')); })"
                        class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-[11px] font-bold uppercase tracking-tight hover:bg-primary-50 hover:text-primary-600 transition shadow-sm"
                        x-text="globalAll ? 'Deselect All Permissions' : 'Select All Permissions'">
                        Select All Permissions
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-10" x-data="{ 
                toggleGroup(groupName, checked) {
                    document.querySelectorAll(`input[data-group='${groupName}']`).forEach(el => {
                        el.checked = checked;
                        el.dispatchEvent(new Event('change'));
                    });
                }
            }">
                @forelse($permissions as $group => $groupPermissions)
                    <div class="space-y-4" x-data="{ allSelected: false }">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <div class="flex items-center gap-3">
                                <span class="flex-shrink-0 w-2 h-2 rounded-full bg-primary-500"></span>
                                <h5 class="text-xs font-black text-slate-900 uppercase tracking-widest">{{ $group }}</h5>
                            </div>
                            <button type="button" 
                                @click="allSelected = !allSelected; toggleGroup('{{ $group }}', allSelected)"
                                class="text-[10px] font-bold uppercase tracking-tight transition px-2 py-1 rounded-md bg-slate-100 text-slate-500 hover:bg-primary-50 hover:text-primary-600"
                                x-text="allSelected ? 'Deselect Group' : 'Select Group'">
                                Select Group
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($groupPermissions as $permission)
                                <div class="relative flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-white hover:border-primary-100 hover:shadow-sm transition-all group">
                                    <div class="min-w-0 pr-4">
                                        <span class="block text-xs font-bold text-slate-700 group-hover:text-primary-700 transition-colors uppercase tracking-tight truncate">
                                            {{ str_replace(['view ', 'create ', 'edit ', 'delete '], '', $permission->name) }}
                                        </span>
                                        <span class="block text-[10px] text-slate-400 font-medium lowercase">
                                            {{ explode(' ', $permission->name)[0] }}
                                        </span>
                                    </div>
                                    
                                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                            data-group="{{ $group }}"
                                            {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-500 transition-colors"></div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                        <p class="text-slate-400 text-sm italic">No permissions found. Run the seeder first.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-soft">
            <button type="submit" class="px-8 py-3 rounded-xl bg-primary-500 text-white font-bold hover:bg-primary-600 transition shadow-soft flex items-center gap-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Role
            </button>
            <a href="{{ route('admin.roles.index') }}" class="px-8 py-3 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
