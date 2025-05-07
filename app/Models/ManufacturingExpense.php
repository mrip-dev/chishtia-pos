<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingExpense extends Model
{
    protected $guarded = [];

    public function flow()
    {
        return $this->belongsTo(ManufacturingFlow::class);
    }

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }
}
