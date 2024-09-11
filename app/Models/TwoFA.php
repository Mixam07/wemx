<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class TwoFA extends Model
{
    protected $table = 'user_2fa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'key',
        'recovery_codes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'key',
        'recovery_codes'
    ];

    protected $casts = [
        'recovery_codes' => 'array',
        'session_expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Validate 2FA code
     *
     * @param Google2FA $google2fa Instance of Google2FA library
     * @param string $code The 2FA code to validate
     * @return bool True if the code is valid, otherwise false
     */
    public function validate(string $code): bool
    {
        $google2fa = app(\PragmaRX\Google2FALaravel\Google2FA::class);
        return $google2fa->verifyKey($this->key, $code);
    }

    /**
     * Invalidate 2FA session
     *
     * @var array<int, string>
     */
    public function invalidate()
    {
        $this->session_expires_at = Carbon::now()->subDays(5);
        $this->save();
    }

    /**
     * Require user to validate 2FA
     * 
     * This function is similar to the invalidate but can be called at different times
     *
     * @var array<int, string>
     */
    public function require()
    {
        $this->session_expires_at = Carbon::now()->subDays(5);
        $this->save();
    }

    /**
     * Disable 2FA
     *
     * @var array<int, string>
     */
    public function disable()
    {
        $this->delete();
    }

    
    public function setKeyAttribute($value)
    {
        $this->attributes['key'] = encrypt($value);
    }

    public function setRecoveryCodesAttribute($value)
    {
        $this->attributes['recovery_codes'] = encrypt($value);
    }

    public function getKeyAttribute($value)
    {
        return decrypt($value);
    }

    public function getRecoveryCodesAttribute($value)
    {
        return decrypt($value);
    }
}