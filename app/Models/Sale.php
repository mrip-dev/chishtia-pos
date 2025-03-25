<?php

namespace App\Models;

use App\Traits\ActionTakenBy;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{

    use ActionTakenBy;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetails::class);
    }

    public function saleReturn()
    {
        return $this->hasOne(SaleReturn::class);
    }
}
