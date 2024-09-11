<?php

namespace Modules\PagePlus\Http\Controllers;

use App\Facades\Theme;
use Illuminate\Routing\Controller;
use Modules\PagePlus\Entities\PagePlus;

class PagePlusController extends Controller
{
    public function show($slug)
    {
        $page = PagePlus::getBySlug($slug);
        return view(Theme::moduleView('pageplus', 'index'), compact('page'));
    }
}
