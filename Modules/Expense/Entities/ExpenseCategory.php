<?php

namespace Modules\Expense\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseCategory extends Model
{
    use HasFactory, \App\Models\Concerns\BelongsToBusiness, \App\Models\Concerns\ScopedByStore;

    protected $guarded = [];

    public function expenses() {
        return $this->hasMany(Expense::class, 'category_id', 'id');
    }

    public function store() {
        return $this->belongsTo(\App\Models\Store::class, 'store_id', 'id');
    }
}
