<?php

namespace App\Models;

use App\Traits\ActionTakenBy;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use ActionTakenBy;
    protected $guarded = [];
    protected $casts = [
        'stock_in' => 'integer',
        'stock_out' => 'integer',
        'stock_balance' => 'integer',
        'stock_alert' => 'integer',
        'cost_price' => 'float',
        'sale_price' => 'float',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function stockInOut()
    {
        return $this->hasMany(StockInOut::class);
    }
    public function stockIn()
    {
        return $this->hasMany(StockInOut::class)->where('stock_in_out_type', 'in');
    }
    public function stockOut()
    {
        return $this->hasMany(StockInOut::class)->where('stock_in_out_type', 'out');
    }
}
