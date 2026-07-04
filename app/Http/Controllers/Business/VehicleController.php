<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleAssignment;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    private function profile() { return Auth::user()->providerProfile; }

    public function index(Request $request)
    {
        $profile  = $this->profile();
        $vehicles = Vehicle::with('currentAssignment.member')
            ->where('business_profile_id', $profile->id)
            ->orderBy('plate_number')
            ->paginate(20);

        $teamMembers = TeamMember::where('business_profile_id', $profile->id)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        // Query assignments for history report
        $assignmentsQuery = VehicleAssignment::with(['vehicle', 'member', 'assignedBy'])
            ->where('business_profile_id', $profile->id);

        if ($request->filled('vehicle_id')) {
            $assignmentsQuery->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('team_member_id')) {
            $assignmentsQuery->where('team_member_id', $request->team_member_id);
        }

        if ($request->filled('assignment_status')) {
            $assignmentsQuery->where('status', $request->assignment_status);
        }

        $assignments = $assignmentsQuery->orderByDesc('assigned_at')
            ->paginate(15, ['*'], 'history_page')
            ->withQueryString();

        // Collections for the filters
        $allVehiclesForFilter = Vehicle::where('business_profile_id', $profile->id)
            ->orderBy('plate_number')
            ->get();

        $allMembersForFilter = TeamMember::where('business_profile_id', $profile->id)
            ->orderBy('full_name')
            ->get();

        return view('business.vehicles.index', compact(
            'vehicles',
            'teamMembers',
            'assignments',
            'allVehiclesForFilter',
            'allMembersForFilter'
        ));
    }

    public function create()
    {
        return view('business.vehicles.form', ['vehicle' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['business_profile_id'] = $this->profile()->id;
        Vehicle::create($data);
        return redirect()->route('business.vehicles.index')->with('success', 'Vehicle added.');
    }

    public function edit(Vehicle $vehicle)
    {
        $this->authorize($vehicle);
        return view('business.vehicles.form', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorize($vehicle);
        $vehicle->update($this->validated($request));
        return redirect()->route('business.vehicles.index')->with('success', 'Vehicle updated.');
    }

    public function assign(Request $request, Vehicle $vehicle)
    {
        $this->authorize($vehicle);
        abort_if($vehicle->status === 'assigned', 422, 'Vehicle already assigned.');
        $request->validate(['team_member_id' => 'required|exists:team_members,id', 'odometer' => 'nullable|numeric']);

        VehicleAssignment::create([
            'vehicle_id'           => $vehicle->id,
            'team_member_id'       => $request->team_member_id,
            'business_profile_id'  => $vehicle->business_profile_id,
            'assigned_by'          => Auth::id(),
            'odometer_at_assignment'=> $request->odometer ?? $vehicle->current_odometer_km,
        ]);
        $vehicle->update(['status' => 'assigned']);
        return back()->with('success', 'Vehicle assigned.');
    }

    public function returnVehicle(Request $request, Vehicle $vehicle)
    {
        $this->authorize($vehicle);
        $request->validate(['odometer' => 'nullable|numeric', 'notes' => 'nullable|string']);

        $assignment = $vehicle->currentAssignment;
        if ($assignment) {
            $assignment->update([
                'returned_at'       => now(),
                'odometer_at_return'=> $request->odometer,
                'status'            => 'returned',
                'notes'             => $request->notes,
            ]);
        }
        $vehicle->update([
            'status'              => 'available',
            'current_odometer_km' => $request->odometer ?? $vehicle->current_odometer_km,
        ]);
        return back()->with('success', 'Vehicle returned.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'vehicle_type'        => 'required|in:bike,car,van,truck,other',
            'make'                => 'nullable|string|max:100',
            'model'               => 'nullable|string|max:100',
            'year'                => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'color'               => 'nullable|string|max:50',
            'plate_number'        => 'required|string|max:30',
            'vin'                 => 'nullable|string|max:50',
            'registration_expiry' => 'nullable|date',
            'insurance_expiry'    => 'nullable|date',
            'fitness_expiry'      => 'nullable|date',
            'fuel_type'           => 'required|in:petrol,diesel,cng,electric',
            'fuel_tank_capacity_liters' => 'nullable|numeric',
            'notes'               => 'nullable|string',
        ]);
    }

    private function authorize(Vehicle $vehicle): void
    {
        abort_unless($vehicle->business_profile_id === $this->profile()->id, 403);
    }
}
