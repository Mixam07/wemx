<?php

namespace App\Services\Pterodactyl\Entities;

use App\Models\Order;
use App\Services\Pterodactyl\Api\Pterodactyl;
use Exception;
use Illuminate\Support\Str;

class User
{
    public Pterodactyl $api;

    public function __construct(Pterodactyl $api)
    {
        $this->api = $api;
    }

    public function get($user = false)
    {
        $user = !$user ? auth()->user() : $user;
        try {
            $pterodactylUser = $this->api->users->getExternal("wmx-" . $user->id);
            if (is_array($pterodactylUser) and array_key_exists('attributes', $pterodactylUser)) {
                return $pterodactylUser['attributes'];
            } else {
                return $this->create($user);
            }
        } catch (Exception $e) {
            ErrorLog("pterodactyl::User::get", "Failed to retrieve Pterodactyl user from its external id wmx-$user->id : {$e->getMessage()}");
            return ['error' => "Failed to retrieve Pterodactyl user from its external id wmx-$user->id : {$e->getMessage()}"];
        }
    }

    public function create($user)
    {
        $authUser = $user;
        $username = $this->sanitizeUsername($authUser->username);
        $user = $this->api->users->all("?filter[email]=" . $authUser->email);

        $firstName = $authUser->first_name ?: Str::random(5);
        $lastName = $authUser->last_name ?: Str::random(5);

        if (is_array($user) and array_key_exists('data', $user) and count($user['data'])) {
            // edit this users external id so next call it gets easier.
            $params = [
                "external_id" => "wmx-" . $authUser->id,
                "email" => $authUser->email,
                "username" => $username,
                "first_name" => $firstName,
                "last_name" => $lastName,
            ];

            $this->api->users->update($user['data'][0]['attributes']['id'], $params);
            return $user['data'][0]['attributes'];
        }


        // create a brand new pterodactyl user
        $user = [
            'external_id' => "wmx-" . $authUser->id,
            'email' => $authUser->email,
            'username' => $username . '-' . $authUser->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
        try {
            $data = $this->api->users->create($user)['attributes'];
        } catch (Exception $e) {
            ErrorLog("pterodactyl::user", "Failed to create Pterodactyl user with external id wmx-$authUser->id : {$e->getMessage()} data: " . $this->api->users->create($user));
            return ['error' => "Failed to create Pterodactyl user with external id wmx-$authUser->id : {$e->getMessage()}"];
        }
        return $data;
    }

    public function changePassword(Order $order, string $newPassword)
    {
        try {
            $ptero_user = $this->get($order->user);
            if (!$order->hasExternalUser()) {
                $order->createExternalUser([
                    'external_id' => $ptero_user['id'],
                    'username' => $ptero_user['email'],
                    'password' => $newPassword,
                    'data' => $ptero_user,
                ]);
            }
            $this->api->users->update($ptero_user['id'], [
                'email' => $ptero_user['email'],
                'username' => $ptero_user['username'],
                'first_name' => $ptero_user['first_name'],
                'last_name' => $ptero_user['last_name'],
                'password' => $newPassword,
            ]);
            $order->updateExternalPassword($newPassword);
        } catch (Exception $error) {
            return redirect()->back()->withError(__('responses.error_change_ptero_password', ['error_message' => $error->getMessage()]));
        }
        return redirect()->back()->withSuccess(__('responses.success_change_ptero_password'));
    }

    public static function sanitizeUsername($username): string
    {
        // Remove any character that is not alphanumeric, dash, underscore or dot
        $sanitized = preg_replace('/[^\w.-]/', '', $username);
        // Ensure it starts with an alphanumeric character
        $sanitized = preg_replace('/^[^a-zA-Z0-9]+/', '', $sanitized);
        // Ensure it ends with an alphanumeric character
        return preg_replace('/[^a-zA-Z0-9]+$/', '', $sanitized);
    }
}
