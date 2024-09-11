<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Facades\AdminTheme as Theme;
use App\Models\ErrorLog;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class WebhooksController extends Controller
{
    public function index()
    {
        return Theme::view('webhooks');
    }
}
