<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Punishment extends Model
{
    use HasFactory;
    protected $table = 'punishments';

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unban()
    {
        $this->type = 'unbanned';
        $this->expires_at = now();
        $this->save();
    }

    public static function hasActiveBans()
    {
        $ban = self::where('type', 'ipban')->where('ip_address', request()->getClientIp())->first();
        if($ban) {
            if(isset($ban->expires_at) AND $ban->expires_at->isPast()) {
                return false;
            }
            return true;
        }   

        if(auth()->check()) {
            $ban = auth()->user()->punishments()->where('type', 'ban')->orWhere('type', 'ipban')->first();
            if($ban) {
                if(isset($ban->expires_at) AND $ban->expires_at->isPast()) {
                    return false;
                }
                return true;
            }
        }

        return false;
    }

    public static function getActiveBan()
    {
        $ban = self::where('type', 'ipban')->where('ip_address', request()->getClientIp())->first();
        if($ban) {
            if(isset($ban->expires_at) AND $ban->expires_at->isPast()) {
                return false;
            }
            return $ban;
        }   

        if(auth()->check()) {
            $ban = auth()->user()->punishments()->where('type', 'ban')->orWhere('type', 'ipban')->first();
            if($ban) {
                if(isset($ban->expires_at) AND $ban->expires_at->isPast()) {
                    return false;
                }
                return $ban;
            }
        }

        return false;
    }
}
