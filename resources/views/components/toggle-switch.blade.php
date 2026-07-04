@props(['name', 'checked' => false, 'id' => null])

@php
    $id = $id ?? 'switch-' . str_replace(['[', ']', '.'], '-', $name) . '-' . uniqid();
@endphp

<label for="{{ $id }}" class="relative inline-flex items-center cursor-pointer select-none">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" 
           id="{{ $id }}"
           name="{{ $name }}" 
           value="1" 
           {{ $checked ? 'checked' : '' }} 
           class="sr-only peer">
    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-500"></div>
</label>
