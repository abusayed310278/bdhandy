<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LanguageController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view languages', only: ['index']),
            new Middleware('permission:create languages', only: ['create', 'store']),
            new Middleware('permission:edit languages', only: ['edit', 'update']),
            new Middleware('permission:delete languages', only: ['destroy']),
        ];
    }
    public function index()
    {
        $languages = Language::orderBy('is_default', 'desc')->get();
        return view('admin.languages.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.languages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages,code',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->is_default) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        Language::create($request->all());

        return redirect()->route('admin.languages.index')->with('success', 'Language added successfully.');
    }

    public function edit(Language $language)
    {
        return view('admin.languages.edit', compact('language'));
    }

    public function update(Request $request, Language $language)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages,code,'.$language->id,
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->is_default) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $language->update($request->all());

        return redirect()->route('admin.languages.index')->with('success', 'Language updated successfully.');
    }

    public function destroy(Language $language)
    {
        if ($language->is_default) {
            return back()->with('error', 'Cannot delete default language.');
        }
        $language->delete();
        return redirect()->route('admin.languages.index')->with('success', 'Language deleted successfully.');
    }
}
