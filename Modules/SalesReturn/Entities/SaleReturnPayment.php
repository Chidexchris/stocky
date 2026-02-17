<?php

namespace Modules\SalesReturn\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use App\Models\Concerns\ScopedByStore;

class SaleReturnPayment extends Model
{
    use HasFactory, ScopedByStore, \App\Models\Concerns\BelongsToBusiness;

    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->order_id = 'ORD-' . $model->reference;
        });
    }

    public function saleReturn() {
        return $this->belongsTo(SaleReturn::class, 'sale_return_id', 'id');
    }

    public function setAmountAttribute($value) {
        $this->attributes['amount'] = $value * 100;
    }

    public function getAmountAttribute($value) {
        return $value / 100;
    }

    public function getDateAttribute($value) {
        return Carbon::parse($value)->format('d M, Y');
    }

    public function scopeBySaleReturn($query) {
        return $query->where('sale_return_id', request()->route('sale_return_id'));
    }
}
