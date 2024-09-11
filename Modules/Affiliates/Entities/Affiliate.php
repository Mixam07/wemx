<?php

namespace Modules\Affiliates\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;

class Affiliate extends Model
{
    use HasFactory;
    protected $table = 'affiliates';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(AffiliateInvite::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(AffiliatePayout::class);
    }

    public function createInvite()
    {
        $invite = new AffiliateInvite;
        $invite->affiliate_id = $this->id;
        $invite->user_id = (auth()->check()) ? auth()->user()->id : NULL;
        $invite->created_an_account = (auth()->check()) ? true : false;
        $invite->save();

        return $invite;
    }

    public static function calculateDiscountPercentage($code)
    {
        $affiliate = Affiliate::firstWhere('code', $code);

        return $affiliate ? $affiliate->discount : 0;
    }

    public static function calculateDiscountFactor($code, $only_percent = false)
    {
        $affiliate = Affiliate::firstWhere('code', $code);
        if ($only_percent){
            return $affiliate ? $affiliate->discount : 0;
        }

        return $affiliate ? $affiliate->discount / 100 : 0;
    }

    // create affiliate account if the user doesn't already have one
    public static function getOrCreate()
    {
        if(auth()->user()->affiliate()->exists()) {
            return auth()->user()->affiliate()->first();
        }

        $affiliate = new Affiliate;
        $affiliate->user_id = auth()->user()->id;
        $affiliate->code = strtoupper(Str::random(6));
        $affiliate->commission = settings('affiliates::default_comission', 10);
        $affiliate->discount = settings('affiliates::default_discount', 10);
        $affiliate->save();

        return $affiliate;
    }

}
