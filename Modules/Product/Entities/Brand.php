<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\ScopedByStore;

class Brand extends Model
{
    use HasFactory, \App\Models\Concerns\BelongsToBusiness, ScopedByStore;

    protected $guarded = [];

    public function products() {
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }
}
