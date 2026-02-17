@extends('layouts.superadmin')

@section('title', 'Promotional Coupons')
@section('page_title', 'Discount Management')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Coupon Form -->
    <div class="lg:col-span-1">
        <div class="premium-card p-8 sticky top-8">
            <h4 class="text-white font-bold mb-6 flex items-center">
                <i class="bi bi-ticket-perforated-fill text-blue-500 mr-2"></i> Create New Coupon
            </h4>
            
            <form action="{{ route('saas.coupons.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Coupon Code</label>
                    <input type="text" name="code" placeholder="e.g. WELCOME50" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all font-bold uppercase" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Type</label>
                        <select name="type" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all font-bold">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Value</label>
                        <input type="number" name="value" placeholder="e.g. 50" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all font-bold" required>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Usage Limit</label>
                    <input type="number" name="usage_limit" placeholder="Unlimited" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all font-bold">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Expiry Date</label>
                    <input type="date" name="expires_at" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 transition-all font-bold">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-4 rounded-xl transition-all shadow-lg shadow-blue-600/20 uppercase text-xs tracking-widest">
                    Initialize Coupon
                </button>
            </form>
        </div>
    </div>

    <!-- Coupon List -->
    <div class="lg:col-span-2">
        <div class="premium-card overflow-hidden">
            <div class="p-8 border-b border-white/5">
                <h4 class="text-white font-bold tracking-tight">Active Promotions</h4>
                <p class="text-xs text-slate-500">Manage all discount codes across the platform.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4">Code</th>
                            <th class="px-8 py-4">Benefit</th>
                            <th class="px-8 py-4">Usage</th>
                            <th class="px-8 py-4">Status</th>
                            <th class="px-8 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($coupons as $coupon)
                        <tr class="hover:bg-white/5 transition-all">
                            <td class="px-8 py-5">
                                <span class="text-white font-black font-mono tracking-widest bg-slate-800 px-3 py-1 rounded-lg border border-white/5">{{ $coupon->code }}</span>
                            </td>
                            <td class="px-8 py-5 text-sm font-bold text-white">
                                @if($coupon->type == 'percentage')
                                    {{ $coupon->value }}% OFF
                                @else
                                    ${{ number_format($coupon->value / 100, 2) }} OFF
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs text-slate-400 font-bold">{{ $coupon->times_used }} / {{ $coupon->usage_limit ?: '∞' }}</span>
                                    <div class="w-full bg-slate-900 h-1 rounded-full mt-1 overflow-hidden">
                                        <div class="bg-blue-500 h-full" style="width: {{ $coupon->usage_limit ? ($coupon->times_used / $coupon->usage_limit * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                @if($coupon->expires_at && $coupon->expires_at->isPast())
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest border border-red-500/20 text-red-400 bg-red-500/10">Expired</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 text-emerald-400 bg-emerald-500/10">Active</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                <form action="{{ route('saas.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Archive this coupon?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-500 hover:text-red-400 transition-all">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-center text-slate-500 italic">No coupons created yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
