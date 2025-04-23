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
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
