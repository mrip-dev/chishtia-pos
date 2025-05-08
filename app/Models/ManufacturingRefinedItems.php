<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingRefinedItems extends Model
{
    protected $guarded = [];

    public function flow()
    {
        return $this->belongsTo(ManufacturingFlow::class);
    }


}
