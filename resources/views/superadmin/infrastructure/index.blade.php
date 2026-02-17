@extends('layouts.superadmin')

@section('title', 'System Infrastructure')
@section('page_title', 'Platform Health & Resources')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Storage Stats -->
    <div class="lg:col-span-1 space-y-8">
        <div class="premium-card p-8 bg-blue-600/5 border-blue-500/20">
            <h5 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-4">Total Cloud Consumption</h5>
            <div class="text-4xl font-black text-white tracking-tighter mb-2">
                {{ format_size($totalStorage) }}
            </div>
            <p class="text-xs text-slate-500">Aggregated media and document storage across all isolated tenants.</p>
        </div>

        <div class="premium-card p-6 border-white/5">
            <h5 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-6">Utilities & Tools</h5>
            <div class="space-y-4">
                <form action="{{ route('saas.infrastructure.scan') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-between p-4 rounded-xl bg-slate-900 border border-white/5 hover:border-blue-500/30 transition-all group">
                        <div class="flex items-center">
                            <i class="bi bi-trash3 text-slate-500 group-hover:text-blue-500 mr-3"></i>
                            <span class="text-xs font-bold text-white">Scan Orphaned Files</span>
                        </div>
                        <i class="bi bi-chevron-right text-[10px] text-slate-700"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Storage Leaderboard -->
    <div class="lg:col-span-3">
        <div class="premium-card overflow-hidden">
            <div class="p-8 border-b border-white/5">
                <h4 class="text-white font-bold tracking-tight">Resource Utilization by Tenant</h4>
                <p class="text-xs text-slate-500">Monitoring high-traffic nodes and storage spikes.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4">Tenant Node</th>
                            <th class="px-8 py-4">Cloud Footprint</th>
                            <th class="px-8 py-4">System Status</th>
                            <th class="px-8 py-4 text-right">Maintenance Control</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($businesses as $b)
                        <tr class="hover:bg-white/5 transition-all text-sm">
                            <td class="px-8 py-5">
                                <span class="text-white font-bold">{{ $b['name'] }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center space-x-3 w-48">
                                    <div class="flex-grow bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                        @php
                                            $perc = $totalStorage > 0 ? ($b['storage_used'] / $totalStorage * 100) : 0;
                                        @endphp
                                        <div class="bg-blue-500 h-full" style="width: {{ $perc }}%"></div>
                                    </div>
                                    <span class="text-xs font-mono text-slate-400">{{ format_size($b['storage_used']) }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                @if($b['is_under_maintenance'])
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest border border-amber-500/20 text-amber-400 bg-amber-500/10">Under Maintenance</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 text-emerald-400 bg-emerald-500/10">Stable</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                <form action="{{ route('saas.infrastructure.maintenance', $b['id']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-black {{ $b['is_under_maintenance'] ? 'text-emerald-500' : 'text-amber-500' }} uppercase tracking-widest hover:underline">
                                        {{ $b['is_under_maintenance'] ? 'Resume Node' : 'Initialize Maintenance' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
