@extends('layouts.app')

@section('title', 'Upgrade Required')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Upgrade Required</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-5">
        <div class="col-lg-6 col-md-8">
            <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                {{-- Gradient Header --}}
                <div class="text-center text-white py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="mb-3">
                        <i class="bi bi-lock-fill" style="font-size: 3rem; opacity: 0.9;"></i>
                    </div>
                    <h2 class="font-weight-bold mb-1">Upgrade Required</h2>
                    <p class="mb-0 opacity-75">This feature is not available on your current plan</p>
                </div>

                {{-- Body --}}
                <div class="card-body text-center px-5 py-4">
                    {{-- Feature Info --}}
                    <div class="mb-4">
                        <span class="badge badge-pill px-3 py-2 mb-3" style="background: #f0f0ff; color: #667eea; font-size: 0.85rem;">
                            <i class="bi bi-star-fill mr-1"></i> {{ $featureName ?? 'Premium Feature' }}
                        </span>
                        <p class="text-muted mb-0">
                            Your current <strong>{{ $currentPlan ?? 'Starter' }}</strong> plan does not include
                            <strong>{{ $featureName ?? 'this feature' }}</strong>.
                            Upgrade your plan to unlock this and many more powerful features.
                        </p>
                    </div>

                    {{-- Plan Comparison Hint --}}
                    <div class="rounded p-3 mb-4" style="background: #f8f9ff; border: 1px dashed #667eea;">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="mr-3">
                                <span class="text-muted" style="font-size: 0.85rem;">Current Plan</span>
                                <h5 class="mb-0 font-weight-bold">{{ $currentPlan ?? 'Starter' }}</h5>
                            </div>
                            <div class="mx-3">
                                <i class="bi bi-arrow-right" style="font-size: 1.5rem; color: #667eea;"></i>
                            </div>
                            <div class="ml-3">
                                <span class="text-muted" style="font-size: 0.85rem;">Recommended</span>
                                <h5 class="mb-0 font-weight-bold" style="color: #667eea;">{{ $recommendedPlan ?? 'Business' }}</h5>
                            </div>
                        </div>
                    </div>

                    {{-- CTA Buttons --}}
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ env('SALES_PAGE_URL', 'http://localhost:5173/pricing') }}" target="_blank" rel="noopener noreferrer" class="btn btn-lg px-5 text-white"
                           style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; font-weight: 600;">
                            <i class="bi bi-rocket-takeoff mr-2"></i> Upgrade Now
                        </a>
                    </div>

                    <div class="mt-3">
                        <a href="{{ url()->previous() }}" class="text-muted" style="text-decoration: none; font-size: 0.9rem;">
                            <i class="bi bi-arrow-left mr-1"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
