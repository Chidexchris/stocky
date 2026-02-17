@extends('layouts.superadmin')

@section('title', 'Subscription Plans')
@section('page_title', 'Revenue Configurations')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach($plans as $plan)
    <div class="premium-card p-10 flex flex-col items-center text-center transition-all hover:-translate-y-2 border-t-4 {{ $plan->name == 'Premium' ? 'border-purple-500' : 'border-blue-500' }}">
        <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center text-2xl mb-6">
            <i class="bi {{ $plan->name == 'Premium' ? 'bi-gem text-purple-500' : 'bi-award text-blue-500' }}"></i>
        </div>
        
        <h3 class="text-2xl font-black text-white mb-2 uppercase tracking-tighter">{{ $plan->name }}</h3>
        <p class="text-slate-500 text-xs mb-8">{{ $plan->description }}</p>

        <div class="mb-10 text-center">
            <div class="flex items-baseline justify-center">
                <span class="text-4xl font-black text-white">${{ number_format($plan->price / 100, 2) }}</span>
                <span class="text-slate-500 text-[10px] uppercase tracking-widest font-bold ml-1">/ Mo</span>
            </div>
            <div class="flex items-baseline justify-center mt-1">
                <span class="text-xl font-bold text-blue-400">${{ number_format($plan->price_annual / 100, 2) }}</span>
                <span class="text-slate-500 text-[10px] uppercase tracking-widest font-bold ml-1">/ Yr</span>
            </div>
        </div>

        <div class="w-full space-y-4 mb-10 text-left border-t border-white/5 pt-8">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Identity Limit</span>
                <span class="text-sm font-bold text-white">{{ $plan->limit_users }} Users</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Node Limit</span>
                <span class="text-sm font-bold text-white">{{ $plan->limit_stores }} Store</span>
            </div>
        </div>

        <div class="flex gap-2 w-full">
            <a href="{{ route('saas.plans.edit', $plan->id) }}" class="flex-1 py-4 bg-slate-800 hover:bg-slate-700 text-white font-black uppercase text-[10px] tracking-[0.2em] rounded-xl transition-all block text-center">
                Edit
            </a>
            <form action="{{ route('saas.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Immediately purge this tier? This cannot be undone if no businesses are attached.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-4 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white rounded-xl transition-all">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach

    <a href="{{ route('saas.plans.create') }}" class="premium-card p-10 flex flex-col items-center justify-center text-center border-2 border-dashed border-white/10 hover:border-blue-500/50 transition-all group">
        <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-all">
            <i class="bi bi-plus-lg text-slate-500 group-hover:text-blue-500"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-500 uppercase tracking-widest">Initialize New Tier</h3>
    </a>
</div>
@endsection
