<?php

namespace App\Models;

use App\Traits\ActionTakenBy;
use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    use ActionTakenBy;

    public function adjustmentDetails()
    {
        return $this->hasMany(AdjustmentDetails::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
