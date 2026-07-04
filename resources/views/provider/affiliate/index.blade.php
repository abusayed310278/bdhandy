@extends('layouts.dashboard')
@section('title', 'Affiliate Program')

@section('content')
<div class="space-y-6 text-sm">

  <div>
    <h2 class="text-xl font-bold text-slate-900">Affiliate Program</h2>
    <p class="text-slate-500 text-xs mt-0.5">Earn 50% commission when a referred provider activates their first paid plan</p>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
      <p class="text-xs font-medium text-slate-500">Total Referrals</p>
      <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalReferrals }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
      <p class="text-xs font-medium text-slate-500">Total Earned</p>
      <p class="text-2xl font-bold text-slate-900 mt-1">৳{{ number_format($affiliate->total_earnings, 0) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
      <p class="text-xs font-medium text-slate-500">Pending Commission</p>
      <p class="text-2xl font-bold text-amber-600 mt-1">৳{{ number_format($pendingEarnings, 0) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
      <p class="text-xs font-medium text-slate-500">Available Balance</p>
      <p class="text-2xl font-bold text-green-600 mt-1">৳{{ number_format($balance, 0) }}</p>
    </div>
  </div>

  {{-- Referral link + code --}}
  <div class="bg-primary-50 border border-primary-100 rounded-2xl p-5 space-y-4">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h3 class="font-semibold text-primary-900">Your Referral Link</h3>
        <p class="text-xs text-primary-700 mt-0.5">Share this link. When a new provider signs up and pays their first plan, you earn 50% of the plan price.</p>
      </div>
      <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase
        {{ $affiliate->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
        {{ $affiliate->status }}
      </span>
    </div>

    {{-- Full referral URL --}}
    <div x-data="{ copied: false }" class="flex items-center gap-2">
      <input id="ref-url" type="text" readonly
        value="{{ route('register') }}?ref={{ $affiliate->referral_code }}"
        class="flex-1 bg-white border border-primary-200 rounded-xl px-4 py-2.5 text-xs text-slate-700 font-mono select-all focus:outline-none focus:ring-2 focus:ring-primary-300">
      <button @click="navigator.clipboard.writeText($el.previousElementSibling.value); copied = true; setTimeout(() => copied = false, 2000)"
        class="shrink-0 px-4 py-2.5 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition min-w-[80px] text-center">
        <span x-text="copied ? '✓ Copied!' : 'Copy Link'"></span>
      </button>
    </div>

    {{-- Code only --}}
    <div class="flex items-center gap-2">
      <div class="flex-1 bg-white border border-primary-200 rounded-xl px-4 py-2.5 font-mono text-sm font-bold text-primary-700 tracking-widest select-all">
        {{ $affiliate->referral_code }}
      </div>
      <div x-data="{ copied: false }">
        <button @click="navigator.clipboard.writeText('{{ $affiliate->referral_code }}'); copied = true; setTimeout(() => copied = false, 2000)"
          class="shrink-0 px-4 py-2.5 rounded-xl bg-white border border-primary-200 text-primary-700 text-xs font-bold hover:bg-primary-50 transition min-w-[80px] text-center">
          <span x-text="copied ? '✓ Copied!' : 'Copy Code'"></span>
        </button>
      </div>
    </div>

    <div class="flex flex-wrap gap-4 text-xs text-primary-800 pt-1">
      <span>Commission: <strong>50%</strong> of first paid plan</span>
      <span>·</span>
      <span>Minimum payout: <strong>৳{{ number_format($affiliate->minimum_payout, 0) }}</strong></span>
      <span>·</span>
      <span>One commission per referred provider</span>
    </div>
  </div>

  {{-- How it works --}}
  <div class="grid sm:grid-cols-3 gap-4">
    @foreach([
      ['step' => '1', 'title' => 'Share your link', 'desc' => 'Send your referral link to other service providers you know.'],
      ['step' => '2', 'title' => 'They register & subscribe', 'desc' => 'When they sign up using your link and activate a paid plan for the first time.'],
      ['step' => '3', 'title' => 'You earn 50%', 'desc' => 'You receive 50% of their first plan price as commission automatically.'],
    ] as $step)
    <div class="bg-white border border-slate-200 rounded-xl p-4 flex gap-3">
      <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 font-bold text-sm flex items-center justify-center shrink-0">{{ $step['step'] }}</div>
      <div>
        <p class="font-semibold text-slate-900 text-xs">{{ $step['title'] }}</p>
        <p class="text-slate-500 text-xs mt-0.5 leading-relaxed">{{ $step['desc'] }}</p>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Referrals table --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-900">Your Referrals</h3>
      @if($totalReferrals)
        <span class="text-xs text-slate-400">{{ $totalReferrals }} total</span>
      @endif
    </div>

    @if($referrals->isEmpty())
      <div class="p-12 text-center">
        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
          <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
        </div>
        <p class="text-slate-500 text-sm font-medium">No referrals yet</p>
        <p class="text-slate-400 text-xs mt-1">Share your referral link to start earning commissions</p>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3.5">Provider</th>
              <th class="px-5 py-3.5">Joined</th>
              <th class="px-5 py-3.5">Plan</th>
              <th class="px-5 py-3.5 text-right">Commission</th>
              <th class="px-5 py-3.5 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($referrals as $ref)
            @php
              $badge = match($ref->commission_status) {
                'pending'  => ['bg-amber-50 text-amber-700',  'Pending'],
                'approved' => ['bg-blue-50 text-blue-700',    'Approved'],
                'paid'     => ['bg-green-50 text-green-700',  'Paid'],
                'rejected' => ['bg-red-50 text-red-700',      'Rejected'],
                default    => ['bg-slate-50 text-slate-600',  $ref->commission_status],
              };
            @endphp
            <tr class="hover:bg-slate-50/50 transition">
              <td class="px-5 py-4">
                <div class="flex items-center gap-2.5">
                  @if($ref->referredUser?->photo)
                    <img src="{{ $ref->referredUser->photo }}" class="w-8 h-8 rounded-full object-cover">
                  @else
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs">
                      {{ strtoupper(substr($ref->referredUser?->name ?? '?', 0, 1)) }}
                    </div>
                  @endif
                  <div>
                    <p class="font-medium text-slate-900">{{ $ref->referredUser?->name ?? '—' }}</p>
                    <p class="text-[11px] text-slate-400">{{ $ref->referredUser?->email ?? '' }}</p>
                  </div>
                </div>
              </td>
              <td class="px-5 py-4 text-slate-500 text-xs">{{ $ref->created_at->format('d M Y') }}</td>
              <td class="px-5 py-4">
                @if($ref->subscription?->plan)
                  <span class="text-slate-900">{{ $ref->subscription->plan->name }}</span>
                  <span class="text-slate-400 text-[11px] block">{{ $ref->subscription->plan->duration_months }}mo</span>
                @else
                  <span class="text-slate-400">—</span>
                @endif
              </td>
              <td class="px-5 py-4 text-right font-bold text-slate-900">৳{{ number_format($ref->commission_amount, 0) }}</td>
              <td class="px-5 py-4 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $badge[0] }}">{{ $badge[1] }}</span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($referrals->hasPages())
        <div class="px-5 py-4 bg-slate-50 border-t border-slate-100">{{ $referrals->links() }}</div>
      @endif
    @endif
  </div>

  {{-- Payout info --}}
  <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs text-slate-600">
    <p class="font-semibold text-slate-700 mb-1">About payouts</p>
    <p>Commissions are marked <strong>Pending</strong> for 7 days after the referred provider's payment clears, then move to <strong>Approved</strong>. Payouts are processed manually by the admin team once your balance reaches ৳{{ number_format($affiliate->minimum_payout, 0) }}. Contact support to request a payout.</p>
  </div>

</div>
@endsection
