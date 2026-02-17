@extends('layouts.superadmin')

@section('title', 'Feature Governance')
@section('page_title', 'Feature Overrides: ' . $business->name)

@section('content')
<div class="max-w-4xl">
    <div class="premium-card p-8 border-l-4 border-blue-600">
        <div class="flex items-center space-x-6 mb-8">
            <div class="w-16 h-16 rounded-2xl bg-blue-500/10 flex items-center justify-center text-3xl text-blue-500">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <h4 class="text-white font-black uppercase tracking-tight text-xl">Governance Control</h4>
                <p class="text-slate-500 text-sm">Manually override plan restrictions for <span class="text-blue-400 font-bold">{{ $business->name }}</span>.</p>
            </div>
        </div>

        <form action="{{ route('saas.businesses.features.update', $business) }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-8 border-b border-white/5">
                @foreach($availableFeatures as $feature)
                <label class="flex items-center p-4 rounded-xl bg-slate-900/50 border border-white/5 hover:border-blue-500/30 transition-all cursor-pointer group">
                    <div class="relative flex items-center">
                        <input type="checkbox" name="features[]" value="{{ $feature }}" 
                            class="w-5 h-5 rounded border-2 border-white/10 bg-transparent text-blue-600 focus:ring-blue-500 transition-all cursor-pointer"
                            {{ $business->hasFeatureOverride($feature) ? 'checked' : '' }}>
                    </div>
                    <div class="ml-4 flex-grow">
                        <span class="block text-sm font-bold {{ str_contains($feature, 'Beta:') ? 'text-blue-400' : 'text-white' }} group-hover:text-blue-400 transition-colors">
                            {{ $feature }}
                        </span>
                        @if(str_contains($feature, 'Beta:'))
                            <span class="text-[9px] font-black uppercase tracking-widest text-blue-500/50">Experimental Module</span>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>

            <div class="mt-8 flex items-center justify-between">
                <div class="text-xs text-slate-500 max-w-sm">
                    <i class="bi bi-info-circle mr-1"></i>
                    These settings will bypass the business's current plan (<span class="text-white font-bold">{{ $business->plan->name ?? 'No Plan' }}</span>).
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('saas.businesses.index') }}" class="px-6 py-3 rounded-xl bg-white/5 text-slate-400 font-bold hover:bg-white/10 transition-all text-sm uppercase tracking-widest">
                        Back to List
                    </a>
                    <button type="submit" class="px-8 py-3 rounded-xl bg-blue-600 text-white font-black hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/20 text-sm uppercase tracking-widest">
                        Save Governance Profile
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
