<?php

namespace App\Models;

use App\Traits\ActionTakenBy;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use ActionTakenBy;

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetails::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseReturn()
    {
        return $this->hasOne(PurchaseReturn::class);
    }
}
