<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * App\Models\Device
 *
 * @property int $id
 * @property int $user_id
 * @property int $is_revoked
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $device_name
 * @property string|null $device_type
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Device newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Device newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Device query()
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereDeviceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereIsRevoked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereUserId($value)
 * @mixin \Eloquent
 */
class Device extends Model
{
    use HasFactory;
    protected $table = 'user_devices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_name',
        'device_type',
        'last_login_at',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public static function addDevice(Request $request, $user)
    {
        // Get the device information
        $userAgent = $request->header('User-Agent');

        if($userAgent == NULL) {
            return 0;
        }

        if($user->devices()->where('user_agent', $userAgent)->exists()) {
            $user->devices()->where('user_agent', $userAgent)->first()->update(['last_login_at' => Carbon::now(),'ip_address' => $request->ip()]);
            return 0;
        }

        $device = Device::updateOrCreate([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device_name' => Device::getDeviceCategory($userAgent),
            'device_type' => Device::getDeviceType($userAgent),
            'last_login_at' => Carbon::now(),
        ]);

        if($user->devices->count() > 1) {
            app()->setLocale($user->language);
            $user->email([
                'subject' => __('client.email_new_device_subject', ['app_name' => settings('app_name', config('app.name'))]),
                'content' =>
                emailMessage('new_device', $user->language) . __('client.email_add_device_content', [
                    'device_name' => $device->device_name,
                    'device_type' => $device->device_type,
                    'ip_address' => $device->ip_address,
                    'last_login_at' => $device->last_login_at,
                    'user_agent' => $device->user_agent
                ]),
                'button' => [
                    'name' => __('client.email_check_activity_btn'),
                    'url' => route('user.settings'),
                ],
            ]);

            // create notification
            $user->notify([
                'type' => 'danger',
                'icon' => "<i class='bx bx-desktop' ></i>",
                'message' => __('client.email_new_device_subject', ['app_name' => settings('app_name', 'WemX')]),
                'button_url' =>route('user.settings')
            ]);
        }
    }



    protected static function getDeviceType($userAgent)
    {
        $deviceType = 'Unknown';

        // Check if the user agent contains keywords for specific device types
        if (strpos($userAgent, 'iPhone') !== false) {
            $deviceType = 'iPhone';
        } elseif (strpos($userAgent, 'iPad') !== false) {
            $deviceType = 'iPad';
        } elseif (strpos($userAgent, 'Android') !== false) {
            $deviceType = 'Android';
        } elseif (strpos($userAgent, 'Windows Phone') !== false) {
            $deviceType = 'Windows Phone';
        } elseif (stripos($userAgent, 'Windows') !== false) {
            $deviceType = 'Windows';
        } elseif (stripos($userAgent, 'Macintosh') !== false) {
            $deviceType = 'Mac';
        } elseif (stripos($userAgent, 'Linux') !== false) {
            $deviceType = 'Linux';
        } elseif (strpos($userAgent, 'BlackBerry') !== false) {
            $deviceType = 'BlackBerry';
        }

        return $deviceType;
    }

    protected static function getDeviceCategory($userAgent)
    {
        $deviceCategory = 'Unknown';

        // Check if the user agent contains keywords for specific device categories
        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false || strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false || strpos($userAgent, 'Windows Phone') !== false || strpos($userAgent, 'BlackBerry') !== false) {
            $deviceCategory = 'Phone';
        } elseif (strpos($userAgent, 'Windows') !== false || strpos($userAgent, 'Macintosh') !== false || strpos($userAgent, 'Linux') !== false) {
            $deviceCategory = 'Desktop';
        }

        return $deviceCategory;
    }

    public function revoke()
    {
        if($this->is_revoked) {
            $this->is_revoked = false;
        } else {
            $this->is_revoked = true;
        }

        $this->save();
    }
}
