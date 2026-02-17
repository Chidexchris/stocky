@extends('layouts.superadmin')

@section('title', 'Platform Audit Trail')
@section('page_title', 'Administrative Governance')

@section('content')
<div class="premium-card overflow-hidden">
    <div class="p-8 border-b border-white/5">
        <h4 class="text-white font-bold tracking-tight">Security Event Logs</h4>
        <p class="text-xs text-slate-500">Immutable record of every administrative action performed in the master portal.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">Timestamp</th>
                    <th class="px-8 py-4">Administrator</th>
                    <th class="px-8 py-4">Action Type</th>
                    <th class="px-8 py-4">Target ID</th>
                    <th class="px-8 py-4">Event Context</th>
                    <th class="px-8 py-4 text-right">Source IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($logs as $log)
                <tr class="hover:bg-white/5 transition-all text-sm">
                    <td class="px-8 py-5 text-slate-400 font-mono text-xs">
                        {{ $log->created_at->format('M d, H:i:s') }}
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center">
                            <div class="w-6 h-6 rounded-full bg-blue-500/20 flex items-center justify-center text-[10px] text-blue-500 mr-2 uppercase font-black">
                                {{ substr($log->user->name, 0, 1) }}
                            </div>
                            <span class="text-white font-bold">{{ $log->user->name }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        @php
                            $actionColors = [
                                'impersonate_start' => 'text-purple-400 bg-purple-500/10',
                                'delete_business' => 'text-red-400 bg-red-500/10',
                                'terminate_trial' => 'text-amber-400 bg-amber-500/10',
                                'verify_business' => 'text-emerald-400 bg-emerald-500/10',
                                'create_plan' => 'text-blue-400 bg-blue-500/10',
                            ];
                        @endphp
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest border border-white/5 {{ $actionColors[$log->action] ?? 'text-slate-400 bg-slate-800' }}">
                            {{ str_replace('_', ' ', $log->action) }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-slate-500 font-mono text-xs">
                        {{ $log->target_id ? '#' . str_pad($log->target_id, 4, '0', STR_PAD_LEFT) : '-' }}
                    </td>
                    <td class="px-8 py-5">
                        <div class="max-w-xs truncate text-[10px] text-slate-500 font-mono bg-slate-900/50 p-2 rounded" title="{{ json_encode($log->details) }}">
                            @foreach($log->details ?? [] as $key => $value)
                                <span class="text-blue-400">{{ $key }}:</span> {{ is_scalar($value) ? $value : '...' }}@if(!$loop->last), @endif
                            @endforeach
                        </div>
                    </td>
                    <td class="px-8 py-5 text-right text-slate-500 font-mono text-[10px]">
                        {{ $log->ip_address }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="px-8 py-6 border-t border-white/5">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
