<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use Illuminate\Http\Request;

class VehicleMaintenanceController extends Controller
{
    private function profile() { return auth()->user()->providerProfile; }

    public function index(Vehicle $vehicle)
    {
        abort_unless($vehicle->business_profile_id === $this->profile()->id, 403);
        $records = $vehicle->maintenance()->orderByDesc('maintenance_date')->paginate(20);
        return view('business.vehicles.maintenance', compact('vehicle', 'records'));
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        abort_unless($vehicle->business_profile_id === $this->profile()->id, 403);
        $request->validate([
            'maintenance_type'        => 'required|in:oil_change,tyre,brake,engine,body,inspection,other',
            'maintenance_date'        => 'required|date',
            'next_service_date'       => 'nullable|date',
            'odometer_at_service'     => 'nullable|numeric',
            'next_service_odometer_km'=> 'nullable|numeric',
            'workshop_name'           => 'nullable|string',
            'cost'                    => 'nullable|numeric',
            'cost_currency_id'        => 'nullable|exists:currencies,id',
            'status'                  => 'required|in:scheduled,completed,cancelled',
            'description'             => 'nullable|string',
        ]);

        VehicleMaintenance::create(array_merge($request->validated(), [
            'vehicle_id'          => $vehicle->id,
            'business_profile_id' => $vehicle->business_profile_id,
        ]));

        if ($request->status === 'scheduled') {
            $vehicle->update(['status' => 'in_maintenance']);
        } elseif ($request->status === 'completed') {
            $vehicle->update(['status' => 'available']);
        }

        return back()->with('success', 'Maintenance record saved.');
    }
}
