<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CountryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view countries', only: ['index']),
            new Middleware('permission:create countries', only: ['create', 'store']),
            new Middleware('permission:edit countries', only: ['edit', 'update']),
            new Middleware('permission:delete countries', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Country::query();

        // Real Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('iso_code', 'like', "%{$search}%")
                  ->orWhere('phone_code', 'like', "%{$search}%")
                  ->orWhere('currency_code', 'like', "%{$search}%");
            });
        }

        $countries = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('admin.countries.index', compact('countries'));
    }


    public function create()
    {
        return view('admin.countries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'required|string|max:2|unique:countries',
            'phone_code' => 'required|string|max:10',
            'currency_code' => 'required|string|max:3',
            'currency_symbol' => 'required|string|max:10',
            'locale' => 'required|string|max:5',
            'direction' => 'required|in:ltr,rtl',
            'status' => 'required|in:active,inactive',
        ]);


        Country::create($request->all());

        return redirect()->route('admin.countries.index')->with('success', 'Country created successfully.');
    }

    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'required|string|max:2|unique:countries,iso_code,'.$country->id,
            'phone_code' => 'required|string|max:10',
            'currency_code' => 'required|string|max:3',
            'currency_symbol' => 'required|string|max:10',
            'locale' => 'required|string|max:5',
            'direction' => 'required|in:ltr,rtl',
            'status' => 'required|in:active,inactive',
        ]);


        $country->update($request->all());

        return redirect()->route('admin.countries.index')->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return redirect()->route('admin.countries.index')->with('success', 'Country deleted successfully.');
    }
}
