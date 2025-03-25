<?php
namespace App\Traits;


trait UserNotify
{
    public static function notifyToUser(){
        return [
            'allUsers'      => 'All Customers',
            'selectedUsers' => 'Selected Customer'
        ];
    }

    public function scopeSelectedUsers($query)
    {
        return $query->whereIn('id', request()->user ?? []);
    }

    public function scopeAllUsers($query)
    {
        return $query;
    }


}
