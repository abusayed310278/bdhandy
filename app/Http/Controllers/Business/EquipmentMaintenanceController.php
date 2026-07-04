<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentMaintenanceController extends Controller
{
    private function profile() { return Auth::user()->providerProfile; }

    public function index(Equipment $equipment)
    {
        abort_unless($equipment->business_profile_id === $this->profile()->id, 403);
        $records = $equipment->maintenance()->orderByDesc('maintenance_date')->paginate(20);
        return view('business.equipment.maintenance', compact('equipment', 'records'));
    }

    public function store(Request $request, Equipment $equipment)
    {
        abort_unless($equipment->business_profile_id === $this->profile()->id, 403);
        $request->validate([
            'maintenance_type'     => 'required|in:scheduled,repair,calibration,inspection',
            'maintenance_date'     => 'required|date',
            'next_maintenance_date'=> 'nullable|date|after:maintenance_date',
            'description'          => 'nullable|string',
            'performed_by'         => 'nullable|string|max:255',
            'cost'                 => 'nullable|numeric|min:0',
            'cost_currency_id'     => 'nullable|exists:currencies,id',
            'status'               => 'required|in:scheduled,completed,cancelled',
        ]);

        $profile = $this->profile();
        EquipmentMaintenance::create(array_merge($request->validated(), [
            'equipment_id'        => $equipment->id,
            'business_profile_id' => $profile->id,
        ]));

        if ($request->status === 'in_maintenance') {
            $equipment->update(['status' => 'in_maintenance']);
        } elseif ($request->status === 'completed' && $equipment->status === 'in_maintenance') {
            $equipment->update(['status' => 'available']);
        }

        return back()->with('success', 'Maintenance record saved.');
    }
}
