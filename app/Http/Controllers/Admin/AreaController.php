<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\Facades\DataTables;

class AreaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view areas', only: ['index']),
            new Middleware('permission:create areas', only: ['create', 'store']),
            new Middleware('permission:edit areas', only: ['edit', 'update']),
            new Middleware('permission:delete areas', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Area::with('district.division.country');

        // Real Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('district', function($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%")
                        ->orWhereHas('division', function($divq) use ($search) {
                            $divq->where('name', 'like', "%{$search}%");
                        });
                  });
            });
        }

        $areas = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('admin.areas.index', compact('areas'));
    }


    public function create()
    {
        $districts = District::with('division.country')->get();
        return view('admin.areas.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:areas',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Area::create($request->all());

        return redirect()->route('admin.areas.index')->with('success', 'Area created successfully.');
    }

    public function edit(Area $area)
    {
        $districts = District::with('division.country')->get();
        return view('admin.areas.edit', compact('area', 'districts'));
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:areas,slug,'.$area->id,
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $area->update($request->all());


        return redirect()->route('admin.areas.index')->with('success', 'Area updated successfully.');
    }

    public function destroy(Area $area)
    {
        $area->delete();
        return redirect()->route('admin.areas.index')->with('success', 'Area deleted successfully.');
    }
}
