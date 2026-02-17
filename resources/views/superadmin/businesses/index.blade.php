@extends('layouts.superadmin')

@section('title', 'Businesses')
@section('page_title', 'Tenant Ecosystem')

@section('content')
<div class="premium-card overflow-hidden">
    <div class="p-8 border-b border-white/5 flex items-center justify-between">
        <div>
            <h4 class="text-white font-bold tracking-tight">Active Registrations</h4>
            <p class="text-xs text-slate-500">Managing {{ $businesses->count() }} isolated business nodes.</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">Node Name</th>
                    <th class="px-8 py-4">Identifier</th>
                    <th class="px-8 py-4">Service Tier</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4">Verification</th>
                    <th class="px-8 py-4 text-center">Cloud Storage</th>
                    <th class="px-8 py-4 text-right">Administrative</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($businesses as $business)
                <tr class="hover:bg-white/5 transition-all">
                    <td class="px-8 py-5">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-400 mr-4 font-bold">
                                {{ substr($business->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white mb-0.5">{{ $business->name }}</p>
                                <p class="text-[10px] text-slate-500">Registered {{ $business->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-xs font-medium text-slate-400 font-mono bg-slate-800 px-2 py-1 rounded">#{{ str_pad($business->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="px-8 py-5">
                        @if(!$business->plan_id && $business->trial_ends_at)
                            @php
                                $daysLeft = ceil(now()->diffInDays($business->trial_ends_at, false));
                            @endphp
                            <div class="flex flex-col">
                                <span class="px-3 py-1 bg-amber-500/10 text-amber-500 rounded-full text-[10px] font-black uppercase tracking-widest inline-block text-center border border-amber-500/20">
                                    FREE TRIAL
                                </span>
                                <span class="text-[9px] font-bold mt-1 text-center {{ $daysLeft < 0 ? 'text-red-500' : 'text-slate-500' }}">
                                    @if($daysLeft < 0)
                                        EXPIRED {{ abs($daysLeft) }}d AGO
                                    @elseif($daysLeft == 0)
                                        ENDS TODAY
                                    @else
                                        {{ $daysLeft }}d REMAINING
                                    @endif
                                </span>
                            </div>
                        @else
                            <span class="px-3 py-1 bg-blue-500/10 text-blue-400 rounded-full text-[10px] font-black uppercase tracking-widest">
                                {{ $business->plan->name ?? 'NO TIER' }}
                            </span>
                        @endif
                    </td>
                    <td class="px-8 py-5">
                        @if($business->is_active)
                            <span class="flex items-center text-emerald-500 text-[10px] font-black uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                Online
                            </span>
                        @else
                            <span class="flex items-center text-red-500 text-[10px] font-black uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-2"></span>
                                Suspended
                            </span>
                        @endif
                    </td>
                    <td class="px-8 py-5">
                        @php
                            $v_colors = [
                                'pending' => 'bg-slate-800 text-slate-400',
                                'verified' => 'bg-emerald-500/10 text-emerald-500',
                                'suspended' => 'bg-red-500/10 text-red-500'
                            ];
                        @endphp
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border border-white/5 {{ $v_colors[$business->verification_status] ?? 'bg-slate-800' }}">
                            {{ $business->verification_status ?? 'UNKNOWN' }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        @php
                            $used = $business->storageUsed();
                            $limit = ($business->plan->limit_storage ?? 0) * 1024 * 1024 * 1024;
                            $percentage = $limit > 0 ? ($used / $limit) * 100 : 0;
                            $percentage = min($percentage, 100);
                        @endphp
                        <div class="flex flex-col items-center">
                            <span class="text-[10px] font-bold text-slate-400 mb-1 font-mono">{{ format_size($used) }} / {{ $business->plan->limit_storage ?? 0 }}GB</span>
                            <div class="w-24 h-1 bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full {{ $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-amber-500' : 'bg-blue-500') }}" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center space-x-2 text-right justify-end">
                            <!-- Support Login -->
                            <form action="{{ route('saas.businesses.impersonate', $business) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded-lg text-[10px] font-black uppercase tracking-tighter transition-all flex items-center">
                                    <i class="bi bi-person-bounding-box mr-1.5"></i> Support Login
                                </button>
                            </form>

                            <!-- Governance Link -->
                            <a href="{{ route('saas.businesses.features.edit', $business) }}" class="p-2 bg-slate-800 text-slate-400 hover:text-blue-400 rounded-lg transition-all" title="Feature Governance">
                                <i class="bi bi-gear-wide-connected"></i>
                            </a>

                            <!-- Verification Dropdown -->
                            <div class="relative group">
                                <button class="p-2 bg-slate-800 text-slate-400 hover:text-white rounded-lg transition-all" title="Verifcation Status">
                                    <i class="bi bi-shield-check"></i>
                                </button>
                                <div class="absolute right-0 bottom-full mb-2 w-32 bg-slate-900 border border-white/10 rounded-xl overflow-hidden shadow-2xl z-50 hidden group-hover:block">
                                    <form action="{{ route('saas.businesses.verify', $business) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="verified">
                                        <button class="w-full text-left px-4 py-2 text-[9px] font-bold text-emerald-500 hover:bg-emerald-500/10 uppercase tracking-widest">Verify</button>
                                    </form>
                                    <form action="{{ route('saas.businesses.verify', $business) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="pending">
                                        <button class="w-full text-left px-4 py-2 text-[9px] font-bold text-slate-400 hover:bg-white/5 uppercase tracking-widest">Reset</button>
                                    </form>
                                    <form action="{{ route('saas.businesses.verify', $business) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="suspended">
                                        <button class="w-full text-left px-4 py-2 text-[9px] font-bold text-red-500 hover:bg-red-500/10 uppercase tracking-widest">Suspend</button>
                                    </form>
                                </div>
                            </div>

                            <form action="{{ route('saas.businesses.toggle', $business) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 bg-slate-800 text-slate-400 hover:text-white rounded-lg transition-all" title="{{ $business->is_active ? 'Suspend Node' : 'Activate Node' }}">
                                    <i class="bi {{ $business->is_active ? 'bi-pause-fill text-red-400' : 'bi-play-fill text-emerald-400' }}"></i>
                                </button>
                            </form>

                            <form action="{{ route('saas.businesses.destroy', $business) }}" method="POST" onsubmit="return confirm('DANGER: This will permanently delete this business and all its associated data. This action cannot be undone. Proceed?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white rounded-lg transition-all" title="DELETE BUSINESS">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
