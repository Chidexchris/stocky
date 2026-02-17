@extends('layouts.superadmin')

@section('title', 'Subscriptions')
@section('page_title', 'Subscribed Users')

@section('content')
<div class="bg-slate-800 rounded-2xl p-6 border border-white/5">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left border-b border-white/5">
                    <th class="pb-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Business</th>
                    <th class="pb-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Plan</th>
                    <th class="pb-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Status</th>
                    <th class="pb-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Start Date</th>
                    <th class="pb-4 px-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Administrative</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($subscriptions as $subscription)
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="py-4 px-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-500 mr-3">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-white">{{ $subscription->business->name }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $subscription->business->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-purple-500/10 text-purple-500 border border-purple-500/20">
                            {{ $subscription->plan->name }}
                        </span>
                    </td>
                    <td class="py-4 px-4">
                        @php
                            $colors = [
                                'active' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                'trial' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                                'canceled' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                'expired' => 'bg-slate-500/10 text-slate-400 border-white/5',
                            ];
                            $status = $subscription->status;
                            if ($status === 'trial' && $subscription->ends_at && $subscription->ends_at->isPast()) {
                                $status = 'expired';
                            }
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $colors[$status] ?? 'bg-slate-800 text-slate-400' }}">
                            {{ ucfirst($status) }}
                        </span>
                    </td>
                    <td class="py-4 px-4 text-xs font-mono text-slate-400">
                        {{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : '-' }}
                    </td>
                    <td class="py-4 px-4 text-xs font-mono text-slate-400">
                        {{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : '-' }}
                    </td>
                    <td class="py-4 px-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            @if($subscription->status === 'active' || $subscription->status === 'trial')
                                <form action="{{ route('saas.subscriptions.cancel', $subscription) }}" method="POST" onsubmit="return confirm('Immediately restrict access for this business?')">
                                    @csrf
                                    <button type="submit" class="p-2 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition-all" title="Cancel Subscription">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('saas.subscriptions.activate', $subscription) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="p-2 bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500 hover:text-white rounded-lg transition-all" title="Activate Subscription">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                            @endif

                            <!-- Extend Trial Modal-ish Trigger -->
                            <div class="relative group">
                                <button class="p-2 bg-slate-700 text-slate-400 hover:text-white rounded-lg transition-all" title="Extend Trial">
                                    <i class="bi bi-calendar-plus"></i>
                                </button>
                                <div class="absolute right-0 bottom-full mb-2 w-48 bg-slate-900 border border-white/10 rounded-xl p-4 shadow-2xl z-50 hidden group-hover:block text-left">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Add Trial Days</p>
                                    <form action="{{ route('saas.subscriptions.extend-trial', $subscription) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <input type="number" name="days" value="7" min="1" max="90" class="w-full bg-slate-800 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-white">
                                        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-bold uppercase rounded-lg transition-all">
                                            Extend
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500">
                        No active nodes in the subscription mesh.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-8 border-t border-white/5 pt-6">
        {{ $subscriptions->links() }}
    </div>
</div>
@endsection
