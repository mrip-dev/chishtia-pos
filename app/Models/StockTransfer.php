<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'product_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'quantity',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

}
