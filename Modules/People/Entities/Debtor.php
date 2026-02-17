<?php

namespace Modules\People\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\ScopedByStore;

class Debtor extends Model
{
    use HasFactory, \App\Models\Concerns\BelongsToBusiness, ScopedByStore;

    protected $guarded = [];

    /** Accessor: return amount_owed in major units */
    public function getAmountOwedAttribute($value) {
        return $value / 100;
    }

    /** Mutator: store amount_owed in minor units */
    public function setAmountOwedAttribute($value) {
        $this->attributes['amount_owed'] = (int) round($value * 100);
    }

    /** Safely adjust balance by cents and delete when settled */
    public function adjustBalance(int $deltaCents): void {
        $new = max(0, ($this->getRawOriginal('amount_owed') ?? 0) + $deltaCents);
        if ($new === 0) {
            $this->delete();
            return;
        }
        $this->update(['amount_owed' => $new]);
    }
}
