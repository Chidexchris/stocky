@php
    $user = auth()->user();
    $business = $user ? $user->business : null;
    $onTrial = $business && !$business->plan_id && $business->trial_ends_at;
    $daysLeft = $onTrial ? now()->diffInDays($business->trial_ends_at, false) : 0;
    $isExpired = $onTrial && $daysLeft < 0;
@endphp

@if($onTrial)
    <div class="d-flex align-items-center justify-content-center flex-grow-1 mx-2">
        <div class="d-flex align-items-center py-1 px-3 border shadow-sm" 
             style="background: {{ $daysLeft <= 1 ? 'linear-gradient(90deg, #fff5f5 0%, #fed7d7 100%)' : 'linear-gradient(90deg, #f0f7ff 0%, #e1effe 100%)' }}; 
                    gap: 15px; 
                    border-radius: 50px;
                    border-color: {{ $daysLeft <= 1 ? '#feb2b2' : '#bee3f8' }} !important;
                    max-width: fit-content;">
            
            <div class="d-flex align-items-center">
                <i class="bi {{ $daysLeft <= 1 ? 'bi-exclamation-triangle-fill text-danger' : 'bi-info-circle-fill text-primary' }} mr-2" style="font-size: 1rem;"></i>
                <span class="font-weight-bold" style="color: {{ $daysLeft <= 1 ? '#c53030' : '#2b6cb0' }}; font-size: 0.8rem; white-space: nowrap;">
                    @if($isExpired)
                        Trial Expired!
                    @elseif($daysLeft == 0)
                        Trial Ends Today
                    @else
                        {{ ceil($daysLeft) }} Days Trial
                    @endif
                </span>
            </div>

            <a href="{{ env('SALES_PAGE_URL', 'http://localhost:5173/pricing') }}" target="_blank" rel="noopener noreferrer" class="btn {{ $daysLeft <= 1 ? 'btn-danger' : 'btn-primary' }} btn-sm font-weight-bold px-3 py-0 hover-lift" 
               style="border-radius: 50px; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.7rem; transition: all 0.2s ease; height: 24px; line-height: 24px;">
                Upgrade
            </a>
        </div>
    </div>

    <style>
        .hover-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
        }
    </style>
@endif
