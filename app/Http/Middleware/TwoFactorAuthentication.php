<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class TwoFactorAuthentication
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
        if(auth()->check()) {
            $user = auth()->user();

            // check if user has 2fa enabled
            if($user->TwoFa()->exists()) {
                if ($user->TwoFa->session_expires_at->lessThan(Carbon::now())) {
                    return redirect()->route('2fa.validate');
                } 
            }

        }
    
        return $next($request);
    }
}
