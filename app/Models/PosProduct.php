<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosProduct extends Model
{
    protected $guarded = [];
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
}
