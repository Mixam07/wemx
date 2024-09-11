<?php 

namespace Modules\Tickets\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'Bearer token not provided'], 401);
        }

        if(!settings('encrypted::tickets::api_key', false)) {
            return response()->json(['message' => 'API token has not been generated'], 400);
        }

        if(settings('encrypted::tickets::api_key') !== $request->bearerToken()) {
            return response()->json(['message' => 'Bearer token is not valid'], 403);
        }

        return $next($request);
    }
}