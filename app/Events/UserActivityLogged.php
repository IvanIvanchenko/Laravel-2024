<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserActivityLogged
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $route;
    public $method;
    public $status;
    public $duration;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $route, $method, $status, $duration)
    {
        $this->userId = $userId;
        $this->route = $route;
        $this->method = $method;
        $this->status = $status;
        $this->duration = $duration;
    }
}

