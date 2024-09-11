<?php

namespace Modules\Affiliates\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;

class AffiliateInvite extends Model
{
    use HasFactory;
    protected $table = 'affiliate_invites';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function hasRegistered()
    {
        $this->user_id = auth()->user()->id;
        $this->created_an_account = true;
        $this->save();
    }
}
