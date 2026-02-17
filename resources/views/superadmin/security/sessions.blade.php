@extends('layouts.superadmin')

@section('title', 'Global Session Manager')
@section('page_title', 'Active Transmission Sessions')

@section('content')
<div class="premium-card overflow-hidden">
    <div class="p-8 border-b border-white/5 flex justify-between items-center">
        <div>
            <h4 class="text-white font-bold tracking-tight">System Session Ledger</h4>
            <p class="text-xs text-slate-500">Monitoring all active user connections across the platform.</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">User</th>
                    <th class="px-8 py-4">IP Address</th>
                    <th class="px-8 py-4">Device / User Agent</th>
                    <th class="px-8 py-4">Last Pulse</th>
                    <th class="px-8 py-4 text-right">Administrative</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($sessions as $session)
                <tr class="hover:bg-white/5 transition-all text-sm">
                    <td class="px-8 py-5">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center text-[10px] text-blue-500 mr-3 uppercase font-black">
                                {{ substr($session->user_name ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-white font-bold mb-0">{{ $session->user_name ?? 'Guest User' }}</p>
                                <p class="text-[10px] text-slate-500">{{ $session->user_email ?? 'Anonymous' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-slate-400 font-mono text-xs">
                        {{ $session->ip_address }}
                    </td>
                    <td class="px-8 py-5">
                        <div class="max-w-xs truncate text-[10px] text-slate-500 font-mono bg-slate-900/50 p-2 rounded" title="{{ $session->user_agent }}">
                            {{ $session->user_agent }}
                        </div>
                    </td>
                    <td class="px-8 py-5 text-slate-500 text-xs">
                        {{ date('M d, H:i:s', $session->last_activity) }}
                    </td>
                    <td class="px-8 py-5 text-right">
                        <form action="{{ route('saas.security.sessions.terminate', $session->id) }}" method="POST" onsubmit="return confirm('Immediately terminate this connection?')">
                            @csrf
                            <button type="submit" class="text-xs font-black text-red-500 hover:text-red-400 uppercase tracking-widest">Force Expire</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-10 text-center text-slate-500 italic">No active sessions detected (DB Driver enabled?).</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sessions->hasPages())
    <div class="px-8 py-6 border-t border-white/5">
        {{ $sessions->links() }}
    </div>
    @endif
</div>
@endsection
