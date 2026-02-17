@extends('layouts.superadmin')

@section('title', 'Withdrawal Requests')
@section('page_title', 'Payout Management')

@section('content')
<div class="premium-card overflow-hidden">
    <div class="p-8 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h4 class="text-white font-bold tracking-tight">Active Requests</h4>
            <p class="text-xs text-slate-500">Monitoring all affiliate payout transactions.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('saas.withdrawals.index', ['status' => 'all']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ request('status', 'all') === 'all' ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400 border border-white/5' }} transition-all">ALL</a>
            <a href="{{ route('saas.withdrawals.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ request('status') === 'pending' ? 'bg-amber-500 text-white' : 'bg-slate-800 text-slate-400 border border-white/5' }} transition-all">PENDING</a>
            <a href="{{ route('saas.withdrawals.index', ['status' => 'completed']) }}" class="px-4 py-2 rounded-xl text-xs font-bold {{ request('status') === 'completed' ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-400 border border-white/5' }} transition-all">COMPLETED</a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">Partner</th>
                    <th class="px-8 py-4">Amount</th>
                    <th class="px-8 py-4">Bank Details</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4">Reference</th>
                    <th class="px-8 py-4">Requested</th>
                    <th class="px-8 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($withdrawals as $withdrawal)
                <tr class="hover:bg-white/5 transition-all text-sm">
                    <td class="px-8 py-5">
                        @if($withdrawal->affiliate && $withdrawal->affiliate->user)
                        <div>
                            <p class="font-bold text-white mb-0.5">{{ $withdrawal->affiliate->user->name }}</p>
                            <p class="text-[10px] text-slate-500 font-mono">{{ $withdrawal->affiliate->user->email }}</p>
                        </div>
                        @else
                        <span class="text-slate-500 italic">Unknown Partner</span>
                        @endif
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-white font-bold">${{ number_format($withdrawal->amount, 2) }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="text-xs">
                            <p class="text-slate-300 font-semibold">{{ $withdrawal->bank_name }}</p>
                            <p class="text-slate-500 font-mono">{{ $withdrawal->account_number }}</p>
                            <p class="text-[10px] text-slate-500 uppercase">{{ $withdrawal->account_name }}</p>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest {{ $withdrawal->status === 'completed' ? 'bg-emerald-500/10 text-emerald-500' : ($withdrawal->status === 'rejected' ? 'bg-red-500/10 text-red-500' : 'bg-amber-500/10 text-amber-500') }}">
                            {{ $withdrawal->status }}
                        </span>
                    </td>
                    <td class="px-8 py-5 font-mono text-[10px] text-slate-400">
                        {{ $withdrawal->reference ?: 'N/A' }}
                    </td>
                    <td class="px-8 py-5 text-slate-500 text-xs">
                        {{ $withdrawal->created_at->format('M d, Y H:i') }}
                    </td>
                    <td class="px-8 py-5 text-right">
                        @if($withdrawal->status === 'pending')
                        <div class="flex justify-end gap-2">
                            <form action="{{ route('saas.withdrawals.update', $withdrawal) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="p-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-500 rounded-lg transition-all" title="Mark as Completed">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            <form action="{{ route('saas.withdrawals.update', $withdrawal) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="p-2 bg-red-500/10 hover:bg-red-500/20 text-red-500 rounded-lg transition-all" title="Reject Request">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="text-[10px] text-slate-600 font-black uppercase tracking-widest italic">SETTLED</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-8 py-10 text-center text-slate-500 italic">No withdrawal requests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($withdrawals->hasPages())
    <div class="px-8 py-6 border-t border-white/5">
        {{ $withdrawals->links() }}
    </div>
    @endif
</div>
@endsection
