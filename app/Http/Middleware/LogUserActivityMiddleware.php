<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Events\UserActivityLogged;
use Illuminate\Support\Facades\Event;

class LogUserActivityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {

        $start = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $start) * 1000; // duration in milliseconds
        $userId = Auth::check() ? Auth::id() : null;
        $route = $request->route()->getName();
        $method = $request->method();
        $status = $response->getStatusCode();

        if ($userId) {
            event(new UserActivityLogged($userId, $route, $method, $status, $duration));
        }

        return $response;
    }
}

