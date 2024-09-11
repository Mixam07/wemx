<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Facades\AdminTheme as Theme;
use App\Models\ErrorLog;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class LogsController extends Controller
{


    public function index()
    {
        $logs = ErrorLog::query()->latest()->where('severity', request()->input('severity', 'CRITICAL'))->paginate(25);
        return Theme::view('logs', compact('logs'));
    }
}
