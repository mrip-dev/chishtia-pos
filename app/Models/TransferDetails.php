<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferDetails extends Model
{
    public function transfer()
    {
        return $this->belongsTo(Sale::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
