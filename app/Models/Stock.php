<?php

namespace App\Models;

use App\Traits\ActionTakenBy;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use ActionTakenBy;
    protected $guarded = [];


    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function stockInOuts()
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
    public function user()
    {
        if ($this->user_model === 'Supplier') {
            return $this->userSupplier();
        } elseif ($this->user_model === 'Customer') {
            return $this->userCustomer();
        }

        return null;
    }
    public function userSupplier()
    {
        return $this->belongsTo(Supplier::class, 'user_id');
    }
    public function userCustomer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }



}
