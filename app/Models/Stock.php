<?php

namespace App\Models;

use App\Traits\ActionTakenBy;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use ActionTakenBy;
    protected $guarded = [];
    protected static function booted()
    {
        static::creating(function ($stock) {
            $stock->tracking_id = self::generateTrackingId();
        });
    }

    public static function generateTrackingId()
    {
        // Get the last integer tracking number (e.g., CTN-10002 -> 10002)
        $lastTracking = self::orderByDesc('id')->value('tracking_id');

        $lastNumber = 10000; // Default start

        if ($lastTracking && preg_match('/CTN-(\d+)/', $lastTracking, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return 'CTN-' . ($lastNumber + 1);
    }


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
        return $this->morphTo(__FUNCTION__, 'user_model', 'user_id');
    }
}
