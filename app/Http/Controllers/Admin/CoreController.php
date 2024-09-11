<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Facades\AdminTheme as Theme;

class CoreController extends Controller
{

    // return login page view
    public function setup()
    {
        return Theme::view('setup');
    }
}
