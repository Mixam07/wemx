<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use App\Models\Settings;
use App\Models\User;
use App\Models\UserOauth;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OauthController extends Controller
{
    public string|null|object $service;

    public function __construct()
    {
        $this->service = request()->route('service');

        $services = ['github', 'discord', 'google'];
        if (!in_array($this->service, $services)) {
            redirect()->back()->send();
        }
        $this->setConfig();
    }

    public function login()
    {
        return Socialite::driver($this->service)->redirect();
    }

    public function connect()
    {
        try {
            return Socialite::driver($this->service)->redirect();
        } catch (Exception $error) {
            ErrorLog::catch('oauth::connect::' . $this->service, $error->getMessage());
            return redirect()->route('user.settings')->with('error',
                trans('auth.oauth_failed',
                    ['default' => "Authentication Failed: Please contact an Administrator, errors have been logged."])
            );
        }
    }

    public function remove()
    {
        if (Auth::user()->oauthService($this->service)->exists()) {
            Auth::user()->oauthService($this->service)->first()->delete();
        }

        return redirect()->route('user.settings')->with('success',
            trans('auth.oauth_disconnect',
                ['service' => $this->service, 'default' => ":service was disconnected from your account."])
        );
    }

    public function callback()
    {
        try {
            $oauthUser = Socialite::driver($this->service)->user();
            // store or update the user for the service
            $this->{$this->service}($oauthUser);
        } catch (Exception $error) {
            ErrorLog::catch('oauth::callback::' . $this->service, $error->getMessage());
            return redirect()->route('user.settings')->with('error',
                trans('auth.oauth_connect_error',
                    ['default' => "Something went wrong, please try again."])
            );
        }

        return redirect()->route('user.settings')->with('success',
            trans('auth.oauth_connect_success',
                ['service' => $this->service, 'default' => "Successfully connected :service to your account."])
        );
    }

    // store the user for Google
    protected function google($user)
    {
        // ensure user is verified on Google
        if (!$user->user['verified_email']) {
            return redirect()->route('user.settings')->with('error',
                trans('auth.oauth_google_verified_error',
                    ['default' => "Your Google account is not verified."])
            );
        }
        $this->loginOrRegisterOauthUser($user);
        UserOauth::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'driver' => $this->service,
            ],
            [
                'driver' => $this->service,
                'email' => $user->getEmail(),
                'data' => $user->user,
            ]
        );
    }

    // store the user for GitHub
    protected function github($user)
    {
        $githubUser = Http::withToken($user->token)->get('https://api.github.com/user/emails');
        $verified = collect($githubUser->json())->first(fn($email) => $email['verified'] === true && $email['primary'] === true);
        if (!$verified) {
            return redirect()->route('user.settings')->with('error',
                trans('auth.auth.oauth_failed',)
            );
        }
        $this->loginOrRegisterOauthUser($user);
        UserOauth::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'driver' => $this->service,
            ],
            [
                'driver' => $this->service,
                'email' => $user->getEmail(),
                'external_profile' => $user->user['html_url'],
                'data' => $user->user,
            ]
        );
    }

    // store the user for GitHub
    protected function discord($user)
    {
        // ensure user is verified on discord
        if (!$user->user['verified']) {
            return redirect()->route('user.settings')->with('error',
                trans('auth.oauth_discord_verified_error',
                    ['default' => 'Your Discord account is not verified.'])
            );
        }
        $this->loginOrRegisterOauthUser($user);
        UserOauth::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'driver' => $this->service,
            ],
            [
                'driver' => $this->service,
                'email' => $user->getEmail(),
                'data' => $user->user,
            ]
        );
    }

    /**
     * This function dynamically sets the values for config/services.php
     * so that oauth services can retrieve settings set by the user in the admin area
     *
     * @return void
     */
    protected function setConfig(): void
    {
        config([
            'services.' . $this->service . '.client_id' => Settings::getJson('encrypted::oauth::' . $this->service, 'client_id')
        ]);

        config([
            'services.' . $this->service . '.client_secret' => Settings::getJson('encrypted::oauth::' . $this->service, 'client_secret')
        ]);

        config([
            'services.' . $this->service . '.redirect' => config('app.url') . '/oauth/' . $this->service . '/redirect'
        ]);
    }

    private function loginOrRegisterOauthUser($oauthUser)
    {
        if (!Auth::check()) {
            // Trying to find a user by email in UserOauth
            $userOauth = UserOauth::where('email', $oauthUser->getEmail())->first();
            if ($userOauth) {
                // check if user is staff and whether staff can login using sso
                if ($userOauth->user->isAdmin() && !settings('staff_sso_login', false)) {
                    $this->reportSSOLogin($userOauth->user);
                    dd('Staff cannot login using SSO, please contact an administrator.');
                }

                // If found, authorize the user
                $user = User::find($userOauth->user_id);
                Auth::login($user, true);
                return;
            }

            // Trying to find a user by email in User
            $user = User::where('email', $oauthUser->getEmail())->first();
            $nickname = $oauthUser->getNickname() ?: Str::random(10); // If the nickname is empty, generate a random one

            // We check the uniqueness of the nickname
            while (User::where('username', $nickname)->exists()) {
                $nickname = $nickname . Str::random(5); // We add a unique suffix
            }

            if (!$user) {
                // If the user is not found, we create a new one
                $password = Str::random(16);
                $user = User::create([
                    'username' => $nickname,
                    'email' => $oauthUser->getEmail(),
                    'first_name' => $oauthUser->getName(),
                    'status' => 'active',
                    'password' => Hash::make($password),
                ]);
                
                $user->email([
                    'subject' => config('app.name'),
                    'content' => "Your account has been created with the following details: <br> Username: " . $user->username . "<br> Password: " . $password . "<br> Please login and change your password.",
                    'button' => [
                        'name' => config('app.name'),
                        'url' => route('login'),
                    ]
                ]);
            }

            // check if user is staff and whether staff can login using sso
            if ($user->isAdmin() && !settings('staff_sso_login', false)) {
                $this->reportSSOLogin($user);
                dd('Staff cannot login using SSO, please contact an administrator.');
            }

            Auth::login($user, true);
        }
    }

    protected function reportSSOLogin($user)
    {
        // Report the login to the user
        $user->email([
            'subject' => 'Failed Login Attempt using ' . $this->service,
            'content' => "You have attempted to login using " . $this->service . ". SSO logins for staff members have been disabled in settings. <br><br> If this was not you, please contact an administrator.",
        ]);
    }
}
