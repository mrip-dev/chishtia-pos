<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Action extends Model
{
    const UPDATED_AT = null;
    protected static function booted()
    {
        parent::booted();

        // Define the morph map here
        Relation::morphMap([
            \App\Models\Supplier::class => \App\Models\Supplier::class,
            \App\Models\Customer::class => \App\Models\Customer::class,
        ]);
    }
    // In App\Models\ModelName.php
    public function scopeFilter($query, $filters)
    {
        return $query->when(
                ($filters['start_date'] ?? null) && ($filters['end_date'] ?? null),
                fn($q) => $q->whereBetween('created_at', [$filters['start_date'], $filters['end_date']])
            );
    }

    public function actionable()
    {
        return $this->morphTo();
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public static function newEntry($parent, $type)
    {
        $action = new Action();
        $action->action_name = $type;
        $action->admin_id = auth()->guard('admin')->id();
        $parent->actions()->save($action);
    }
}
