<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WareHouseDetailHistory extends Model
{
    protected $guarded = [];

    public function wareHouse()
    {
        return $this->belongsTo(WareHouse::class, 'ware_house_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

}
