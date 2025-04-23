<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInOut extends Model
{
    protected $guarded = [];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

}
