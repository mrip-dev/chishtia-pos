<?php

namespace App\Models;

use App\Traits\ActionTakenBy;
use Illuminate\Database\Eloquent\Model;

class ServiceStockDetail extends Model
{
    use ActionTakenBy;
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->morphTo(__FUNCTION__, 'user_model', 'user_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }



}
