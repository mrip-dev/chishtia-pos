<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function transactable()
    {
        return $this->morphTo();
    }
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
