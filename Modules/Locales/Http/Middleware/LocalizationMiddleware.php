<?php

namespace Modules\Locales\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LocalizationMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            $lang = Session::get('locale');
        } else {
            $lang = Auth::check() ? Auth::user()->language : $request->getPreferredLanguage();
        }

        App::setLocale($lang);

        return $next($request);
    }
}
