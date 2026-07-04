<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-primary-500 text-white font-medium hover:bg-primary-600 active:bg-primary-700 transition shadow-soft disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
