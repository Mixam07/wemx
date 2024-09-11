<?php 

namespace Modules\Affiliates\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Affiliates\Entities\Affiliate;
use Modules\Affiliates\Entities\AffiliateInvite;
use Illuminate\Support\Facades\Cookie;

class TrackAffiliate
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasCookie('affiliate')) {
            return $next($request);
        }

        $affiliate = Affiliate::where('code', $request->cookie('affiliate'))->first();
        $invite = AffiliateInvite::find($request->cookie('affiliate_invite'));
        if ($affiliate) {
            if(auth()->check() AND $invite) {
                $invite->hasRegistered();
            }
        }

        return $next($request);
    }
}