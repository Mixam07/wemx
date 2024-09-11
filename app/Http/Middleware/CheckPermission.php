<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $routeName = '')
    {
        if (empty($routeName)) {
            $routeName = Route::currentRouteName();
        }

        if (Auth::check() AND Auth::user()->hasPerm($routeName)) {
            return $next($request);
        }

        abort(403, __('responses.no_permission'));
    }
}
