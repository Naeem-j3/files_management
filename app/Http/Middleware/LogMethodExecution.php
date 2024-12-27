<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogMethodExecution
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            // **Before**: Logic to execute before the controller method
            Log::info('Before method execution: ' . $request->route()->getActionName());

            // Proceed to the next middleware/controller
            $response = $next($request);

            // **After**: Logic to execute after the controller method
            Log::info('After method execution: ' . $request->route()->getActionName());

            return $response;
        } catch (\Exception $e) {
            // **On Exception**: Logic to execute when an exception occurs
            Log::error('Exception in method: ' . $request->route()->getActionName());
            Log::error('Error details: ' . $e->getMessage());

            // Re-throw the exception so it can be handled by Laravel's exception handler
            throw $e;
        }
    }

}
