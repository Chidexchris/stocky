<?php

namespace Modules\Transfer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Store;

class Transfer extends Model
{
    use HasFactory, \App\Models\Concerns\BelongsToBusiness;

    protected $guarded = [];

    public function fromStore() {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore() {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function details() {
        return $this->hasMany(TransferDetail::class);
    }

    public static function boot() {
        parent::boot();

        static::addGlobalScope('business_scope', function ($builder) {
             if (auth()->check()) {
                 $user = auth()->user();
                 if ($user->hasRole('Super Admin')) {
                     return;
                 }

                 if ($user->store_id) {
                     // If user is assigned to a specific store, only show transfers involving that store
                     $builder->where(function($q) use ($user) {
                         $q->where('from_store_id', $user->store_id)
                           ->orWhere('to_store_id', $user->store_id);
                     });
                 } elseif ($user->business_id) {
                     // If user is a Business Admin (no specific store), show all transfers for their business
                     $builder->whereHas('fromStore', function($q) use ($user) {
                         $q->where('business_id', $user->business_id);
                     });
                 }
             }
        });

        static::creating(function ($model) {
            $number = Transfer::max('id') + 1;
            $model->reference = make_reference_id('TRF', $number);
        });
    }
}
