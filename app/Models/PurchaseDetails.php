<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetails extends Model
{

    public $timestamps = false;

    protected $casts = [
        'date' => 'date',
    ];
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
