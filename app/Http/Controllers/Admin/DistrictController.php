<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\Facades\DataTables;

class DistrictController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view districts', only: ['index']),
            new Middleware('permission:create districts', only: ['create', 'store']),
            new Middleware('permission:edit districts', only: ['edit', 'update']),
            new Middleware('permission:delete districts', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = District::with('division.country');

        // Real Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('division', function($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $districts = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('admin.districts.index', compact('districts'));
    }


    public function create()
    {
        $divisions = Division::with('country')->get();
        return view('admin.districts.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:districts',
        ]);

        District::create($request->all());

        return redirect()->route('admin.districts.index')->with('success', 'District created successfully.');
    }

    public function edit(District $district)
    {
        $divisions = Division::with('country')->get();
        return view('admin.districts.edit', compact('district', 'divisions'));
    }

    public function update(Request $request, District $district)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:districts,slug,'.$district->id,
        ]);

        $district->update($request->all());


        return redirect()->route('admin.districts.index')->with('success', 'District updated successfully.');
    }

    public function destroy(District $district)
    {
        $district->delete();
        return redirect()->route('admin.districts.index')->with('success', 'District deleted successfully.');
    }
}
