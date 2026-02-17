<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopedByStore
{
    public static function bootScopedByStore()
    {
        static::addGlobalScope('store', function (Builder $builder) {
            if (Auth::guard()->hasUser()) {
                $user = Auth::user();
                if ($user->hasRole('Super Admin') || $user->hasRole('Business Owner') || $user->hasRole('Admin')) {
                    return;
                }
                if (static::hasColumn($builder->getModel()->getTable(), 'store_id')) {
                    $builder->where($builder->getModel()->getTable() . '.store_id', $user->store_id);
                }
            }
        });

        static::creating(function ($model) {
            if (Auth::guard()->hasUser()) {
                $user = Auth::user();
                if (static::hasColumn($model->getTable(), 'store_id')) {
                    if (!$user->hasRole('Super Admin') && !$user->hasRole('Business Owner') && !$user->hasRole('Admin')) {
                        $model->store_id = $user->store_id;
                    }
                }
                if (static::hasColumn($model->getTable(), 'user_id')) {
                    $model->user_id = $user->id;
                }
            }
        });

        static::updating(function ($model) {
            if (Auth::guard()->hasUser()) {
                $user = Auth::user();
                if (static::hasColumn($model->getTable(), 'store_id') && $model->isDirty('store_id') && !$user->hasRole('Super Admin')) {
                    if (!$user->hasRole('Admin') && !$user->hasRole('Business Owner')) {
                        $model->store_id = $model->getOriginal('store_id');
                    }
                }
            }
        });
    }

    protected static function hasColumn(string $table, string $column): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
