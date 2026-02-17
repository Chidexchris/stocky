<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'name',
        'email',
        'category',
        'message',
        'status',
        'priority',
    ];

    /**
     * Scope a query to only include open tickets.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
