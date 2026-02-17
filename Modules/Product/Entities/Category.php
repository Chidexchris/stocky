<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, \App\Models\Concerns\BelongsToBusiness, \App\Models\Concerns\ScopedByStore;

    protected $guarded = [];

    public function products() {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function store() {
        return $this->belongsTo(\App\Models\Store::class, 'store_id', 'id');
    }
}
