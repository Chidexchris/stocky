@extends('layouts.superadmin')

@section('title', 'Global Transactions')
@section('page_title', 'Revenue Stream')

@section('content')
<div class="premium-card overflow-hidden">
    <div class="p-8 border-b border-white/5 flex justify-between items-center">
        <div>
            <h4 class="text-white font-bold tracking-tight">Transaction Ledger</h4>
            <p class="text-xs text-slate-500">Real-time tracking of all payment attempts across the platform.</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">Date</th>
                    <th class="px-8 py-4">Business</th>
                    <th class="px-8 py-4">Plan / Purpose</th>
                    <th class="px-8 py-4">Amount</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4">Gateway / Ref</th>
                    <th class="px-8 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($transactions as $transaction)
                <tr class="hover:bg-white/5 transition-all text-sm">
                    <td class="px-8 py-5 text-slate-400 font-mono text-xs">
                        {{ $transaction->created_at->format('M d, H:i') }}
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-white font-bold">{{ $transaction->business->name }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-tighter">{{ $transaction->plan->name ?? 'Custom Payment' }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-white font-mono font-bold">{{ number_format($transaction->amount / 100, 2) }}</span>
                    </td>
                    <td class="px-8 py-5">
                        @php
                            $statusColors = [
                                'completed' => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                                'pending' => 'text-amber-400 bg-amber-500/10 border-amber-500/20',
                                'failed' => 'text-red-400 bg-red-500/10 border-red-500/20',
                                'refunded' => 'text-blue-400 bg-blue-500/10 border-blue-500/20',
                            ];
                        @endphp
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest border {{ $statusColors[$transaction->status] ?? 'text-slate-400 bg-slate-800' }}">
                            {{ $transaction->status }}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-white font-bold uppercase">{{ $transaction->gateway_name }}</span>
                            <span class="text-[9px] text-slate-500 font-mono truncate max-w-[100px]">{{ $transaction->gateway_reference }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-right">
                        @if($transaction->status == 'completed')
                        <form action="{{ route('saas.finance.refund', $transaction) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this as refunded?')">
                            @csrf
                            <button type="submit" class="text-xs font-black text-blue-500 hover:text-blue-400 uppercase tracking-widest">Mark Refund</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-8 py-10 text-center text-slate-500 italic">No transactions recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
    <div class="px-8 py-6 border-t border-white/5">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection
