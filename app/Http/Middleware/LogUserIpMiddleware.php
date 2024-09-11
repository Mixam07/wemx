<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserIp;

class LogUserIpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (auth()->check()) {
            $user = auth()->user();
            $ipAddress = $request->getClientIp();
    
            // Check if the IP address has already been logged for the user
            $userIp = UserIp::where('user_id', $user->id)
                            ->where('ip_address', $ipAddress)
                            ->first();
    
            // If the IP address is not logged, create a new record
            if (!$userIp) {
                UserIp::create([
                    'user_id' => $user->id,
                    'ip_address' => $ipAddress,
                ]);
            } else {
                $userIp->increment('uses');
            }
        }
    
        return $next($request);
    }
}
