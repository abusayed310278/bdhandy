<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleFuelRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleFuelController extends Controller
{
    private function profile() { return Auth::user()->providerProfile; }

    public function index(Vehicle $vehicle)
    {
        abort_unless($vehicle->business_profile_id === $this->profile()->id, 403);
        $records = $vehicle->fuelRecords()->orderByDesc('fuel_date')->paginate(20);
        return view('business.vehicles.fuel', compact('vehicle', 'records'));
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        abort_unless($vehicle->business_profile_id === $this->profile()->id, 403);
        $request->validate([
            'fuel_date'       => 'required|date',
            'liters_filled'   => 'required|numeric|min:0.01',
            'cost_per_liter'  => 'nullable|numeric|min:0',
            'odometer_reading'=> 'nullable|numeric',
            'station_name'    => 'nullable|string',
            'cost_currency_id'=> 'nullable|exists:currencies,id',
        ]);

        $total = $request->liters_filled * ($request->cost_per_liter ?? 0);
        VehicleFuelRecord::create(array_merge($request->validated(), [
            'vehicle_id'          => $vehicle->id,
            'business_profile_id' => $vehicle->business_profile_id,
            'total_cost'          => $total,
        ]));

        if ($request->odometer_reading) {
            $vehicle->update(['current_odometer_km' => $request->odometer_reading]);
        }

        return back()->with('success', 'Fuel record added.');
    }
}
