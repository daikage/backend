<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log specific critical actions to avoid log bloat
        $action = $request->route() ? $request->route()->getActionMethod() : null;
        
        $loggableActions = [
            'requestRide', 
            'updateStatus', 
            'login', 
            'register', 
            'cancelRide',
            'updateLocation'
        ];

        if ($action && in_array($action, $loggableActions)) {
            Log::info("Activity Event: {$action}", [
                'user_id' => $request->user()?->id ?? 'guest',
                'ip' => $request->ip(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'status' => $response->getStatusCode(),
            ]);
        }

        return $response;
    }
}
