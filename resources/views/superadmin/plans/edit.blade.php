@extends('layouts.superadmin')

@section('title', 'Edit Plan')
@section('page_title', 'Modify Plan Parameters')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-slate-800 rounded-2xl p-8 border border-white/5">
        <form action="{{ route('saas.plans.update', $plan->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Plan Identity</label>
                <input type="text" name="name" value="{{ old('name', $plan->name) }}" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500" required>
            </div>

            <!-- Price -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Monthly Fee ({{ config('cashier.currency_symbol', '$') }})</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $plan->price / 100) }}" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Annual Fee ({{ config('cashier.currency_symbol', '$') }})</label>
                    <input type="number" step="0.01" name="price_annual" value="{{ old('price_annual', $plan->price_annual / 100) }}" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500" required>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Foundational values for recurring revenue metrics and billing cycles.</p>

            <!-- Limits -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Node Limit (Stores)</label>
                    <input type="number" name="limit_stores" value="{{ old('limit_stores', $plan->limit_stores) }}" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Identity Limit (Users)</label>
                    <input type="number" name="limit_users" value="{{ old('limit_users', $plan->limit_users) }}" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Storage Limit (GB)</label>
                    <input type="number" name="limit_storage" value="{{ old('limit_storage', $plan->limit_storage) }}" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Currency Limit</label>
                    <input type="number" name="limit_currencies" value="{{ old('limit_currencies', $plan->limit_currencies) }}" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500" required>
                </div>
            </div>

            <!-- Features -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Capabilities (One per line)</label>
                <textarea name="features" rows="8" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500" placeholder="e.g. Real-time Inventory tracking&#10;Advanced Analytics">{{ old('features', is_array($plan->features) ? implode("\n", $plan->features) : '') }}</textarea>
                <p class="text-xs text-slate-500 mt-1">Each line defines a distinct capability of this plan.</p>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Manifest</label>
                <textarea name="description" rows="3" class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500">{{ old('description', $plan->description) }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-white/5">
                <a href="{{ route('saas.plans.index') }}" class="text-slate-500 text-xs font-bold uppercase tracking-widest hover:text-white transition-colors">Abort</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-lg shadow-blue-500/20">
                    Commit Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
