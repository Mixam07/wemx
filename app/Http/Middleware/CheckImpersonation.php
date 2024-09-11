<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckImpersonation
{
    public function handle(Request $request, Closure $next)
    {
        if (session('impersonate')) {
            auth()->onceUsingId(session('impersonate'));
        }

        return $next($request);
    }
}