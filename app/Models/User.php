<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\HasApiTokens;
use Modules\Affiliates\Entities\Affiliate;
use App\Models\Admin\Group;
use Carbon\Carbon;
use App\Events;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string $first_name
 * @property string $last_name
 * @property string|null $avatar
 * @property string $group
 * @property string $language
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, Group> $groups
 * @property-read int|null $groups_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @property string $status
 * @property float $balance
 * @property int $is_subscribed
 * @property string|null $data
 * @property-read Collection<int, Device> $devices
 * @property-read int|null $devices_count
 * @property-read Collection<int, Order> $orders
 * @property-read int|null $orders_count
 * @property-read Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property Carbon $last_login_at
 * @property $visibility
 * @property int $verification_code
 * @property mixed $address
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsSubscribed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'first_name',
        'last_name',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'recovery_codes',
        'google2fa_secret',
        'verification_code'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Define allowed filters for the application and API.
     *
     * @var array
     */
    public static array $filters = [
        'email',
        'username',
        'first_name',
        'last_name',
        'language',
        'status',
        'balance',
        'visibility',
        'is_subscribed',
        'is_online',
        'created_at',
    ];

    /**
     * Define dispatchable events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => Events\UserCreated::class,
        'updated' => Events\UserUpdated::class,
        'deleted' => Events\UserDeleted::class,
    ];

    protected static function boot(): void
    {
        parent::boot();
        
        static::creating(function ($user) {
            $user->language = settings('language', 'en');
        });

        static::created(function ($user) {
            $user->createEmptyAddress();
        });
    }

    /**
     * Email a user
     * Example
     *
     * Required array params
     * ['subject' => '', 'content' => '']
     *
     * $user->email([
     *      'subject' => 'Test email',
     *      'content' => 'This is a test email sent from admin side',
     *  ]);
     *
     * @param array $data
     * @return mixed $email
     */
    public function email(array $data): mixed
    {
        return EmailHistory::query()->create([
            'user_id' => $this->id,
            'sender' => config('mail.from.address'),
            'receiver' => $this->email,
            'subject' => $data['subject'],
            'content' => $data['content'],
            'button' => isset($data['button']) ? $data['button'] : NULL,
            'attachment' => isset($data['attachment']) ? $data['attachment'] : NULL,
        ]);
    }

    /**
     * Send a notification to a user
     * Example
     *
     * Required array params
     * ['subject' => '', 'content' => '']
     *
     * $user->notify([
     *      'type' => 'success, danger or warning',
     *      'icon' => '<i class="bx bx-bel"'></i>', // icon from boxicons
     *      'message' => 'hey, welcome back! This is a test',
     *  ]);
     *
     * @param array $data
     * @return mixed $notification
     */
    public function notify(array $data): mixed
    {
        return Notification::query()->create([
            'user_id' => $this->id,
            'type' => $data['type'],
            'icon' => $data['icon'],
            'message' => $data['message'],
            'button_url' => $data['button_url'] ?? NULL,
        ]);
    }

    /**
     * Send a password reset notification to the user.
     *
     */
    public function sendPasswordResetEmail(): void
    {
        $token = $this->passwordResetToken();

        $this->email([
            'subject' => __('auth.password_reset'),
            'content' => __('auth.password_reset_content'),
            'button' => [
                'name' => __('auth.password_reset'),
                'url' => route('reset-password', $token)
            ]
        ]);
    }

    /**
     * Punish a user
     * Example
     *
     * Required array params
     * ['type' => 'ban'] // types: warning, ban, ipban
     *
     * Optional array params
     * ['staff_id' => 1, 'reason' => 'Breaking Rules', 'ip_address' => 127.0.0.1, 'expiry_date' => now()->addDays(14)]
     *
     * $user->punish([
     *      'type' => 'success, danger or warning',
     *      // (optional params)
     *  ]);
     *
     * @param array $data
     * @return mixed $punishment
     */
    public function punish(array $data)
    {
        $punishment = new Punishment;
        $punishment->id = rand(100000, 999999);
        $punishment->user_id = $this->id;
        $punishment->staff_id = $data['staff_id'] ?? auth()->user()->id;
        $punishment->type = $data['type'];
        $punishment->reason = $data['reason'] ?? null;
        $punishment->ip_address = $data['ip_address'] ?? null;
        $punishment->expires_at = $data['expiry_date'] ?? null;
        $punishment->save();

        return $punishment;
    }

    /**
     * Scope a query to only include users who were seen online in the last 5 minutes.
     *
     * Usage:
     * User::getOnlineUsers()->get();
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetOnlineUsers($query)
    {
        return $query->where('last_seen_at', '>', now()->subMinutes(5));
    }

    /**
     * Returns bool value depending on if the user has been seen online
     * in the last 5 minutes
     *
     * @return boolean
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at > now()->subMinutes(5);
    }

    /**
     * This method is called when a user has logged out
     *
     * @return void
     */
    public function loggedOut()
    {
        // if user has 2FA enabled, invalidate the sessin
        if ($this->TwoFa()->exists()) {
            $this->twoFa->invalidate();
        }
    }

    /**
     * This method is called when a user has logged in
     *
     * @return void
     */
    public function loggedIn()
    {
        $this->updateLastLoginAt();

        // if user has 2FA enabled, prompt them to verify
        $this->require2FA();
    }

    /**
     * This method checks whether 2FA is enabled for a specific user,
     *
     * @return bool
     */
    public function has2FA(): bool
    {
        return $this->TwoFa()->exists();
    }

    /**
     * This method checks whether 2FA is enabled for a specific user,
     * if so will require them to verify before they continue
     *
     * @return void
     */
    public function require2FA()
    {
        if ($this->TwoFa()->exists()) {
            $this->twoFa->require();
        }
    }

    /**
     * This method disables 2FA if it is enabled for a specific user,
     *
     * @return void
     */
    public function disable2FA()
    {
        if ($this->TwoFa()->exists()) {
            $this->twoFa->disable();
        }
    }

    /**
     * This method is called when the user is deleted
     * $user->terminate();
     *
     * The user is permanently deleted, including all data, payments and orders.
     *
     * @return void
     */
    public function terminate()
    {
        $this->delete();
    }

    /**
     * Create an empty address
     *
     * @return void
     */
    public function createEmptyAddress()
    {
        // check if user doesnt have address, create an empty one
        if (!$this->address) {
            $address = new Address();
            $address->user_id = $this->id;
            $address->company_name = null;
            $address->address = null;
            $address->address_2 = null;
            $address->country = null;
            $address->city = null;
            $address->region = null;
            $address->zip_code = null;
            $address->save();
        }
    }

    public function passwordResetToken()
    {
        return PasswordReset::createPasswordResetToken($this->email);
    }

    public function punishments(): HasMany
    {
        return $this->hasMany(Punishment::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function ips(): HasMany
    {
        return $this->hasMany(UserIp::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function deletion_requests(): HasOne
    {
        return $this->hasOne(UserDelete::class);
    }

    public function TwoFa(): HasOne
    {
        return $this->hasOne(TwoFA::class);
    }

    public function affiliate(): HasOne
    {
        return $this->hasOne(Affiliate::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function suborders(): HasMany
    {
        return $this->hasMany(OrderMember::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function oauth(): HasMany
    {
        return $this->hasMany(UserOauth::class);
    }

    public function oauthService($service)
    {
        return $this->hasMany(UserOauth::class)->whereDriver($service);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(EmailHistory::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function balance_transactions(): HasMany
    {
        return $this->hasMany(BalanceTransaction::class);
    }

    public function balance($description, $type, $amount, $payment_id = NULL): bool
    {

        BalanceTransaction::query()->create([
            'user_id' => $this->id,
            'payment_id' => $payment_id,
            'description' => $description,
            'result' => $type,
            'amount' => $amount,
            'balance_before_transaction' => $this->balance,
            'currency' => 'USD',
        ]);

        if ($type == '+') {
            $this->increment('balance', $amount);
        } elseif ($type == '-') {
            $this->decrement('balance', $amount);
        } elseif ($type == '=') {
            $this->balance = $amount;
        }
        $this->save();

        return true;
    }

    public function hasPerm(string $permission_name): bool
    {
        // ensure the user with id 1 is admin
        if ($this->id == 1) {
            return true;
        }

        if ($this->groups()->whereHas('permissions', function ($query) use ($permission_name) {
            $query->where('name', 'admin.root');
        })->exists()) {
            return true;
        }

        return $this->groups()->whereHas('permissions', function ($query) use ($permission_name) {
            $query->where('name', $permission_name);
        })->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasPerm('admin.view');
    }

    // this function still exits for backwards compatibility
    public function is_admin(): bool
    {
        return $this->isAdmin();
    }

    // add full name attribute
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isRootAdmin(): bool
    {
        return $this->hasPerm('admin.root');
    }

    public function is_verified(): bool
    {
        return (bool)$this->email_verified_at;
    }

    public function avatar(): string
    {
        return $this->avatar ? asset('storage/avatars/' . $this->avatar) : settings('default_avatar', 'https://imgur.com/koz9j8a.png');
    }

    public function updateLastLoginAt(): void
    {
        $this->last_login_at = Carbon::now();
        $this->save();
    }

    public static function avatarUri($user_id): string
    {
        $user = User::query()->find($user_id);
        if ($user->avatar == NULL) {
            return asset('storage/avatars/default.jpg');
        }

        return asset('storage/avatars/' . $user->avatar);
    }

    public function setVisibility($status): void
    {
        if (in_array($status, ['online', 'away', 'busy', 'offline'])) {
            $this->visibility = $status;
            $this->save();
        }
    }

    public function generateVerificationCode(): int
    {
        if ($this->verification_code == NULL) {
            $this->verification_code = rand(100000, 999999);
            $this->save();
        }

        return $this->verification_code;
    }


    public function allAccessibleOrders(): Builder
    {
        $ownOrdersQuery = Order::query()
            ->where('user_id', $this->id)
            ->select('orders.*')
            ->toBase();

        $accessibleOrdersQuery = Order::query()
            ->join('order_members', 'orders.id', '=', 'order_members.order_id')
            ->where('order_members.user_id', $this->id)
            ->select('orders.*')
            ->toBase();

        return $ownOrdersQuery->union($accessibleOrdersQuery);
    }


}
