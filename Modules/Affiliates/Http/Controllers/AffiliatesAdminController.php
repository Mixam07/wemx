<?php

namespace Modules\Affiliates\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Modules\Affiliates\Entities\Affiliate;
use Modules\Affiliates\Entities\AffiliatePayout;
use Illuminate\Routing\Controller;
use App\Facades\AdminTheme as Theme;

class AffiliatesAdminController extends Controller
{
    /**
     * Affiliate management screen for affiliates
     * @return Renderable
     */
    public function settings()
    {

        return view(Theme::moduleView('affiliates','settings'));
    }

    /**
     * Affiliate management screen for affiliates
     * @return Renderable
     */
    public function affiliates()
    {
        $affiliates = Affiliate::latest()->paginate(10);

        return view(Theme::moduleView('affiliates','affiliates'), compact('affiliates'));
    }

    /**
     * Update affiliate
     * @return Renderable
     */
    public function edit(Affiliate $affiliate)
    {
        return view(Theme::moduleView('affiliates','update'), compact('affiliate'));
    }

    /**
     * Update affiliate
     * @return Renderable
     */
    public function update(Affiliate $affiliate)
    {
        $validated = request()->validate([
            'code' => 'required|unique:affiliates,code,' . $affiliate->id,
            'balance' => 'required|numeric',
            'discount' => 'required|numeric',
            'commission' => 'required|numeric',
        ]);

        $affiliate->code = request()->input('code');
        $affiliate->balance = request()->input('balance');
        $affiliate->discount = request()->input('discount');
        $affiliate->commission = request()->input('commission');
        $affiliate->save();

        return redirect()->back()->with('success', 'Successfully updated affiliate');
    }

    /**
     * Affiliate management screen for affiliates
     * @return Renderable
     */
    public function payouts()
    {
        $payouts = AffiliatePayout::latest()->paginate(10);
        return view(Theme::moduleView('affiliates','payouts'), compact('payouts'));
    }

    public function editPayout($payout)
    {
        $payout = AffiliatePayout::findOrFail($payout);
        $affiliate = $payout->affiliate;
        return view(Theme::moduleView('affiliates','update-payout'), compact('payout', 'affiliate'));
    }

    public function updatePayout($payout)
    {
        $payout = AffiliatePayout::findOrFail($payout);
        $validated = request()->validate([
            'amount' => 'required|numeric',
            'status' => 'required',
            'gateway' => 'required',
            'address' => 'nullable',
            'transaction_id' => 'nullable',
            'email_user' => 'bool',
        ]);

        $payout->amount = request()->input('amount');
        $payout->status = request()->input('status');
        $payout->gateway = request()->input('gateway');
        $payout->address = request()->input('address');
        $payout->transaction_id = request()->input('transaction_id');
        $payout->save();

        if(request()->input('email_user', false)) {
            app()->setLocale($payout->user->language);
            $payout->user->email([
                'subject' => 'Affiliate Payout Request',
                'content' => "
                    We've updated your payout request <br><br>

                    Status: $payout->status <br>
                    Amount: $payout->amount <br>
                    Gateway: $payout->gateway <br>
                    Address: $payout->address <br>
                    Transaction ID: $payout->transaction_id <br><br>

                    Login to your affiliate account on our website for more information. Affiliate >> Cashout >> My Payouts

                ",
                'button' => [
                    'name' => 'My Account',
                    'url' => route('affiliates.manage'),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Successfully updated affiliate');
    }
}
