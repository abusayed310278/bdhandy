<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    private function profile() { return Auth::user()->providerProfile; }

    public function index()
    {
        $profile = $this->profile();
        $items   = Inventory::where('business_profile_id', $profile->id)->orderBy('name')->paginate(25);
        return view('business.inventory.index', compact('items'));
    }

    public function lowStock()
    {
        $profile = $this->profile();
        $items   = Inventory::where('business_profile_id', $profile->id)
            ->whereColumn('quantity_in_stock', '<=', 'low_stock_threshold')
            ->orderBy('quantity_in_stock')
            ->get();
        return view('business.inventory.low-stock', compact('items'));
    }

    public function create()
    {
        $currencies = \App\Models\Currency::where('status', 'active')->get();
        return view('business.inventory.form', ['item' => null, 'currencies' => $currencies]);
    }

    public function store(Request $request)
    {
        $profile = $this->profile();
        $data    = $this->validated($request);

        if ($request->hasFile('photo')) {
            $imageController = new \App\Http\Controllers\ImageController();
            $data['photo'] = $imageController->uploadInventoryPhoto($request->file('photo'));
        }

        $data['business_profile_id'] = $profile->id;
        Inventory::create($data);
        return redirect()->route('business.inventory.index')->with('success', 'Item added to inventory.');
    }

    public function edit(Inventory $item)
    {
        $this->authorize($item);
        $currencies = \App\Models\Currency::where('status', 'active')->get();
        return view('business.inventory.form', compact('item', 'currencies'));
    }

    public function update(Request $request, Inventory $item)
    {
        $this->authorize($item);
        $data = $this->validated($request);

        if ($request->hasFile('photo')) {
            $imageController = new \App\Http\Controllers\ImageController();
            $data['photo'] = $imageController->uploadInventoryPhoto($request->file('photo'), $item->photo);
        }

        $item->update($data);
        return redirect()->route('business.inventory.index')->with('success', 'Item updated.');
    }

    public function restock(Request $request, Inventory $item)
    {
        $this->authorize($item);
        $request->validate(['quantity' => 'required|numeric|min:0.01', 'notes' => 'nullable|string']);

        $before = $item->quantity_in_stock;
        $after  = $before + $request->quantity;

        InventoryTransaction::create([
            'inventory_id'       => $item->id,
            'business_profile_id'=> $item->business_profile_id,
            'transaction_type'   => 'restock',
            'quantity'           => $request->quantity,
            'quantity_before'    => $before,
            'quantity_after'     => $after,
            'performed_by'       => Auth::id(),
            'notes'              => $request->notes,
        ]);

        $item->update(['quantity_in_stock' => $after]);
        return back()->with('success', "Restocked {$request->quantity} {$item->unit}.");
    }

    public function transactions(Inventory $item)
    {
        $this->authorize($item);
        $transactions = $item->transactions()->orderByDesc('created_at')->paginate(30);
        return view('business.inventory.transactions', compact('item', 'transactions'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'                => 'required|string|max:255',
            'sku'                 => 'nullable|string|max:100',
            'category'            => 'nullable|string|max:100',
            'unit'                => 'nullable|string|max:50',
            'quantity_in_stock'   => 'nullable|numeric|min:0',
            'low_stock_threshold' => 'nullable|numeric|min:0',
            'unit_cost'           => 'nullable|numeric|min:0',
            'cost_currency_id'    => 'nullable|exists:currencies,id',
            'supplier_name'       => 'nullable|string|max:255',
            'supplier_contact'    => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
            'photo'               => 'nullable|image|max:4096',
        ]);
    }

    private function authorize(Inventory $item): void
    {
        abort_unless($item->business_profile_id === $this->profile()->id, 403);
    }
}
