<?php

namespace Modules\PagePlus\Http\Controllers;

use App\Facades\AdminTheme;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\PagePlus\Entities\PageHelper;
use Modules\PagePlus\Entities\PagePlus;
use Modules\PagePlus\Rules\UniqueSlug;

class AdminPagePlusController extends Controller
{
    public function index()
    {
        PageHelper::clearAllCache();
        $pages = PagePlus::whereNull('parent_id')->paginate(20);
        return view(AdminTheme::moduleView('pageplus', 'index'), compact('pages'));
    }

    public function create(PagePlus $page = null)
    {
        $pages = PagePlus::get();
        return view(AdminTheme::moduleView('pageplus', 'create'), compact('pages', 'page'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'nullable|exists:page_pluses,id',
            'parent_id' => 'nullable|exists:page_pluses,id',
            'slug' => ['required', 'string', 'regex:/^[A-Za-z0-9\-_]+$/', new UniqueSlug($request->input('id'))],
            'order' => 'required|integer',
            'locale' => 'nullable|string',
            'title' => 'nullable|string',
            'content' => 'nullable|string',
        ]);

        $page = PagePlus::updateOrCreate(
            ['id' => $data['id'] ?? null],
            ['parent_id' => $data['parent_id'], 'slug' => $data['slug'], 'order' => $data['order']]
        );

        $page->translations()->updateOrCreate(
            ['locale' => $data['locale'] ?? app()->getLocale()],
            ['title' => $data['title'] ?? '', 'content' => $data['content'] ?? '']
        );

        if ($request->has('meta')) {
            foreach ($request->input('meta') as $key => $value) {
                $page->metas()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value ?? '']
                );
            }
        }

        return redirect()->route('admin.pageplus.index')->with('success', __('pageplus::messages.page_saved_success'));
    }

    public function destroy(PagePlus $page)
    {
        $page->delete();
        return redirect()->route('admin.pageplus.index');
    }

    public function translate(PagePlus $page, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return view(AdminTheme::moduleView('pageplus', 'translate'), compact('page', 'locale'));
    }

    public function translateStore(Request $request, PagePlus $page)
    {
        $data = $request->validate([
            'locale' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
        ]);
        $page->translations()->updateOrCreate(
            ['locale' => $data['locale']],
            ['title' => $data['title'], 'content' => $data['content']]
        );

        return redirect()->route('admin.pageplus.index')->with('success', __('pageplus::messages.translate_saved_success'));
    }

    public function changeOrder(PagePlus $page, $action = 'down')
    {
        if ($action == 'up') {
            $page->increment('order');
        } else {
            $page->decrement('order');
        }
        return redirect()->back()->with('success', __('pageplus::messages.order_change_success'));
    }

    public function toggleEditor()
    {
        $editor = Cache::get('pageplus::editor', 'summernote');
        Cache::put('pageplus::editor', $editor == 'summernote' ? 'tinymce' : 'summernote');
        return redirect()->back();
    }

}
