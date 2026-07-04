<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    private function profile() { return Auth::user()->providerProfile; }

    public function index()
    {
        $profile   = $this->profile();
        $equipment = Equipment::with('currentAssignment.member')
            ->where('business_profile_id', $profile->id)
            ->orderBy('name')
            ->paginate(20);

        $members = \App\Models\TeamMember::where('business_profile_id', $profile->id)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        return view('business.equipment.index', compact('equipment', 'members'));
    }

    public function create()
    {
        return view('business.equipment.form', ['equipment' => null]);
    }

    public function store(Request $request)
    {
        $profile = $this->profile();
        $data    = $this->validated($request);
        $data['business_profile_id'] = $profile->id;
        $data['code'] = $this->generateCode($profile);
        Equipment::create($data);

        return redirect()->route('business.equipment.index')->with('success', 'Equipment added.');
    }

    public function edit(Equipment $equipment)
    {
        $this->authorize($equipment);
        return view('business.equipment.form', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $this->authorize($equipment);
        $equipment->update($this->validated($request));

        return redirect()->route('business.equipment.index')->with('success', 'Equipment updated.');
    }

    public function assign(Request $request, Equipment $equipment)
    {
        $this->authorize($equipment);
        abort_if($equipment->status === 'assigned', 422, 'Equipment is already assigned.');
        $request->validate(['team_member_id' => 'required|exists:team_members,id']);

        EquipmentAssignment::create([
            'equipment_id'        => $equipment->id,
            'team_member_id'      => $request->team_member_id,
            'business_profile_id' => $equipment->business_profile_id,
            'assigned_by'         => Auth::id(),
        ]);
        $equipment->update(['status' => 'assigned']);

        return back()->with('success', 'Equipment assigned.');
    }

    public function returnEquipment(Request $request, Equipment $equipment)
    {
        $this->authorize($equipment);
        $request->validate(['returned_condition' => 'required|in:good,damaged,lost', 'return_notes' => 'nullable|string']);

        $assignment = $equipment->currentAssignment;
        if ($assignment) {
            $assignment->update([
                'returned_at'        => now(),
                'returned_condition' => $request->returned_condition,
                'return_notes'       => $request->return_notes,
                'status'             => 'returned',
            ]);
        }
        $newStatus = $request->returned_condition === 'good' ? 'available' : 'needs_repair';
        $equipment->update(['status' => $newStatus, 'condition' => $request->returned_condition === 'good' ? 'good' : 'needs_repair']);

        return back()->with('success', 'Equipment returned.');
    }

    public function reportLost(Request $request, Equipment $equipment)
    {
        $this->authorize($equipment);
        $assignment = $equipment->currentAssignment;
        if ($assignment) {
            $assignment->update(['returned_condition' => 'lost', 'status' => 'lost', 'returned_at' => now()]);
        }
        $equipment->update(['status' => 'lost']);

        return back()->with('success', 'Equipment marked as lost.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'          => 'required|string|max:255',
            'category'      => 'nullable|string|max:100',
            'brand'         => 'nullable|string|max:100',
            'model'         => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price'=> 'nullable|numeric|min:0',
            'purchase_currency_id' => 'nullable|exists:currencies,id',
            'condition'     => 'required|in:new,good,fair,needs_repair,retired',
            'notes'         => 'nullable|string',
        ]);
    }

    private function authorize(Equipment $equipment): void
    {
        abort_unless($equipment->business_profile_id === $this->profile()->id, 403);
    }

    private function generateCode($profile): string
    {
        $count = Equipment::where('business_profile_id', $profile->id)->count() + 1;
        return 'EQ-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
