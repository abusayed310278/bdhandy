<?php
namespace App\Http\Controllers\Tech;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\JobMaterialUsage;
use App\Models\ServiceRequest;
use App\Models\TeamJobAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
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
        abort_unless($member->hasTeamPermission('jobs', 'view_assigned'), 403, 'No permission to view assigned jobs.');

        $assignments = TeamJobAssignment::with('request')
            ->where('team_member_id', $member->id)
            ->orderBy('scheduled_start_time')
            ->get()
            ->groupBy(fn($a) => optional($a->scheduled_start_time)->format('Y-m-d')
                ?? optional($a->request?->preferred_date)->format('Y-m-d')
                ?? $a->created_at->format('Y-m-d'));

        return view('tech.jobs.index', compact('assignments', 'member'));
    }

    public function show(TeamJobAssignment $assignment)
    {
        $member = $this->member();
        abort_unless($member->hasTeamPermission('jobs', 'view_assigned'), 403, 'No permission to view assigned jobs.');
        abort_unless($assignment->team_member_id === $member->id, 403);

        $assignment->load(['request.customer', 'materialUsage.inventory']);
        $inventory = Inventory::where('business_profile_id', $member->business_profile_id)->get();

        return view('tech.jobs.show', compact('assignment', 'inventory'));
    }

    public function updateStatus(Request $request, TeamJobAssignment $assignment)
    {
        $member = $this->member();
        abort_unless($assignment->team_member_id === $member->id, 403);
        $request->validate(['status' => 'required|in:accepted,en_route,arrived,in_progress,paused,completed,rejected']);

        $newStatus = $request->status;

        if (in_array($newStatus, ['accepted', 'rejected'])) {
            abort_unless($member->hasTeamPermission('jobs', 'accept_reject'), 403, 'No permission to accept or reject jobs.');
        } else {
            abort_unless($member->hasTeamPermission('jobs', 'update_status'), 403, 'No permission to update job status.');
        }

        $timestamps = [
            'arrived'     => 'arrived_at_location',
            'in_progress' => 'started_at',
            'completed'   => 'completed_at',
        ];

        $data = ['status' => $newStatus];
        if (isset($timestamps[$newStatus])) {
            $data[$timestamps[$newStatus]] = now();
        }

        $assignment->update($data);

        // Sync ServiceRequest status based on assignment progression
        $serviceRequest = ServiceRequest::find($assignment->service_request_id);
        if ($serviceRequest) {
            if ($newStatus === 'accepted') {
                $serviceRequest->update(['request_status' => 'in_progress']);
            } elseif ($newStatus === 'completed') {
                $serviceRequest->update(['request_status' => 'completed', 'completed_at' => now()]);
            }
        }

        return back()->with('success', 'Status updated to ' . str_replace('_', ' ', $newStatus) . '.');
    }

    public function logMaterials(Request $request, TeamJobAssignment $assignment)
    {
        $member = $this->member();
        abort_unless($member->hasTeamPermission('inventory', 'log_material_usage'), 403, 'No permission to log material usage.');
        abort_unless($assignment->team_member_id === $member->id, 403);

        $request->validate([
            'materials'                => 'required|array|min:1',
            'materials.*.inventory_id' => 'required|exists:inventory,id',
            'materials.*.quantity'     => 'required|numeric|min:0.01',
            'materials.*.notes'        => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $assignment, $member) {
            foreach ($request->materials as $mat) {
                $item = Inventory::where('id', $mat['inventory_id'])
                    ->where('business_profile_id', $member->business_profile_id)
                    ->firstOrFail();

                $before = $item->quantity_in_stock;
                $after  = max(0, $before - $mat['quantity']);

                JobMaterialUsage::create([
                    'job_assignment_id'   => $assignment->id,
                    'team_member_id'      => $member->id,
                    'business_profile_id' => $member->business_profile_id,
                    'inventory_id'        => $item->id,
                    'quantity_used'       => $mat['quantity'],
                    'unit_cost_at_time'   => $item->unit_cost,
                    'cost_currency_id'    => $item->cost_currency_id,
                    'notes'               => $mat['notes'] ?? null,
                ]);

                InventoryTransaction::create([
                    'inventory_id'        => $item->id,
                    'business_profile_id' => $member->business_profile_id,
                    'transaction_type'    => 'usage',
                    'quantity'            => -$mat['quantity'],
                    'quantity_before'     => $before,
                    'quantity_after'      => $after,
                    'reference_type'      => 'job_material_usage',
                    'reference_id'        => $assignment->id,
                    'performed_by'        => Auth::id(),
                ]);

                $item->update(['quantity_in_stock' => $after]);
            }
        });

        return back()->with('success', 'Materials logged.');
    }
}
