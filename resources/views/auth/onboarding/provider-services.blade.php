<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth/onboarding/provider-services.meta_title') }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:{50:'#F0F8FF',100:'#E0F1FE',200:'#BAE0FD',400:'#38ADF7',500:'#0F94EA',600:'#0277C7',700:'#0561A1'},accent:{50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',500:'#F97316',600:'#EA580C',700:'#C2410C'}}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>html,body{font-family:'Inter',system-ui,sans-serif;}[dir="rtl"] .rtl-flip{transform:scaleX(-1);}</style>
</head>
<body class="min-h-full text-slate-700 antialiased">
<div class="min-h-screen flex flex-col">

    <!-- Top bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-2xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 shrink-0" dir="ltr">
                <span class="w-8 h-8 rounded-lg bg-primary-500 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </span>
                <span class="font-bold text-slate-900">{{ config('app.name') }}</span>
            </a>
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                <span class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-xs">✓</span>
                <span class="text-slate-400 hidden sm:inline">{{ __('auth/onboarding/provider-services.profile') }}</span>
                <div class="w-6 border-t border-slate-300 hidden sm:block"></div>
                <span class="w-6 h-6 rounded-full bg-primary-500 text-white flex items-center justify-center font-bold text-xs">2</span>
                <span class="font-semibold text-primary-600 hidden sm:inline">{{ __('auth/onboarding/provider-services.services') }}</span>
                <div class="w-6 border-t border-slate-300 hidden sm:block"></div>
                <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs hidden sm:flex">3</span>
                <span class="hidden sm:inline">{{ __('auth/onboarding/provider-services.area') }}</span>
                <div class="w-6 border-t border-slate-300 hidden sm:block"></div>
                <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs hidden sm:flex">4</span>
                <span class="hidden sm:inline">{{ __('auth/onboarding/provider-services.documents') }}</span>
            </div>
        </div>
    </header>

    <main class="flex-1 py-8 px-4">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900">{{ __('auth/onboarding/provider-services.title') }}</h1>
                <p class="text-slate-500 text-sm mt-1">{{ __('auth/onboarding/provider-services.subtitle') }}</p>
            </div>

            @if($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    <ul class="space-y-1 list-disc list-inside">
                        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Pass PHP data safely to Alpine via a script block --}}
            <script>
                function servicesPageData() {
                    return {
                        categories:        @json($categoriesForJs),
                        currencies:        @json($currenciesForJs),
                        defaultCurrencyId: {{ $defaultCurrencyId }},
                        entries:           @json($existingServices),
                        hours:             @json($hoursForJs),
                        globalStart:       '09:00',
                        globalEnd:         '18:00',

                        newEntry() {
                            return {
                                category_id: '', service_id: '', title: '', description: '',
                                pricing_type: 'fixed', price_fixed: '', price_min: '', price_max: '',
                                currency_id: this.defaultCurrencyId,
                                duration_minutes: '', is_emergency: false
                            };
                        },
                        addEntry()        { this.entries.push(this.newEntry()); },
                        removeEntry(i)    { this.entries.splice(i, 1); },
                        servicesFor(catId){ const c = this.categories.find(c => c.id == catId); return c ? c.services : []; },
                        applyAllTimes()   {
                            this.hours.forEach(h => {
                                if (!h.closed) { h.start = this.globalStart; h.end = this.globalEnd; }
                            });
                        },
                        validate(e) {
                            if (this.entries.length === 0) {
                                e.preventDefault();
                                alert("{{ __('auth/onboarding/provider-services.alert_add_service') }}");
                                return;
                            }
                            for (const h of this.hours) {
                                if (!h.closed && (!h.start || !h.end)) {
                                    e.preventDefault();
                                    alert("{{ __('auth/onboarding/provider-services.alert_set_times') }}");
                                    return;
                                }
                            }
                        }
                    };
                }
            </script>

            <form method="POST" action="{{ route('provider.onboarding.services.store') }}"
                  class="space-y-5"
                  x-data="servicesPageData()"
                  @submit="validate($event)">
                @csrf

                {{-- ── Services ── --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-slate-900">
                            {{ __('auth/onboarding/provider-services.services_you_offer') }} <span class="text-red-500">*</span>
                        </h3>
                        <button type="button" @click="addEntry()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-50 border border-primary-200 text-primary-600 text-xs font-semibold hover:bg-primary-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            {{ __('auth/onboarding/provider-services.add_service') }}
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-if="entries.length === 0">
                            <div class="rounded-xl border-2 border-dashed border-slate-200 p-8 text-center text-slate-400 text-sm">
                                {{ __('auth/onboarding/provider-services.no_services_added') }}
                            </div>
                        </template>

                        <template x-for="(entry, idx) in entries" :key="idx">
                            <div class="rounded-xl border border-slate-200 p-4 space-y-3 relative">
                                <!-- Remove -->
                                <button type="button" @click="removeEntry(idx)"
                                    class="absolute top-3 right-3 w-6 h-6 flex items-center justify-center rounded-full text-slate-400 hover:bg-red-50 hover:text-red-500 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>

                                <div class="grid grid-cols-2 gap-3 pr-8">
                                    <!-- Category -->
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('auth/onboarding/provider-services.category') }} <span class="text-red-500">*</span></label>
                                        <select :name="'services[' + idx + '][category_id]'" x-model="entry.category_id"
                                            @change="entry.service_id = ''"
                                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition" required>
                                            <option value="">{{ __('auth/onboarding/provider-services.select_category') }}</option>
                                            <template x-for="cat in categories" :key="cat.id">
                                                <option :value="cat.id" x-text="cat.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <!-- Service -->
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('auth/onboarding/provider-services.service') }} <span class="text-red-500">*</span></label>
                                        <select :name="'services[' + idx + '][service_id]'" x-model="entry.service_id"
                                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition" required>
                                            <option value="">{{ __('auth/onboarding/provider-services.select_service') }}</option>
                                            <template x-for="svc in servicesFor(entry.category_id)" :key="svc.id">
                                                <option :value="svc.id" x-text="svc.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <!-- Title -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('auth/onboarding/provider-services.service_title') }} <span class="text-red-500">*</span></label>
                                    <input type="text" :name="'services[' + idx + '][title]'" x-model="entry.title"
                                        placeholder="{{ __('auth/onboarding/provider-services.title_placeholder') }}"
                                        class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition" required>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('auth/onboarding/provider-services.description') }}</label>
                                    <textarea :name="'services[' + idx + '][description]'" x-model="entry.description" rows="2"
                                        placeholder="{{ __('auth/onboarding/provider-services.description_placeholder') }}"
                                        class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition resize-none"></textarea>
                                </div>

                                <!-- Pricing type + currency -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('auth/onboarding/provider-services.pricing_type') }} <span class="text-red-500">*</span></label>
                                        <select :name="'services[' + idx + '][pricing_type]'" x-model="entry.pricing_type"
                                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition" required>
                                            <option value="fixed">{{ __('auth/onboarding/provider-services.fixed_price') }}</option>
                                            <option value="range">{{ __('auth/onboarding/provider-services.price_range') }}</option>
                                            <option value="hourly">{{ __('auth/onboarding/provider-services.per_hour') }}</option>
                                            <option value="quote">{{ __('auth/onboarding/provider-services.quote_on_request') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('auth/onboarding/provider-services.currency') }} <span class="text-red-500">*</span></label>
                                        <select :name="'services[' + idx + '][currency_id]'" x-model="entry.currency_id"
                                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition" required>
                                            <template x-for="cur in currencies" :key="cur.id">
                                                <option :value="cur.id" x-text="cur.symbol + ' ' + cur.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <!-- Price fields -->
                                <div x-show="entry.pricing_type === 'fixed' || entry.pricing_type === 'hourly'">
                                    <label class="block text-xs font-medium text-slate-600 mb-1">
                                        {{ __('auth/onboarding/provider-services.price') }} <span x-show="entry.pricing_type === 'hourly'" class="text-slate-400">{{ __('auth/onboarding/provider-services.per_hour_label') }}</span>
                                    </label>
                                    <input type="number" :name="'services[' + idx + '][price_fixed]'" x-model="entry.price_fixed"
                                        step="0.01" min="0" placeholder="0.00"
                                        class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
                                </div>
                                <div x-show="entry.pricing_type === 'range'" class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('auth/onboarding/provider-services.min_price') }}</label>
                                        <input type="number" :name="'services[' + idx + '][price_min]'" x-model="entry.price_min"
                                            step="0.01" min="0" placeholder="0.00"
                                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('auth/onboarding/provider-services.max_price') }}</label>
                                        <input type="number" :name="'services[' + idx + '][price_max]'" x-model="entry.price_max"
                                            step="0.01" min="0" placeholder="0.00"
                                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 items-end">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('auth/onboarding/provider-services.duration_minutes') }}</label>
                                        <input type="number" :name="'services[' + idx + '][duration_minutes]'" x-model="entry.duration_minutes"
                                            min="1" placeholder="{{ __('auth/onboarding/provider-services.duration_placeholder') }}"
                                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
                                    </div>
                                    <div class="pb-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="hidden" :name="'services[' + idx + '][is_emergency]'" value="0">
                                            <input type="checkbox" :name="'services[' + idx + '][is_emergency]'" value="1"
                                                x-model="entry.is_emergency"
                                                class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-100">
                                            <span class="text-xs font-medium text-slate-700">{{ __('auth/onboarding/provider-services.emergency_service') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addEntry()"
                        class="mt-4 w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border-2 border-dashed border-primary-200 text-primary-600 text-sm font-semibold hover:border-primary-400 hover:bg-primary-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        {{ __('auth/onboarding/provider-services.add_more_service') }}
                    </button>
                </div>

                {{-- ── Business Hours ── --}}
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-sm font-semibold text-slate-900">
                            {{ __('auth/onboarding/provider-services.business_hours') }} <span class="text-red-500">*</span>
                        </h3>
                    </div>

                    {{-- Apply all times --}}
                    <div class="flex flex-wrap items-center gap-2 mb-4 p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="text-xs text-slate-500 font-medium shrink-0">{{ __('auth/onboarding/provider-services.apply_to_all_open_days') }}</span>
                        <input type="time" x-model="globalStart"
                            class="rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-slate-900 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
                        <span class="text-slate-400 text-xs">{{ __('auth/onboarding/provider-services.to') }}</span>
                        <input type="time" x-model="globalEnd"
                            class="rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-slate-900 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
                        <button type="button" @click="applyAllTimes()"
                            class="px-3 py-1.5 rounded-lg bg-primary-500 text-white text-xs font-semibold hover:bg-primary-600 transition shrink-0">
                            {{ __('auth/onboarding/provider-services.apply') }}
                        </button>
                    </div>

                    <div class="space-y-1">
                        <template x-for="hour in hours" :key="hour.dayId">
                            <div class="flex items-center gap-3 py-2 border-b border-slate-100 last:border-0">
                                {{-- Hidden inputs for form submission --}}
                                <input type="hidden" :name="'hours[' + hour.dayId + '][day_of_week_id]'" :value="hour.dayId">
                                <input type="hidden" :name="'hours[' + hour.dayId + '][is_closed]'" :value="hour.closed ? '1' : '0'">
                                <input type="hidden" :name="'hours[' + hour.dayId + '][start_time]'" :value="!hour.closed ? hour.start : ''">
                                <input type="hidden" :name="'hours[' + hour.dayId + '][end_time]'"   :value="!hour.closed ? hour.end   : ''">

                                {{-- Day label --}}
                                <span class="w-24 text-xs font-semibold text-slate-700 shrink-0" x-text="hour.name"></span>

                                {{-- Open/Closed toggle --}}
                                <button type="button" @click="hour.closed = !hour.closed"
                                    class="flex items-center gap-2 shrink-0 focus:outline-none">
                                    <div class="relative w-10 h-5 rounded-full transition-colors"
                                         :class="!hour.closed ? 'bg-primary-500' : 'bg-slate-300'">
                                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-all duration-200"
                                             :class="!hour.closed ? 'translate-x-5' : 'translate-x-0'"></div>
                                    </div>
                                    <span class="text-xs font-medium w-12"
                                          :class="!hour.closed ? 'text-primary-600' : 'text-slate-400'"
                                          :x-text="!hour.closed ? '{{ __('auth/onboarding/provider-services.open') }}' : '{{ __('auth/onboarding/provider-services.closed') }}'"></span>
                                </button>

                                {{-- Time pickers (visible when open) --}}
                                <div class="flex items-center gap-2 flex-1 min-w-0" x-show="!hour.closed" x-transition>
                                    <input type="time" x-model="hour.start"
                                        class="flex-1 min-w-0 rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-slate-900 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
                                    <span class="text-slate-400 text-xs shrink-0">–</span>
                                    <input type="time" x-model="hour.end"
                                        class="flex-1 min-w-0 rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-slate-900 text-xs focus:border-primary-500 focus:ring-2 focus:ring-primary-100 focus:outline-none transition">
                                </div>

                                <div x-show="hour.closed" class="flex-1 text-xs text-slate-400 italic">{{ __('auth/onboarding/provider-services.closed_all_day') }}</div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('provider.onboarding.profile') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        {{ __('auth/onboarding/provider-services.back') }}
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-primary-500 text-white text-sm font-semibold hover:bg-primary-600 active:bg-primary-700 transition">
                        {{ __('auth/onboarding/provider-services.save_continue') }}
                        <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
