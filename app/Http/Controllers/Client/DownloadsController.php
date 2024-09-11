<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Facades\Theme;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class DownloadsController extends Controller
{

    public function index()
    {
        return Theme::view('downloads');
    }

}
