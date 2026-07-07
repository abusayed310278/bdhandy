@extends('layouts.dashboard')

@section('title', 'Referral Payouts')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Referral Payouts</h3>
            <p class="text-sm text-slate-500 mt-1">Non-provider affiliate commissions awaiting manual bank/mobile-banking payout. Provider affiliates are credited to their wallet automatically.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700">{{ session('info') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-soft border border-slate-200 overflow-hidden text-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Affiliate</th>
                        <th class="px-6 py-4">Referred User</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Commission</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($referrals as $referral)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900">{{ $referral->affiliate->user->name ?? '—' }}</span>
                                <span class="block text-xs text-slate-400">{{ $referral->affiliate->referral_code }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-700">{{ $referral->referredUser->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-500 text-xs">{{ $referral->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">৳{{ number_format($referral->commission_amount, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.referrals.mark_paid', $referral->id) }}" method="POST" onsubmit="return confirm('Confirm this commission has been paid via bank/mobile banking?')">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary-500 text-white text-xs font-bold hover:bg-primary-600 transition shadow-sm">
                                        Mark Paid
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 italic">No pending referral payouts.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($referrals->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                {{ $referrals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
