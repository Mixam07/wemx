<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Facades\Theme;
use App\Models\Settings;

class MaintenanceController extends Controller
{

    public function activation()
    {


        return Theme::view('restricted', [
            'title' => 'Access Restricted',
            'desc' => Settings::get('registration_activation_message',
                'Your account has been placed in a queue and requires manual approval by an administrator.'),
            'color' => 'warning',
            'icon' => '<i class=\'bx bx-user\' ></i>']);
    }

    public function maintenance()
    {
        return Theme::view('restricted', ['title' => 'Maintenance',
            'desc' => Settings::get('maintenance_message'),
            'color' => 'warning',
            'icon' => '<i class=\'bx bx-hard-hat\' ></i>']);
    }

}
