<?php

namespace Modules\Transfer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Entities\Product;

class TransferDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function transfer() {
        return $this->belongsTo(Transfer::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
