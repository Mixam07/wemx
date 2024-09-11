<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Settings;
use App\Models\User;

class ApiKey extends Model
{
    protected $table = 'api_keys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'api_version',
        'description',
        'secret',
        'permissions',
        'allowed_ips',
        'last_used_at',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<int, string>
     */
    protected $casts = [
        'permissions' => 'collection',
        'allowed_ips' => 'collection',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'secret',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hash a value using sha256 and a salt
     * 
     * @param string $value
     * @return string
    */
    public static function hash($value)
    {
        self::generateSalt();
        return hash('sha256', $value . settings('encrypted::salt'));
    }

    /**
     * Generate salt for enhanced security.
    */
    protected static function generateSalt(): void
    {
        if(!Settings::has('encrypted::salt')) {
            $salt = Str::random(48);
            Settings::put('encrypted::salt', $salt);
        }
    }

    /**
     * Check if a given permission is allowed for this API key
    */
    public function hasPerm(string $permission): bool
    {
        if($this->full_permissions) {
            return true;
        }

        if(empty($this->permissions)) {
            return false;
        }

        return $this->permissions->contains($permission);
    }

    /**
     * Email user when unauthorized access is attempted
    */
    public function unauthorizedIP(): void
    {
        $this->user->email([
            'subject' => 'Unauthorized IP address attempted to access your API key',
            'content' => "An unauthorized IP address attempted to access your API key. <br><br>
            
            <strong>IP Address:</strong> " . request()->ip() . "<br>
            <strong>API ID:</strong> " . $this->id . "<br>
            <strong>API Key:</strong> " . $this->description . "<br>
            <strong>User Agent:</strong> " . request()->userAgent() . "<br>
            <strong>Time:</strong> " . Carbon::now()->toDateTimeString() . "<br>
            <strong>URL:</strong> " . request()->fullUrl() . "<br>
            "
        ]);
    }
}
