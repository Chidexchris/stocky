@extends('layouts.superadmin')

@section('title', 'Affiliate Network')
@section('page_title', 'Affiliate Ecosystem')

@section('content')
<div class="premium-card overflow-hidden">
    <div class="p-8 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h4 class="text-white font-bold tracking-tight">Active Affiliates</h4>
            <p class="text-xs text-slate-500">Managing partners and referral growth.</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">Partner</th>
                    <th class="px-8 py-4">Affiliate Code</th>
                    <th class="px-8 py-4">Referral Count</th>
                    <th class="px-8 py-4">Balance</th>
                    <th class="px-8 py-4">Joined</th>
                    <th class="px-8 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($affiliates as $affiliate)
                <tr class="hover:bg-white/5 transition-all text-sm">
                    <td class="px-8 py-5">
                        <div>
                            <p class="font-bold text-white mb-0.5">{{ $affiliate->user->name }}</p>
                            <p class="text-[10px] text-slate-500 font-mono">{{ $affiliate->user->email }}</p>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-lg text-xs font-bold border border-blue-500/20">
                            {{ $affiliate->affiliate_code }}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-white font-bold">{{ $affiliate->referral_count }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-emerald-500 font-bold">${{ number_format($affiliate->balance, 2) }}</span>
                    </td>
                    <td class="px-8 py-5 text-slate-500 text-xs">
                        {{ $affiliate->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-8 py-5 text-right space-x-2">
                        <a href="{{ route('saas.affiliates.show', $affiliate) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-lg transition-all border border-white/5">
                            MANAGE
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-10 text-center text-slate-500 italic">No affiliates found in the network.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($affiliates->hasPages())
    <div class="px-8 py-6 border-t border-white/5">
        {{ $affiliates->links() }}
    </div>
    @endif
</div>
@endsection
