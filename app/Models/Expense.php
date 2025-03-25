<?php

namespace App\Models;

use App\Traits\ActionTakenBy;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use ActionTakenBy;

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }
}
