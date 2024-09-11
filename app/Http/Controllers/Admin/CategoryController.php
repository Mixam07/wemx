<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use App\Models\Categories;
use App\Facades\AdminTheme as Theme;

class CategoryController extends Controller
{

    /**
     * Display a list of all categories.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $categories = Categories::all();
        return Theme::view('categories.index', compact('categories'));
    }

    /**
     * Show the form to create a new category.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return Theme::view('categories.create');
    }

    /**
     * Save the newly created category to the database.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'status' => 'required',
            'name' => 'required|max:100',
            'link' => 'required|unique:categories|max:100',
            'icon' => 'required|image|max:4048', // Maximum file size is 4MB
            'order' => 'integer',
            'description' => 'nullable',
        ]);

        // remove any spaces and replace with '-'
        $validatedData['link'] = str_replace(' ', '-', $validatedData['link']);

        $category = new Categories;
        $category->name = $validatedData['name'];
        $category->link = $validatedData['link'];

        // store icon image
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = $validatedData['link'] . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/products', $filename);
            $category->icon = $filename;
        }

        $category->status = $request->input('status');
        $category->order = $validatedData['order'] ?? 0;
        $category->description = $validatedData['description'] ?? '';
        $category->save();

        return redirect(route('categories.index'))->with('success',
            trans('responses.categories_create_success', ['name' => $category->name, 'default' => 'The category :name created successfully.']));
    }

    /**
     * Show form to edit category.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $category = Categories::query()->find($id);
        return Theme::view('categories.edit', compact('category'));
    }

    /**
     * Update the category in the database.
     *
     * @param Request $request
     * @param int $id
     * @return Application|Redirector|RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'status' => 'required',
            'name' => 'required|max:100',
            'link' => 'required|unique:categories,link,' . $id . '|max:100',
            'icon' => 'image|max:4048', // Maximum file size is 4MB
            'order' => 'integer',
            'description' => 'nullable',
        ]);

        // remove any spaces and replace with '-'
        $validatedData['link'] = str_replace(' ', '-', $validatedData['link']);

        $category = Categories::query()->find($id);
        $category->status = $request->input('status');
        $category->name = $validatedData['name'];
        $category->link = $validatedData['link'];

        // store icon image
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = $validatedData['link'] . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/products', $filename);
            $category->icon = $filename;
        }

        $category->order = $validatedData['order'] ?? 0;
        $category->description = $validatedData['description'] ?? '';
        $category->save();

        return redirect(route('categories.index'))->with('success',
            trans('responses.categories_update_success', ['name' => $category->name, 'default' => 'The category :name update successfully.']));
    }

    /**
     * Soft Delete the category from the database.
     *
     * @param Categories $category
     * @return RedirectResponse
     */
    public function destroy(Categories $category)
    {
        if ($category->packages()->exists()) {
            return redirect()->back()->with('error',
                trans('responses.categories_delete_error', ['name' => $category->name, 'default' => 'The category :name must not have any packages attached to it to be deleted.']));
        }

        $category->delete();
        return redirect()->back()->with('success',
            trans('responses.categories_delete_success', ['name' => $category->name, 'default' => 'The category :name deleted.']));
    }
}
