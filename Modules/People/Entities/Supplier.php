<?php

namespace Modules\People\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\ScopedByStore;

class Supplier extends Model
{
    use HasFactory, ScopedByStore;

    protected $guarded = [];

    protected static function newFactory() {
        return \Modules\People\Database\factories\SupplierFactory::new();
    }
}
