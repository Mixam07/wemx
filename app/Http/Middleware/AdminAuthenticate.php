<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!♙('VVRKR2FtRkhWVDA9')::has(♙('WkZkS1ZHTklVbkZXYmxKV1lVaGthVko2UVRWaFZGSnJUa1JOUFE9PQ=='))) {
            try {
                $UGTgZcHJiSRvArrTpFu3 = ♙('VTBoU01HTkJQVDA9')::get(♙('WVVoU01HTklUVFpNZVRsb1kwZHJkV1F5Vm5SbFF6VjNZMjA0ZGxsWVFuQk1NMlJzWWxobmRtSkhiR3BhVnpWNldsaE5kZz09') . ♙('V1RJNWRWcHRiRzQ9')(♙('V1ZoQ2QweHRlSEJaTWxaMVl6SlZQUT09'), 'null') . '/check');

                if (!$UGTgZcHJiSRvArrTpFu3[♙('WXpOV2Fsa3lWbnBqZHowOQ==')]) {
                    return ♙('V1ZkS2RtTnVVVDA9')((int) ♙('VGtSQmVnPT0='), ♙("VTFjMU1sbFhlSEJhUTBKTllWZE9iR0p1VG13PQ=="));
                }

            } catch(\Exception $error) {
                return ♙('V1ZkS2RtTnVVVDA9')((int) ♙('VGtSQmVnPT0='), ♙("VTFjMU1sbFhlSEJhUTBKTllWZE9iR0p1VG13PQ=="));
            }

            ♙('VVRKR2FtRkhWVDA9')::remember(♙('WkZkS1ZHTklVbkZXYmxKV1lVaGthVko2UVRWaFZGSnJUa1JOUFE9PQ=='), (int) ♙('VFdwRk1rMUVRVDA9'), fn() => true);
        }

        if(session('reauthenticated') == NULL OR !session('reauthenticated')) {
            return redirect()->route('reauthenticate', ['redirect' => $request->getPathInfo()])->with('title', 'Please reauthenticate');            
        }

        return $next($request);
    }
}