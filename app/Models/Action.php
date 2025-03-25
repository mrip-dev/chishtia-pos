<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    const UPDATED_AT = null;

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
