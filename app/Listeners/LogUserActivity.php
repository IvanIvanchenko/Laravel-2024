<?php

namespace App\Listeners;

use App\Events\UserActivityLogged;
use Illuminate\Support\Facades\DB;

class LogUserActivity
{
    /**
     * Handle the event.
     */
    public function handle(UserActivityLogged $event)
    {

        DB::table('user_activities')->insert([
            'user_id' => $event->userId,
            'route' => $event->route,
            'method' => $event->method,
            'status' => $event->status,
            'duration' => round($event->duration),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

