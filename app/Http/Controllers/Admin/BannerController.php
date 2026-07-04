<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Intervention\Image\Laravel\Facades\Image;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view banners', only: ['index']),
            new Middleware('permission:create banners', only: ['create', 'store']),
            new Middleware('permission:edit banners', only: ['edit', 'update']),
            new Middleware('permission:delete banners', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Banner::query();

        // Real Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $banners = $query->orderBy('sort_order', 'asc')->paginate(10)->withQueryString();

        return view('admin.banners.index', compact('banners'));
    }


    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link' => 'nullable|string|max:500',
            'position' => 'required|in:main,sidebar,popup',
            'type' => 'required|in:web,app',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            
            // Resize image for optimal display
            $img = Image::read($image->getRealPath());
            $img->cover(1200, 400); 
            
            $path = 'banners/' . $filename;
            Storage::disk('public')->put($path, (string) $img->encode());
            $data['image'] = $path;
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link' => 'nullable|string|max:500',
            'position' => 'required|in:main,sidebar,popup',
            'type' => 'required|in:web,app',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }

            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            
            $img = Image::read($image->getRealPath());
            $img->cover(1200, 400);
            
            $path = 'banners/' . $filename;
            Storage::disk('public')->put($path, (string) $img->encode());
            $data['image'] = $path;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }


    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
