<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait BelongsToBusiness
{
    public static function bootBelongsToBusiness()
    {
        static::addGlobalScope('business', function (Builder $builder) {
            // Avoid recursion: Only apply scope if user is ALREADY loaded.
            if (Auth::guard()->hasUser()) {
                $user = Auth::user();
                
                // Super Admin can see all businesses
                if ($user->hasRole('Super Admin')) {
                    return;
                }

                if ($user->business_id) {
                    $builder->where(function ($query) use ($user) {
                        $query->where($query->getModel()->getTable() . '.business_id', $user->business_id)
                            ->orWhereNull($query->getModel()->getTable() . '.business_id');
                    });
                }
            }
        });

        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if (!$user->hasRole('Super Admin') && $user->business_id) {
                    $model->business_id = $user->business_id;
                }
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($model->isDirty('business_id') && !$user->hasRole('Super Admin')) {
                    $model->business_id = $model->getOriginal('business_id');
                }
            }
        });
    }

    /**
     * Scope a query to only include models of a specific business.
     */
    public function scopeForBusiness(Builder $query, $businessId): Builder
    {
        return $query->where('business_id', $businessId);
    }
}
