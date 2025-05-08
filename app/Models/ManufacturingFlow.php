<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingFlow extends Model
{

    protected $guarded = [];
    protected static function booted()
    {
        static::creating(function ($stock) {
            $stock->tracking_id = self::generateTrackingId();
        });
    }

    public static function generateTrackingId()
    {

        $lastTracking = self::orderByDesc('id')->value('tracking_id');
        $lastNumber = 1000;
        if ($lastTracking && preg_match('/FLOW-(\d+)/', $lastTracking, $matches)) {
            $lastNumber = (int) $matches[1];
        }
        return 'FLOW-' . ($lastNumber + 1);
    }
    public function stocks()
    {
        return $this->hasMany(ManufacturingStock::class);
    }
    public function refinedItems()
    {
        return $this->hasMany(ManufacturingRefinedItems::class);
    }
    public function expenses()
    {
        return $this->hasMany(ManufacturingExpense::class);
    }


}
