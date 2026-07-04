<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\Facades\DataTables;

class DivisionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view divisions', only: ['index']),
            new Middleware('permission:create divisions', only: ['create', 'store']),
            new Middleware('permission:edit divisions', only: ['edit', 'update']),
            new Middleware('permission:delete divisions', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Division::with('country');

        // Real Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('country', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $divisions = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('admin.divisions.index', compact('divisions'));
    }


    public function create()
    {
        $countries = Country::where('status', 'active')->get();
        return view('admin.divisions.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:divisions',
        ]);

        Division::create($request->all());

        return redirect()->route('admin.divisions.index')->with('success', 'Division created successfully.');
    }

    public function edit(Division $division)
    {
        $countries = Country::where('status', 'active')->get();
        return view('admin.divisions.edit', compact('division', 'countries'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:divisions,slug,'.$division->id,
        ]);

        $division->update($request->all());


        return redirect()->route('admin.divisions.index')->with('success', 'Division updated successfully.');
    }

    public function destroy(Division $division)
    {
        $division->delete();
        return redirect()->route('admin.divisions.index')->with('success', 'Division deleted successfully.');
    }
}
