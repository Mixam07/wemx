<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Punishment;

class Punishments
{
    public function handle(Request $request, Closure $next)
    {
        if(Punishment::hasActiveBans()) {
            return redirect()->route('suspended');
        }
        
        return $next($request);
    }
}