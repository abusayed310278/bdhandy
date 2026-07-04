<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\Facades\DataTables;

class CurrencyController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view currencies', only: ['index']),
            new Middleware('permission:create currencies', only: ['create', 'store']),
            new Middleware('permission:edit currencies', only: ['edit', 'update']),
            new Middleware('permission:delete currencies', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Currency::query();

        // Real Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
            });
        }

        $currencies = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('admin.currencies.index', compact('currencies'));
    }


    public function create()
    {
        return view('admin.currencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'status' => 'required|in:active,inactive',
        ]);

        Currency::create($request->all());

        return redirect()->route('admin.currencies.index')->with('success', 'Currency created successfully.');
    }

    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'status' => 'required|in:active,inactive',
        ]);

        $currency->update($request->all());

        return redirect()->route('admin.currencies.index')->with('success', 'Currency updated successfully.');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();
        return redirect()->route('admin.currencies.index')->with('success', 'Currency deleted successfully.');
    }
}
