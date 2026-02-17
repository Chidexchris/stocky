<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'features' => 'json',
    ];

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }
}
