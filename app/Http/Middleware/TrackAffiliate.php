<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Affiliates\Entities\AffiliateInvite;

class TrackAffiliate
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
        if(!$request->hasCookie('affiliate_invite')) {
            return $next($request);
        }
    
        if(!auth()->check()) {
            return $next($request);
        }

        if(AffiliateInvite::where('user_id', auth()->user()->id)->exists()) {
            return $next($request);
        }

        $invite = AffiliateInvite::find(request()->cookie('affiliate_invite'));
        if(!$invite) {
            return $next($request);
        }
        
        $invite->user_id = auth()->user()->id;
        $invite->created_an_account = true;
        $invite->status = 'Registered';
        $invite->save();

        return $next($request);
    }
}
