<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use GlobalStatus;

    public function productStock()
    {
        return $this->hasMany(ProductStock::class);
    }
    public function stock()
    {
        return $this->productStock()->sum('quantity');
    }

}