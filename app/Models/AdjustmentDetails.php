<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjustmentDetails extends Model
{

    public function adjustment()
    {
        return $this->belongsTo(Adjustment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
