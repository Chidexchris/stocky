<?php

namespace Modules\SalesReturn\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\ScopedByStore;

class SaleReturn extends Model
{
    use HasFactory, ScopedByStore, \App\Models\Concerns\BelongsToBusiness;

    protected $guarded = [];

    public function saleReturnDetails() {
        return $this->hasMany(SaleReturnDetail::class, 'sale_return_id', 'id');
    }

    public function saleReturnPayments() {
        return $this->hasMany(SaleReturnPayment::class, 'sale_return_id', 'id');
    }

    public function customer() {
        return $this->belongsTo(\Modules\People\Entities\Customer::class, 'customer_id', 'id');
    }

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $number = SaleReturn::max('id') + 1;
            $model->reference = make_reference_id('SLRN', $number);;
            $model->order_id = 'ORD-' . $model->reference;
        });
    }

    public function scopeCompleted($query) {
        return $query->where('status', 'Completed');
    }

    public function getShippingAmountAttribute($value) {
        return $value / 100;
    }

    public function getPaidAmountAttribute($value) {
        return $value / 100;
    }

    public function getTotalAmountAttribute($value) {
        return $value / 100;
    }

    public function getDueAmountAttribute($value) {
        return $value / 100;
    }

    public function getTaxAmountAttribute($value) {
        return $value / 100;
    }

    public function getDiscountAmountAttribute($value) {
        return $value / 100;
    }
}
