<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory, \App\Models\Concerns\BelongsToBusiness;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Modules\Setting\Database\factories\UnitFactory::new();
    }
}
