@extends('layouts.app')

@section('title', 'Trial Expired')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center" style="margin-top: 100px;">
        <div class="col-md-6">
            <div class="card shadow-lg border-0" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-5 text-center bg-white">
                    <div class="mb-4">
                        <div class="display-1 text-danger">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                    
                    <h2 class="font-weight-bold text-dark mb-3">Trial Period Concluded</h2>
                    <p class="text-secondary mb-5 px-4" style="font-size: 1.1rem; line-height: 1.6;">
                        Your 7-day automatic free trial has ended. To continue using the platform and access your data, please select a logistics tier that fits your warehouse needs.
                    </p>

                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <a href="{{ env('SALES_PAGE_URL', 'http://localhost:5173/pricing') }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-lg px-4 gap-3" style="border-radius: 12px; font-weight: 600;">
                            View Pricing Plans
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-lg px-4" style="border-radius: 12px;">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 text-center">
                    <span class="text-muted small">Need more time? Contact our support team for a trial extension.</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
