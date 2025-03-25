<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturnDetails extends Model
{
    public $timestamps = false;

    protected $casts = [
        'date' => 'date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
