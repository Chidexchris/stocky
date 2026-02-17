<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\Product;

class Business extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'daily_report_enabled' => 'boolean',
        'trial_ends_at' => 'datetime',
        'feature_overrides' => 'array',
        'is_under_maintenance' => 'boolean',
    ];

    public function hasFeatureOverride($feature)
    {
        $overrides = $this->feature_overrides ?? [];
        return in_array($feature, $overrides);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    /**
     * Calculate total storage used by this business (in bytes).
     * Includes all media associated with users and products.
     */
    public function storageUsed()
    {
        // Get all user IDs for this business
        $userIds = $this->users()->pluck('id');
        
        // Get all product IDs for this business
        $productIds = Product::where('business_id', $this->id)->pluck('id');

        // Sum media sizes for users (avatars)
        $userMediaSize = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('model_type', User::class)
            ->whereIn('model_id', $userIds)
            ->sum('size');

        // Sum media sizes for products (images)
        $productMediaSize = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('model_type', Product::class)
            ->whereIn('model_id', $productIds)
            ->sum('size');

        return $userMediaSize + $productMediaSize;
    }

    /**
     * Check if the business has reached its plan's storage limit.
     */
    public function storageLimitReached()
    {
        $plan = $this->plan;
        if (!$plan) {
            return false;
        }

        $limitBytes = $plan->limit_storage * 1024 * 1024 * 1024; // GB to Bytes
        return $this->storageUsed() >= $limitBytes;
    }
}
