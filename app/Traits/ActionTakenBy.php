<?php

namespace App\Traits;

use App\Models\Action;

trait ActionTakenBy
{

    public function actions()
    {
        return $this->morphMany(Action::class, 'actionable');
    }

    public function createdBy()
    {
        return $this->morphOne(Action::class, 'actionable')->where('action_name', 'CREATED');
    }
}
