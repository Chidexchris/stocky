<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products() {
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }
}
