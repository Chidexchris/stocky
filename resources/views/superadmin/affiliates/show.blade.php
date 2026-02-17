@extends('layouts.superadmin')

@section('title', 'Manage Affiliate')
@section('page_title', 'Partner Intelligence')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Affiliate Stats -->
    <div class="lg:col-span-1 space-y-8">
        <div class="premium-card p-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-blue-500 rounded-2xl flex items-center justify-center text-white text-xl font-bold mr-4">
                    {{ strtoupper(substr($affiliate->user->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-white font-bold">{{ $affiliate->user->name }}</h3>
                    <p class="text-xs text-slate-500">{{ $affiliate->user->email }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="p-4 bg-slate-900/50 rounded-2xl border border-white/5">
                    <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-1">Account Status</p>
                    <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest {{ $affiliate->status === 'active' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500' }}">
                        {{ $affiliate->status }}
                    </span>
                </div>
                <div class="p-4 bg-slate-900/50 rounded-2xl border border-white/5">
                    <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-1">Current Balance</p>
                    <p class="text-xl font-bold text-emerald-500">${{ number_format($affiliate->balance, 2) }}</p>
                </div>
                <div class="p-4 bg-slate-900/50 rounded-2xl border border-white/5">
                    <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-1">Total Referrals</p>
                    <p class="text-xl font-bold text-white">{{ $affiliate->referral_count }}</p>
                </div>
                <div class="p-4 bg-slate-900/50 rounded-2xl border border-white/5">
                    <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-1">Affiliate Code</p>
                    <p class="text-lg font-mono font-bold text-blue-400">{{ $affiliate->affiliate_code }}</p>
                </div>
            </div>

            <form action="{{ route('saas.affiliates.update', $affiliate) }}" method="POST" class="mt-8 pt-8 border-t border-white/5">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-2 block">Adjust Balance</label>
                    <input type="number" name="balance" step="0.01" value="{{ $affiliate->balance }}" 
                        class="w-full bg-slate-900 border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl transition-all text-sm">
                    UPDATE BALANCE
                </button>
            </form>

            <form action="{{ route('saas.affiliates.toggle-status', $affiliate) }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="w-full {{ $affiliate->status === 'active' ? 'bg-red-500/10 hover:bg-red-500/20 text-red-400' : 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400' }} font-bold py-3 rounded-xl transition-all text-sm border border-white/5">
                    {{ $affiliate->status === 'active' ? 'SUSPEND PARTNER' : 'REACTIVATE PARTNER' }}
                </button>
            </form>

            <form action="{{ route('saas.affiliates.destroy', $affiliate) }}" method="POST" class="mt-4" onsubmit="return confirm('Remove affiliate status for this user? User account will remain.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 font-bold py-3 rounded-xl transition-all text-sm border border-red-500/20">
                    REMOVE AFFILIATE STATUS
                </button>
            </form>
        </div>
    </div>

    <!-- Referral Logs -->
    <div class="lg:col-span-2">
        <div class="premium-card overflow-hidden">
            <div class="p-8 border-b border-white/5">
                <h4 class="text-white font-bold tracking-tight">Referral History</h4>
                <p class="text-xs text-slate-500">Detailed log of all users referred by this partner.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4">Referred User</th>
                            <th class="px-8 py-4">Status</th>
                            <th class="px-8 py-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-sm">
                        @forelse($referrals as $referral)
                        <tr class="hover:bg-white/5 transition-all text-sm">
                            <td class="px-8 py-5">
                                <div>
                                    <p class="font-bold text-white mb-0.5">{{ $referral->referredUser->name }}</p>
                                    <p class="text-[10px] text-slate-500 font-mono">{{ $referral->referredUser->email }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest {{ $referral->status === 'active' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500' }}">
                                    {{ $referral->status }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-slate-500 text-xs">
                                {{ $referral->created_at->format('M d, Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-10 text-center text-slate-500 italic">No referral history available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($referrals->hasPages())
            <div class="px-8 py-6 border-t border-white/5">
                {{ $referrals->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
