@php
  $user = auth()->user();

  // Don't show for admins or dismissed users
  if (!$user || $user->isAdmin() || $user->guide_dismissed) return;

  $percent = $user->guideCompletionPercent();

  // Don't show at 100%
  if ($percent >= 100) return;

  if ($user->isProvider()) {
    $profile = $user->providerProfile;
    $steps = [
      [
        'label'  => 'Complete your profile',
        'done'   => $profile !== null,
        'route'  => route('provider.onboarding.profile'),
        'detail' => 'Add business name, description, and contact info',
      ],
      [
        'label'  => 'Upload identity documents',
        'done'   => $profile && $profile->documents->count() > 0,
        'route'  => route('provider.onboarding.documents'),
        'detail' => 'Submit required documents for verification',
      ],
      [
        'label'  => 'Get verified by admin',
        'done'   => $profile && $profile->verification_status === 'approved',
        'route'  => route('provider.onboarding.pending'),
        'detail' => 'Admin reviews your documents (24–48h)',
      ],
      [
        'label'  => 'Add service areas',
        'done'   => $profile && $profile->serviceAreas->count() > 0,
        'route'  => route('provider.areas.index'),
        'detail' => 'Set the zones where you offer your services',
      ],
      [
        'label'  => 'Upload gallery photos',
        'done'   => $profile && $profile->gallery->count() > 0,
        'route'  => route('provider.gallery.index'),
        'detail' => 'Show your past work to attract more customers',
      ],
    ];
  } else {
    $steps = [
      [
        'label'  => 'Complete your profile',
        'done'   => (bool) $user->onboarding_profile_done,
        'route'  => route('customer.onboarding.profile'),
        'detail' => 'Add a photo and short bio',
      ],
      [
        'label'  => 'Add your address',
        'done'   => $user->customerAddresses->count() > 0,
        'route'  => route('customer.onboarding.address'),
        'detail' => 'So providers can find you nearby',
      ],
    ];
  }

  $nextStep = collect($steps)->firstWhere('done', false);
@endphp

<div
  x-data="{
    open: {{ session('guide_minimized') ? 'false' : 'true' }},
    dismiss() {
      fetch('{{ route('guide.dismiss') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json' }
      });
      this.$el.remove();
    }
  }"
  class="fixed bottom-5 end-5 z-50 w-72 font-sans"
  style="font-family: 'Inter', system-ui, sans-serif;"
>
  <!-- Collapsed pill -->
  <div x-show="!open" @click="open = true"
       class="flex items-center gap-3 bg-white border border-slate-200 rounded-2xl shadow-lg px-4 py-3 cursor-pointer hover:shadow-xl transition">
    <div class="relative w-9 h-9 shrink-0">
      <svg class="w-9 h-9 -rotate-90" viewBox="0 0 36 36">
        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#E2E8F0" stroke-width="3"/>
        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#0F94EA" stroke-width="3"
                stroke-dasharray="{{ round($percent * 99.9 / 100, 1) }} 100"
                stroke-linecap="round"/>
      </svg>
      <span class="absolute inset-0 flex items-center justify-center text-[10px] font-bold text-primary-600">{{ $percent }}%</span>
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-xs font-bold text-slate-900">Profile Setup</p>
      <p class="text-[11px] text-slate-500 truncate">{{ $nextStep ? $nextStep['label'] : 'Almost done!' }}</p>
    </div>
    <svg class="w-4 h-4 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg>
  </div>

  <!-- Expanded panel -->
  <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
       class="bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden">

    <!-- Header -->
    <div class="flex items-center justify-between px-4 py-3 bg-primary-50 border-b border-primary-100">
      <div class="flex items-center gap-2.5">
        <div class="relative w-8 h-8 shrink-0">
          <svg class="w-8 h-8 -rotate-90" viewBox="0 0 36 36">
            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#BAE0FD" stroke-width="4"/>
            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#0F94EA" stroke-width="4"
                    stroke-dasharray="{{ round($percent * 99.9 / 100, 1) }} 100"
                    stroke-linecap="round"/>
          </svg>
          <span class="absolute inset-0 flex items-center justify-center text-[9px] font-bold text-primary-700">{{ $percent }}%</span>
        </div>
        <div>
          <p class="text-xs font-bold text-primary-800">Profile Setup Guide</p>
          <p class="text-[10px] text-primary-600">{{ collect($steps)->where('done', true)->count() }} of {{ count($steps) }} complete</p>
        </div>
      </div>
      <div class="flex items-center gap-1">
        <button @click="open = false" title="Minimise"
                class="p-1.5 text-primary-400 hover:text-primary-700 hover:bg-primary-100 rounded-lg transition">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <button @click="dismiss()" title="Don't show again"
                class="p-1.5 text-primary-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
    </div>

    <!-- Progress bar -->
    <div class="w-full bg-primary-100 h-1">
      <div class="bg-primary-500 h-1 transition-all duration-500" style="width: {{ $percent }}%"></div>
    </div>

    <!-- Steps list -->
    <div class="px-4 py-3 space-y-2.5 max-h-72 overflow-y-auto">
      @foreach($steps as $i => $step)
      <div class="flex items-start gap-3">
        <!-- Check / number -->
        @if($step['done'])
          <span class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-3.5 h-3.5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          </span>
        @elseif($step === $nextStep)
          <span class="w-6 h-6 rounded-full bg-primary-500 flex items-center justify-center shrink-0 mt-0.5 text-[10px] font-bold text-white">{{ $i + 1 }}</span>
        @else
          <span class="w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center shrink-0 mt-0.5 text-[10px] font-semibold text-slate-400">{{ $i + 1 }}</span>
        @endif

        <div class="flex-1 min-w-0">
          @if(!$step['done'] && $step['route'] !== '#')
            <a href="{{ $step['route'] }}" class="block text-xs font-semibold {{ $step === $nextStep ? 'text-primary-700' : 'text-slate-500' }} hover:text-primary-600 transition leading-tight">
              {{ $step['label'] }}
            </a>
          @else
            <p class="text-xs font-semibold {{ $step['done'] ? 'text-slate-400 line-through' : 'text-slate-500' }} leading-tight">
              {{ $step['label'] }}
            </p>
          @endif
          @if(!$step['done'])
            <p class="text-[10px] text-slate-400 mt-0.5 leading-tight">{{ $step['detail'] }}</p>
          @endif
        </div>
      </div>
      @endforeach
    </div>

    <!-- Footer action -->
    @if($nextStep && $nextStep['route'] !== '#')
    <div class="px-4 pb-4 pt-1">
      <a href="{{ $nextStep['route'] }}"
         class="block w-full text-center py-2 rounded-lg bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition">
        {{ $nextStep['label'] }} →
      </a>
    </div>
    @endif

  </div>
</div>
