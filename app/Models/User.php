<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Concerns\ScopedByStore; // Added this import for ScopedByStore

class User extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia, \App\Models\Concerns\BelongsToBusiness, \App\Models\Concerns\ScopedByStore;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'business_id',
        'store_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ['media'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->useFallbackUrl('https://www.gravatar.com/avatar/' . md5("test@mail.com"));
    }

    public function scopeIsActive(Builder $builder) {
        return $builder->where('is_active', 1);
    }

    public function store() {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function business() {
        return $this->belongsTo(Business::class);
    }

    public function hasFeature($featureName) {
        if ($this->hasRole('Super Admin')) {
            return true;
        }

        if (!$this->business || (!$this->business->plan && !$this->business->feature_overrides)) {
            return false;
        }

        // Check Local Governance Overrides (Phase 6)
        if ($this->business->hasFeatureOverride($featureName)) {
            return true;
        }

        $features = $this->business->plan->features ?? [];

        // Handle Plan Inheritance / Collections
        if (in_array('Everything in Business', $features)) {
            $features = array_merge($features, [
                'Supplier Management',
                'Customer Debt Tracking',
                'Expiry Date Alerts',
                'Login Logs Tracking',
                'Barcode Printing',
                'Expense Management'
            ]);
        }
        
        // Map simplified names to actual feature strings from PlanSeeder
        $featureMap = [
            'suppliers'  => 'Supplier Management',
            'debtors'    => 'Customer Debt Tracking',
            'transfers'  => 'Inter-store Transfers',
            'login_logs' => 'Login Logs Tracking',
            'barcode_printing' => 'Barcode Printing',
            'expenses'   => 'Expense Management',
            'reports'    => 'Advanced Reports',
        ];

        $requiredFeature = $featureMap[$featureName] ?? $featureName;

        return in_array($requiredFeature, $features);
    }
}
