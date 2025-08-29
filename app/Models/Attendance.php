<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{


    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'check_in' => 'datetime', // Converts check_in to a Carbon object
        'check_out' => 'datetime', // Converts check_out to a Carbon object
        'date' => 'date',         // Converts date to a Carbon object (date only)
    ];
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
     public function employee()
    {
        return $this->belongsTo(Admin::class);
    }
}
