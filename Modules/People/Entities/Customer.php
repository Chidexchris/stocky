<?php

namespace Modules\People\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\ScopedByStore;

class Customer extends Model
{
    use HasFactory, ScopedByStore, \App\Models\Concerns\BelongsToBusiness;

    protected $guarded = [];

    protected static function newFactory() {
        return \Modules\People\Database\factories\CustomerFactory::new();
    }

}
