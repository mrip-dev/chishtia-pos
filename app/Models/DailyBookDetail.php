<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyBookDetail extends Model
{
    protected $guarded = [];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    public function sale(){
        
    }
}
