{{--
  OTP Phone Verification Modal + Alpine.js component
  Usage:
    <div x-data="phoneVerify({ phone, isVerified, otpEnabled, sendUrl, verifyUrl })">
      ... phone input ...
      @include('partials.otp-modal')
    </div>
--}}

{{-- ── BACKDROP + MODAL ── --}}
<div
  x-show="modalOpen"
  x-transition:enter="transition ease-out duration-200"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-150"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
  @click.self="closeModal()"
  style="display:none">

  <div
    x-show="modalOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
    class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
    @click.stop>

    {{-- Close --}}
    <button type="button" @click="closeModal()"
      class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition z-10">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>

    {{-- ── SUCCESS STATE ── --}}
    <div x-show="verified" class="p-8 text-center" style="display:none">
      <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
      </div>
      <h3 class="text-xl font-bold text-slate-900 mb-1">Phone Verified!</h3>
      <p class="text-sm text-slate-500 mb-6">
        <span x-text="sendingPhone"></span> has been verified successfully.
      </p>
      <button type="button" @click="closeModal()"
        class="w-full py-3 rounded-xl bg-green-500 hover:bg-green-600 text-white text-sm font-bold transition">
        Done
      </button>
    </div>

    {{-- ── OTP INPUT STATE ── --}}
    <div x-show="!verified" class="p-8" style="display:none">

      {{-- Header --}}
      <div class="text-center mb-7">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center mx-auto mb-4 shadow-lg">
          <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3m-3 3h3m-3 3h3"/>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900">Verify Your Phone</h3>
        <p class="text-sm text-slate-500 mt-1.5">
          Code sent to<br>
          <span class="font-semibold text-slate-700" x-text="sendingPhone"></span>
        </p>
      </div>

      {{-- 6 OTP boxes --}}
      <div class="flex gap-2 justify-center mb-3" id="otp-box-row">
        <input type="text" maxlength="1" inputmode="numeric" autocomplete="one-time-code"
          class="otp-box w-11 h-14 text-center text-2xl font-bold rounded-xl border-2 border-slate-200 bg-slate-50 focus:border-primary-500 focus:bg-white focus:outline-none transition text-slate-900"
          :class="otpError ? 'border-red-400 bg-red-50' : ''"
          @input="otpInput($event,0)" @keydown="otpKeydown($event,0)" @paste.prevent="otpPaste($event)">
        <input type="text" maxlength="1" inputmode="numeric"
          class="otp-box w-11 h-14 text-center text-2xl font-bold rounded-xl border-2 border-slate-200 bg-slate-50 focus:border-primary-500 focus:bg-white focus:outline-none transition text-slate-900"
          :class="otpError ? 'border-red-400 bg-red-50' : ''"
          @input="otpInput($event,1)" @keydown="otpKeydown($event,1)" @paste.prevent="otpPaste($event)">
        <input type="text" maxlength="1" inputmode="numeric"
          class="otp-box w-11 h-14 text-center text-2xl font-bold rounded-xl border-2 border-slate-200 bg-slate-50 focus:border-primary-500 focus:bg-white focus:outline-none transition text-slate-900"
          :class="otpError ? 'border-red-400 bg-red-50' : ''"
          @input="otpInput($event,2)" @keydown="otpKeydown($event,2)" @paste.prevent="otpPaste($event)">
        <input type="text" maxlength="1" inputmode="numeric"
          class="otp-box w-11 h-14 text-center text-2xl font-bold rounded-xl border-2 border-slate-200 bg-slate-50 focus:border-primary-500 focus:bg-white focus:outline-none transition text-slate-900"
          :class="otpError ? 'border-red-400 bg-red-50' : ''"
          @input="otpInput($event,3)" @keydown="otpKeydown($event,3)" @paste.prevent="otpPaste($event)">
        <input type="text" maxlength="1" inputmode="numeric"
          class="otp-box w-11 h-14 text-center text-2xl font-bold rounded-xl border-2 border-slate-200 bg-slate-50 focus:border-primary-500 focus:bg-white focus:outline-none transition text-slate-900"
          :class="otpError ? 'border-red-400 bg-red-50' : ''"
          @input="otpInput($event,4)" @keydown="otpKeydown($event,4)" @paste.prevent="otpPaste($event)">
        <input type="text" maxlength="1" inputmode="numeric"
          class="otp-box w-11 h-14 text-center text-2xl font-bold rounded-xl border-2 border-slate-200 bg-slate-50 focus:border-primary-500 focus:bg-white focus:outline-none transition text-slate-900"
          :class="otpError ? 'border-red-400 bg-red-50' : ''"
          @input="otpInput($event,5)" @keydown="otpKeydown($event,5)" @paste.prevent="otpPaste($event)">
      </div>

      {{-- Error --}}
      <p x-show="otpError" x-text="otpError" class="text-center text-xs text-red-600 mb-3 min-h-[1rem]"></p>

      {{-- Resend / Edit --}}
      <div class="flex items-center justify-between text-xs mb-5">
        <button type="button" @click="editPhone()"
          class="flex items-center gap-1 text-slate-500 hover:text-primary-600 font-medium transition">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-2.828 1.172H7v-2a4 4 0 011.172-2.828z"/>
          </svg>
          Edit number
        </button>
        <div>
          <template x-if="countdown > 0">
            <span class="text-slate-400">
              Resend in <span class="font-bold text-slate-700" x-text="fmtCountdown(countdown)"></span>
            </span>
          </template>
          <template x-if="countdown === 0">
            <button type="button" @click="resendOtp()" :disabled="sending"
              class="text-primary-600 hover:text-primary-700 font-semibold transition disabled:opacity-50">
              <span x-text="sending ? 'Sending…' : 'Resend Code'"></span>
            </button>
          </template>
        </div>
      </div>

      {{-- Verify button --}}
      <button type="button" @click="verifyOtp()"
        :disabled="verifying || otp.join('').length < 6"
        class="w-full py-3 rounded-xl bg-primary-500 hover:bg-primary-600 text-white text-sm font-bold transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
        <svg x-show="verifying" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
        </svg>
        <span x-text="verifying ? 'Verifying…' : 'Verify Code'"></span>
      </button>
    </div>

  </div>
</div>
