<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Device;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CheckRevokedDevices
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && $request->hasHeader('user-agent')) {

            // add the device
            Device::addDevice(request(), Auth::user());

            // check if device is revoked
            $device = Auth::user()->devices->where('user_agent', $request->header('user-agent'))->where('is_revoked', true)->first();
            if($device !== NULL) {
                return redirect()->route('client.reauthenticate', ['device' => $device->id]);            
            }
        }

        return $next($request);
    }
}
