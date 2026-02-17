@extends('layouts.app')

@section('title', 'Home')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item active">Home</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        @can('show_total_stats')
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="card border-0">
                    <div class="card-body p-0 d-flex align-items-center shadow-sm">
                        <div class="bg-gradient-primary p-4 mfe-3 rounded-left">
                            <i class="bi bi-bar-chart font-2xl"></i>
                        </div>
                        <div>
                            <div class="text-value text-primary">{{ format_currency($revenue) }}</div>
                            <div class="text-muted text-uppercase font-weight-bold small">Revenue</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0">
                    <div class="card-body p-0 d-flex align-items-center shadow-sm">
                        <div class="bg-gradient-warning p-4 mfe-3 rounded-left">
                            <i class="bi bi-arrow-return-left font-2xl"></i>
                        </div>
                        <div>
                            <div class="text-value text-warning">{{ format_currency($sale_returns) }}</div>
                            <div class="text-muted text-uppercase font-weight-bold small">Sales Return</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0">
                    <div class="card-body p-0 d-flex align-items-center shadow-sm">
                        <div class="bg-gradient-success p-4 mfe-3 rounded-left">
                            <i class="bi bi-arrow-return-right font-2xl"></i>
                        </div>
                        <div>
                            <div class="text-value text-success">{{ format_currency($purchase_returns) }}</div>
                            <div class="text-muted text-uppercase font-weight-bold small">Purchases Return</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card border-0">
                    <div class="card-body p-0 d-flex align-items-center shadow-sm">
                        <div class="bg-gradient-info p-4 mfe-3 rounded-left">
                            <i class="bi bi-trophy font-2xl"></i>
                        </div>
                        <div>
                            <div class="text-value text-info">{{ format_currency($profit) }}</div>
                            <div class="text-muted text-uppercase font-weight-bold small">Profit</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @if($has_sales)
        @if(auth()->user()->can('show_weekly_sales_purchases') || auth()->user()->can('show_month_overview') || auth()->user()->hasRole('Business Owner'))
        <div class="row mb-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header">
                        Sales & Purchases of Last 7 Days
                    </div>
                    <div class="card-body">
                        <canvas id="salesPurchasesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header">
                        Overview of {{ now()->format('F, Y') }}
                    </div>
                    <div class="card-body d-flex justify-content-center">
                        <div class="chart-container" style="position: relative; height:auto; width:280px">
                            <canvas id="currentMonthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif

        @if($has_sales)
        @can('show_monthly_cashflow')
        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        Monthly Cash Flow (Payment Sent & Received)
                    </div>
                    <div class="card-body">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        @endif
    </div>
@endsection

@section('third_party_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.0/chart.min.js"
            integrity="sha512-asxKqQghC1oBShyhiBwA+YgotaSYKxGP1rcSYTDrB0U6DxwlJjU59B67U8+5/++uFjcuVM8Hh5cokLjZlhm3Vg=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection

@push('page_scripts')
    {{-- <script defer src="{{ mix('js/chart-config.js') }}"></script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // New Chart Implementation (Hard Request)
            let salesPurchasesBar = document.getElementById('salesPurchasesChart');
            if (salesPurchasesBar) {
                $.get('/sales-purchases/chart-data', function (response) {
                    // response is now { sales: {data:[], days:[]}, purchases: {data:[], days:[]} }
                    console.log("Chart Data Loaded:", response);
                    
                    let salesPurchasesChart = new Chart(salesPurchasesBar, {
                        type: 'bar',
                        data: {
                            labels: response.sales.days,
                            datasets: [{
                                label: 'Sales',
                                data: response.sales.data,
                                backgroundColor: ['#6366F1'],
                                borderColor: ['#6366F1'],
                                borderWidth: 1
                            },
                            {
                                label: 'Purchases',
                                data: response.purchases.data,
                                backgroundColor: ['#A5B4FC'],
                                borderColor: ['#A5B4FC'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error("Chart Request Failed:", textStatus, errorThrown);
                });
            }

            // Restore other charts if needed, using old logic or similar new logic
            // For now, focusing on the main requested chart: Sales & Purchases
             let overviewChart = document.getElementById('currentMonthChart');
             if (overviewChart) {
                $.get('/current-month/chart-data', function (response) {
                    new Chart(overviewChart, {
                        type: 'doughnut',
                        data: {
                            labels: ['Sales', 'Purchases', 'Expenses'],
                            datasets: [{
                                data: [response.sales, response.purchases, response.expenses],
                                backgroundColor: ['#F59E0B', '#0284C7', '#EF4444'],
                                hoverBackgroundColor: ['#F59E0B', '#0284C7', '#EF4444'],
                            }]
                        },
                    });
                });
             }

             let paymentChart = document.getElementById('paymentChart');
             if(paymentChart) {
                $.get('/payment-flow/chart-data', function (response) {
                    new Chart(paymentChart, {
                        type: 'line',
                        data: {
                            labels: response.months,
                            datasets: [
                                {
                                    label: 'Payment Sent',
                                    data: response.payment_sent,
                                    fill: false,
                                    borderColor: '#EA580C',
                                    tension: 0
                                },
                                {
                                    label: 'Payment Received',
                                    data: response.payment_received,
                                    fill: false,
                                    borderColor: '#2563EB',
                                    tension: 0
                                },
                            ]
                        },
                    });
                });
             }
        });
    </script>
@endpush
