<?php

namespace App\Models;

use App\Traits\ActionTakenBy;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{

    use ActionTakenBy;
    protected $guarded = [];


    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }
    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
    public function fromUser()
    {
        return $this->morphTo(__FUNCTION__, 'from_user_model', 'from_user_id');
    }

    public function toUser()
    {
        return $this->morphTo(__FUNCTION__, 'to_user_model', 'to_user_id');
    }

    public function stockTransferDetails()
    {
        return $this->hasMany(StockTransferDetail::class , 'stock_transfer_id');
    }
}
