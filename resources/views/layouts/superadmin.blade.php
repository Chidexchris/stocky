<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Platform Master</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    
    <!-- Modern Tech Stack -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0b0f1a;
            color: #94a3b8;
        }
        .sidebar {
            background: #0f172a;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        .nav-link {
            transition: all 0.2s;
            border-radius: 12px;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        .content-area {
            background: #0b0f1a;
        }
        .premium-card {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
        }
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 3px;
        }
    </style>
    @stack('page_css')
</head>
<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="sidebar w-64 flex-shrink-0 flex flex-col p-6 overflow-y-auto">
        <div class="mb-10 px-2">
            <h1 class="text-2xl font-black text-white tracking-tighter">PLATFORM<span class="text-blue-500">MASTER</span></h1>
            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-[0.2em]">SaaS Ecosystem Control</span>
        </div>

        <nav class="flex-grow space-y-2">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-4 mt-6">Core Intelligence</p>
            <a href="{{ route('saas.dashboard') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.dashboard') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-cpu-fill mr-3"></i>
                <span class="font-semibold text-sm">Control Center</span>
            </a>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-4 mt-8">Tenancy Control</p>
            <a href="{{ route('saas.businesses.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.businesses.*') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-layers-half mr-3"></i>
                <span class="font-semibold text-sm">All Businesses</span>
            </a>
            <a href="{{ route('saas.plans.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.plans.*') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-tags-fill mr-3"></i>
                <span class="font-semibold text-sm">Pricing Tiers</span>
            </a>
            <a href="{{ route('saas.subscriptions.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.subscriptions.*') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-credit-card-2-front-fill mr-3"></i>
                <span class="font-semibold text-sm">Live Subscriptions</span>
            </a>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-4 mt-8">User Access</p>
            <a href="{{ route('saas.users.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.users.*') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-shield-lock-fill mr-3"></i>
                <span class="font-semibold text-sm">Global User List</span>
            </a>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-4 mt-8">Governance</p>
            <a href="{{ route('saas.audit.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.audit.*') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-shield-shaded mr-3"></i>
                <span class="font-semibold text-sm">Audit Trail</span>
            </a>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-4 mt-8">Connectivity</p>
            <a href="{{ route('saas.broadcast.create') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.broadcast.*') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-megaphone-fill mr-3"></i>
                <span class="font-semibold text-sm">Global Broadcast</span>
            </a>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-4 mt-8">Financials</p>
            <a href="{{ route('saas.finance.transactions') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.finance.*') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-cash-stack mr-3"></i>
                <span class="font-semibold text-sm">Revenue Stream</span>
            </a>
            <a href="{{ route('saas.coupons.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.coupons.*') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-percentage mr-3"></i>
                <span class="font-semibold text-sm">Promotional Coupons</span>
            </a>
            <a href="{{ route('saas.affiliates.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.affiliates.*') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-people-fill mr-3"></i>
                <span class="font-semibold text-sm">Affiliate Network</span>
            </a>
            <a href="{{ route('saas.withdrawals.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.withdrawals.*') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-cash-stack mr-3"></i>
                <span class="font-semibold text-sm">Withdrawal Requests</span>
            </a>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-4 mt-8">System</p>
            <a href="{{ route('saas.infrastructure.index') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.infrastructure.*') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-cpu-fill mr-3"></i>
                <span class="font-semibold text-sm">System Health</span>
            </a>

            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-4 mt-8">Safety</p>
            <a href="{{ route('saas.security.sessions') }}" class="nav-link flex items-center px-4 py-3 {{ request()->routeIs('saas.security.*') ? 'active text-blue-500 bg-blue-500/10' : 'text-slate-400' }}">
                <i class="bi bi-key-fill mr-3"></i>
                <span class="font-semibold text-sm">Security Hub</span>
            </a>
        </nav>

        <div class="mt-auto pt-10">
            <form action="{{ route('superadmin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-slate-800/50 hover:bg-red-500/10 hover:text-red-400 text-slate-400 font-bold py-3 px-4 rounded-xl transition-all flex items-center justify-center text-sm">
                    <i class="bi bi-box-arrow-left mr-2"></i>
                    TERMINATE SESSION
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col h-full overflow-hidden">
        <!-- Header -->
        <header class="h-20 flex items-center justify-between px-10 border-b border-white/5">
            <div>
                <h2 class="text-white font-bold text-lg">@yield('page_title', 'Overview')</h2>
                <p class="text-xs text-slate-500">System Status: <span class="text-emerald-500 font-bold">Operational</span></p>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-sm font-bold text-white">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-blue-500 font-bold uppercase tracking-widest">
                        {{ auth()->user()->email === 'super.admin@test.com' ? 'Platform Master' : 'Platform Administrator' }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center text-white font-bold">
                    {{ auth()->user()->email === 'super.admin@test.com' ? 'PM' : strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            </div>
        </header>

        <!-- Dynamic Content -->
        <div class="flex-grow overflow-y-auto p-10 content-area">
            @if(session('success'))
                <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-6 py-4 rounded-2xl flex items-center">
                    <i class="bi bi-check-circle-fill mr-3"></i>
                    <span class="font-semibold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @stack('page_js')
</body>
</html>
