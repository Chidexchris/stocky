@extends('layouts.superadmin')

@section('title', 'Global Users')
@section('page_title', 'Identity Governance')

@section('content')
<div class="premium-card overflow-hidden">
    <div class="p-8 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h4 class="text-white font-bold tracking-tight">Cross-Tenant Identifiers</h4>
            <p class="text-xs text-slate-500">Monitoring all active identities in the platform mesh.</p>
        </div>
        <form action="{{ route('saas.users.index') }}" method="GET" class="relative group">
            <input type="text" name="search" value="{{ request('search') }}" 
                placeholder="Search Identity..." 
                class="bg-slate-900 border border-white/10 text-slate-300 text-xs font-semibold rounded-xl px-10 py-3 w-full md:w-64 focus:outline-none focus:border-blue-500 transition-all">
            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-500"></i>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">User Identity</th>
                    <th class="px-8 py-4">Node Association</th>
                    <th class="px-8 py-4">Authorization Tiers</th>
                    <th class="px-8 py-4">Last Activity</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($users as $user)
                <tr class="hover:bg-white/5 transition-all text-sm">
                    <td class="px-8 py-5">
                        <div>
                            <p class="font-bold text-white mb-0.5">{{ $user->name }}</p>
                            <p class="text-[10px] text-slate-500 font-mono">{{ $user->email }}</p>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        @if($user->business)
                            <span class="text-slate-400 font-semibold">{{ $user->business->name }}</span>
                        @elseif($user->email === 'super.admin@test.com')
                            <span class="text-blue-500 font-black uppercase text-[10px] tracking-widest italic">PLATFORM ROOT</span>
                        @else
                            <span class="text-slate-400 font-semibold">{{ $user->name }}</span>
                        @endif
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->getRoleNames() as $role)
                                <span class="bg-slate-800 text-slate-400 px-2 py-0.5 rounded text-[10px] font-bold border border-white/5">
                                    {{ $role }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-slate-500 text-xs italic">Encrypted Connection</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="px-8 py-6 border-t border-white/5">
        {{ $users->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
