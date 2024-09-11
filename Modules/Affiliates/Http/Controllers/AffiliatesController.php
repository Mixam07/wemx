<?php

namespace Modules\Affiliates\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Modules\Affiliates\Entities\Affiliate;
use Modules\Affiliates\Entities\AffiliateInvite;
use Modules\Affiliates\Entities\AffiliatePayout;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Facades\Theme;
use App\Models\Payment;
use Carbon\Carbon;

class AffiliatesController extends Controller
{
    /**
     * Affiliate management screen for affiliates
     * @return Renderable
     */
    public function manage()
    {
        $affiliate = Affiliate::getOrCreate();

        $days = collect(range(0, session('affiliates_show_days', 6)))->map(function($day) {
            return Carbon::now()->subDays($day)->format('d F');
        })->reverse()->values()->toArray();

        $invites = collect(range(0, session('affiliates_show_days', 6)))->map(function($day) use ($affiliate) {
            $date = Carbon::now()->subDays($day)->startOfDay();
            return $affiliate->invites()->where('created_at', '>=', $date)
                                        ->where('created_at', '<', $date->copy()->endOfDay())
                                        ->count();
        })->reverse()->values()->toArray();

        $registrations = collect(range(0, session('affiliates_show_days', 6)))->map(function($day) use ($affiliate) {
            $date = Carbon::now()->subDays($day)->startOfDay();
            return $affiliate->invites()->where('created_an_account', true)
                    	                ->where('created_at', '>=', $date)
                                        ->where('created_at', '<', $date->copy()->endOfDay())
                                        ->count();
        })->reverse()->values()->toArray();

        $purchases = collect(range(0, session('affiliates_show_days', 6)))->map(function($day) use ($affiliate) {
            $date = Carbon::now()->subDays($day)->startOfDay();
            return $affiliate->invites()->where('placed_an_order', true)
                    	                ->where('created_at', '>=', $date)
                                        ->where('created_at', '<', $date->copy()->endOfDay())
                                        ->count();
        })->reverse()->values()->toArray();

        return view(Theme::moduleView('affiliates','affiliate'), compact('affiliate', 'invites', 'days', 'registrations', 'purchases'));
    }

    public function showLastDays($days)
    {
        session(['affiliates_show_days' => $days - 1]);
        return redirect()->back();
    }


    /**
     * Create a payout request
     * @return Renderable
     */
    public function payout(Request $request)
    {
        $validated = $request->validate([
            'gateway' => 'required',
            'paypal_email' => ($request->input('gateway') == 'paypal') ? 'required' : 'nullable'. '|email',
            'btc_address' => ($request->input('gateway') == 'bitcoin') ? 'required' : 'nullable',
        ]);

        $affiliate = Affiliate::getOrCreate();

        if($affiliate->balance < 10) {
            return redirect()->back()->withError('You need a minimum of '. currency('symbol') . number_format(settings('affiliates::minimum_payout', 10),2).' to request a payout.');
        }

        $payout = new AffiliatePayout;
        $payout->user_id = auth()->user()->id;
        $payout->affiliate_id = $affiliate->id;
        $payout->amount = $affiliate->balance;
        $payout->gateway = $request->input('gateway');
        $payout->status = 'pending';
        $payout->address = ($request->input('gateway') == 'paypal') ? $request->input('paypal_email') : $request->input('btc_address', NULL) ;
        $payout->save();

        $affiliate->balance = 0;
        $affiliate->save();

        if($request->input('gateway') == 'balance') {
            auth()->user()->balance("Affiliate Payout #$payout->id", '+', $payout->amount);
            $payout->status = 'completed';
            $payout->save();
        }
        app()->setLocale( auth()->user()->language);
        auth()->user()->email([
            'subject' => 'Affiliate Payout Request',
            'content' => "
                We've received your payout request <br><br>

                Payout ID: #$payout->id <br>
                Amount: $payout->amount <br>
                Gateway: $payout->gateway <br>
                Address: $payout->address <br>
                Date: {$payout->created_at->format('d M Y')} <br><br>

                Login to your affiliate account on our website for more information. Affiliate >> Cashout >> My Payouts

            ",
            'button' => [
                'name' => 'My Account',
                'url' => route('affiliates.manage'),
            ],
        ]);

        return redirect()->back()->withSuccess('Payout created successfully');
    }

    /**
     * This function is called when a link is opened that contains a affiliate code
     * @return Renderable
     */
    public function affiliate($affiliateCode)
    {
        $affiliate = Affiliate::where('code', $affiliateCode)->first();

        if ($affiliate) {
            $affiliate->increment('clicks');

            if(auth()->check() AND $affiliate->user_id == auth()->user()->id) {
                return redirect()->route('dashboard')->withError('You cannot use your own affiliate code');
            }

            // add logic to check affiliate invites if a user previously used a different code
            if(auth()->check() AND AffiliateInvite::where('user_id', auth()->user()->id)->exists()) {
                return redirect()->route('dashboard')->withError('You have already used an affiliate code');
            }

            $invite = $affiliate->createInvite();
            return redirect('/')->withCookie('affiliate', $affiliate->code)->withCookie('affiliate_invite', $invite->id);
        }

        return redirect('/');
    }
}
