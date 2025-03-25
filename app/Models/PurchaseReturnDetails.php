<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnDetails extends Model
{
    public $timestamps = false;

    protected $casts = [
        'date' => 'date',
    ];
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
