<?php
namespace App\Http\Controllers\Tech;

use App\Http\Controllers\Controller;
use App\Models\EquipmentAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    private function member()
    {
        $m = Auth::user()->teamMember;
        abort_unless($m, 403, 'Not linked to a team member.');
        return $m;
    }

    public function index()
    {
        $member = $this->member();
        abort_unless($member->hasTeamPermission('equipment', 'view_assigned_equipment'), 403, 'No permission to view equipment.');
        $assignments = EquipmentAssignment::with('equipment')
            ->where('team_member_id', $member->id)
            ->where('status', 'assigned')
            ->get();

        return view('tech.equipment.index', compact('assignments'));
    }

    public function reportIssue(Request $request, EquipmentAssignment $assignment)
    {
        $member = $this->member();
        abort_unless($member->hasTeamPermission('equipment', 'report_lost'), 403, 'No permission to report equipment issues.');
        abort_unless($assignment->team_member_id === $member->id, 403);
        $request->validate(['condition' => 'required|in:damaged,lost', 'notes' => 'nullable|string']);

        $assignment->update([
            'returned_condition' => $request->condition,
            'return_notes'       => $request->notes,
            'status'             => $request->condition === 'lost' ? 'lost' : 'assigned',
        ]);

        if ($request->condition === 'lost') {
            $assignment->equipment?->update(['status' => 'lost']);
        }

        return back()->with('success', 'Issue reported.');
    }
}
