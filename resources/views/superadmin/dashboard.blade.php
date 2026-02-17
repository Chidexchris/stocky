@extends('layouts.superadmin')

@section('title', 'Dashboard')
@section('page_title', 'Network Intelligence')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <!-- Revenue MRR -->
    <div class="premium-card p-6 flex items-start justify-between border-l-4 border-blue-500">
        <div>
            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Monthly Revenue (MRR)</p>
            <h3 class="text-3xl font-black text-white">{{ format_currency($stats['mrr']) }}</h3>
            <p class="text-[10px] text-blue-500 font-bold mt-2">
                <i class="bi bi-graph-up mr-1"></i> RECURRING STREAM
            </p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500">
            <i class="bi bi-cash-stack text-xl"></i>
        </div>
    </div>

    <!-- Revenue ARR -->
    <div class="premium-card p-6 flex items-start justify-between border-l-4 border-emerald-500">
        <div>
            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Annual Revenue (ARR)</p>
            <h3 class="text-3xl font-black text-white">{{ format_currency($stats['arr']) }}</h3>
            <p class="text-[10px] text-emerald-500 font-bold mt-2 uppercase">Forecasted Projection</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
            <i class="bi bi-currency-dollar text-xl"></i>
        </div>
    </div>

    <!-- Growth -->
    <div class="premium-card p-6 flex items-start justify-between border-l-4 border-purple-500">
        <div>
            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Business Growth</p>
            <h3 class="text-3xl font-black text-white">{{ $stats['growth'] }}%</h3>
            <p class="text-[10px] {{ $stats['growth'] >= 0 ? 'text-emerald-500' : 'text-red-500' }} font-bold mt-2 uppercase">
                {{ $stats['growth'] >= 0 ? 'Upward Trend' : 'Downward Trend' }} (Last 30d)
            </p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center text-purple-500">
            <i class="bi bi-rocket-takeoff text-xl"></i>
        </div>
    </div>

    <!-- Total Businesses -->
    <div class="premium-card p-6 flex items-start justify-between border-l-4 border-orange-500">
        <div>
            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-1">Total Tenants</p>
            <h3 class="text-3xl font-black text-white">{{ $stats['total_businesses'] }}</h3>
            <p class="text-[10px] text-orange-500 font-bold mt-2 uppercase">Across All Tiers</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-500">
            <i class="bi bi-building text-xl"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Multi-Tenant Signup Chart -->
    <div class="lg:col-span-2">
        <div class="premium-card p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h4 class="text-white font-bold tracking-tight">Business Acquisition</h4>
                    <p class="text-xs text-slate-500 mt-1">Tenant onboarding frequency - Last 7 Days</p>
                </div>
                <div class="flex space-x-2">
                    <span class="px-3 py-1 bg-blue-500/10 border border-blue-500/20 rounded-full text-[10px] font-bold text-blue-400 uppercase">Live Metrics</span>
                </div>
            </div>
            
            <div class="h-64">
                <canvas id="signupChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Stats & Health -->
    <div class="lg:col-span-1">
        <div class="premium-card p-8 h-full">
            <h4 class="text-white font-bold mb-6 tracking-tight">Ecosystem Health</h4>
            
            <div class="space-y-6">
                <div class="flex items-center justify-between p-4 bg-slate-800/50 rounded-2xl border border-white/5">
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 mr-3 animate-pulse"></div>
                        <span class="text-sm font-semibold text-slate-300">API Gateway</span>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-500 uppercase">Stable</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-800/50 rounded-2xl border border-white/5">
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 mr-3 animate-pulse"></div>
                        <span class="text-sm font-semibold text-slate-300">Database Cluster</span>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-500 uppercase">Optimized</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-800/50 rounded-2xl border border-white/5">
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full bg-blue-500 mr-3"></div>
                        <span class="text-sm font-semibold text-slate-300">Storage Nodes</span>
                    </div>
                    <span class="text-[10px] font-bold text-blue-500 uppercase">92% Free</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-800/50 rounded-2xl border border-white/5">
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full bg-purple-500 mr-3"></div>
                        <span class="text-sm font-semibold text-slate-300">Worker Instances</span>
                    </div>
                    <span class="text-[10px] font-bold text-purple-500 uppercase">12 Active</span>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-white/5">
                <div class="flex items-center justify-between text-xs font-bold mb-4">
                    <span class="text-slate-500 uppercase tracking-widest">Active Users Online</span>
                    <span class="text-white text-lg font-black">{{ number_format($stats['total_users'] * 0.15) }}</span>
                </div>
                <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 w-[15%]"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page_js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('signupChart').getContext('2d');
    const signupChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($stats['signup_chart']['labels']) !!},
            datasets: [{
                label: 'New Businesses',
                data: {!! json_encode($stats['signup_chart']['data']) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 10,
                            weight: '600'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)'
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 10,
                            weight: '600'
                        },
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
