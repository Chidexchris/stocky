@extends('layouts.app')

@section('title', 'Logistics Tiers')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="text-center mb-5">
                <h1 class="display-4 font-weight-bold">Select Your Logistics Tier</h1>
                <p class="text-muted lead">Choose the best plan to manage your warehouse and inventory nodes.</p>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-4">
                @foreach(\App\Models\Plan::all() as $plan)
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0" style="border-radius: 20px;">
                            <div class="card-body p-4 text-center">
                                <h3 class="card-title font-weight-bold mb-3">{{ $plan->name }}</h3>
                                <div class="mb-4">
                                    <span class="display-4 font-weight-bold">${{ number_format($plan->price, 0) }}</span>
                                    <span class="text-muted">/per node</span>
                                </div>
                                <ul class="list-unstyled mb-5 text-left mx-auto" style="max-width: 250px;">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success mr-2"></i> {{ $plan->limit_stores }} Operational Hubs</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success mr-2"></i> {{ $plan->limit_users }} Identity Personnel</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success mr-2"></i> {{ $plan->limit_storage }}GB Data Footprint</li>
                                    @php $features = explode("\n", $plan->features); @endphp
                                    @foreach($features as $feature)
                                        @if(trim($feature))
                                            <li class="mb-2 text-muted small"><i class="bi bi-check mr-2"></i> {{ trim($feature) }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                                <form action="{{ route('saas.pricing.select', $plan) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary btn-block btn-lg" style="border-radius: 12px; font-weight: 600;">
                                        Initialize {{ $plan->name }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
