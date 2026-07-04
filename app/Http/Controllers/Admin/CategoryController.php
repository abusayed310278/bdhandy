<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view categories', only: ['index']),
            new Middleware('permission:create categories', only: ['create', 'store']),
            new Middleware('permission:edit categories', only: ['edit', 'update']),
            new Middleware('permission:delete categories', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Category::query();
        $languages = Language::getActiveLanguages();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search, $languages) {
                $q->where('slug', 'like', "%{$search}%");
                foreach($languages as $lang) {
                    $q->orWhere("translations->{$lang->code}", 'like', "%{$search}%");
                }
            });
        }

        $categories = $query->orderBy('sort_order', 'asc')->paginate(10)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $languages = Language::getActiveLanguages();
        return view('admin.categories.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $languages = Language::getActiveLanguages();
        $defaultLang = $languages->where('is_default', true)->first() ?? $languages->first();

        $rules = [
            "name_{$defaultLang->code}" => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
        $request->validate($rules);

        $category = new Category();
        
        foreach($languages as $lang) {
            $fieldName = "name_{$lang->code}";
            if($request->has($fieldName)) {
                $category->setTranslation('translations', $lang->code, $request->$fieldName);
            }
        }
        
        $category->status = $request->status;
        $category->sort_order = $request->sort_order ?? 0;

        if ($request->hasFile('icon')) {
            $category->icon = $request->file('icon')->store('categories/icons', 'public');
        }

        if ($request->hasFile('image')) {
            $category->image = $request->file('image')->store('categories/covers', 'public');
        }

        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $languages = Language::getActiveLanguages();
        return view('admin.categories.edit', compact('category', 'languages'));
    }

    public function update(Request $request, Category $category)
    {
        $languages = Language::getActiveLanguages();
        $defaultLang = $languages->where('is_default', true)->first() ?? $languages->first();

        $rules = [
            "name_{$defaultLang->code}" => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
        $request->validate($rules);

        foreach($languages as $lang) {
            $fieldName = "name_{$lang->code}";
            if($request->has($fieldName)) {
                $category->setTranslation('translations', $lang->code, $request->$fieldName);
            }
        }
        
        $category->status = $request->status;
        $category->sort_order = $request->sort_order ?? 0;

        if ($request->hasFile('icon')) {
            $category->icon = $request->file('icon')->store('categories/icons', 'public');
        }

        if ($request->hasFile('image')) {
            $category->image = $request->file('image')->store('categories/covers', 'public');
        }

        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
