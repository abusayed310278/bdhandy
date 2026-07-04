<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ServiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view services', only: ['index']),
            new Middleware('permission:create services', only: ['create', 'store']),
            new Middleware('permission:edit services', only: ['edit', 'update']),
            new Middleware('permission:delete services', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Service::with('category');
        $languages = Language::getActiveLanguages();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search, $languages) {
                $q->where('slug', 'like', "%{$search}%");
                foreach($languages as $lang) {
                    $q->orWhere("translations->{$lang->code}->name", 'like', "%{$search}%");
                }
            });
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('admin.services.index', compact('services', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $languages = Language::getActiveLanguages();
        return view('admin.services.create', compact('categories', 'languages'));
    }

    public function store(Request $request)
    {
        $languages = Language::getActiveLanguages();
        $defaultLang = $languages->where('is_default', true)->first() ?? $languages->first();

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            "translations.{$defaultLang->code}.name" => 'required|string|max:255',
        ]);

        $data = $request->except(['image', 'pricing_type']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        $categories = Category::all();
        $languages = Language::getActiveLanguages();
        return view('admin.services.edit', compact('service', 'categories', 'languages'));
    }

    public function update(Request $request, Service $service)
    {
        $languages = Language::getActiveLanguages();
        $defaultLang = $languages->where('is_default', true)->first() ?? $languages->first();

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            "translations.{$defaultLang->code}.name" => 'required|string|max:255',
        ]);

        $data = $request->except(['image', 'pricing_type']);

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}
