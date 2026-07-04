<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\TeamJobAssignment;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispatchController extends Controller
{
    private function profile() { return Auth::user()->providerProfile; }

    public function index()
    {
        $profile = $this->profile();

        $unassigned = ServiceRequest::where('provider_id', $profile->user_id)
            ->where('request_status', 'accepted')
            ->whereDoesntHave('teamAssignments', fn($q) => $q->where('assignment_type', 'primary')->whereNotIn('status', ['rejected', 'reassigned']))
            ->with('customer', 'service')
            ->latest()
            ->get();

        $members = TeamMember::with(['assignments' => fn($q) => $q->whereIn('status', ['assigned','accepted','en_route','in_progress'])])
            ->where('business_profile_id', $profile->id)
            ->where('status', 'active')
            ->get();

        return view('business.dispatch.index', compact('unassigned', 'members'));
    }

    public function assign(Request $request, ServiceRequest $job)
    {
        $profile = $this->profile();
        abort_unless($job->provider_id === $profile->user_id, 403);

        $request->validate([
            'team_member_id'      => 'required|exists:team_members,id',
            'scheduled_start_time'=> 'required|date',
        ]);

        TeamJobAssignment::updateOrCreate(
            ['service_request_id' => $job->id, 'assignment_type' => 'primary'],
            [
                'team_member_id'      => $request->team_member_id,
                'business_profile_id' => $profile->id,
                'assigned_by'         => Auth::id(),
                'scheduled_start_time'=> $request->scheduled_start_time,
                'status'              => 'assigned',
            ]
        );

        return back()->with('success', 'Job assigned successfully.');
    }

    public function unassign(TeamJobAssignment $assignment)
    {
        abort_unless($assignment->business_profile_id === $this->profile()->id, 403);
        abort_unless($assignment->status === 'assigned', 422, 'Only assignments in "assigned" status can be unassigned.');

        $assignment->update(['status' => 'reassigned']);

        return back()->with('success', 'Assignment removed. Job is available for reassignment.');
    }

    public function reassign(Request $request, TeamJobAssignment $assignment)
    {
        abort_unless($assignment->business_profile_id === $this->profile()->id, 403);

        $request->validate(['team_member_id' => 'required|exists:team_members,id']);

        $assignment->update([
            'team_member_id' => $request->team_member_id,
            'assigned_by'    => Auth::id(),
            'status'         => 'assigned',
        ]);

        return back()->with('success', 'Job reassigned.');
    }

    public function suggestions(ServiceRequest $job)
    {
        $profile = $this->profile();
        abort_unless($job->provider_id === $profile->user_id, 403);

        $suggestions = TeamMember::with('services')
            ->where('business_profile_id', $profile->id)
            ->where('status', 'active')
            ->whereHas('services', fn($q) => $q->where('service_id', $job->service_id))
            ->get()
            ->map(fn($m) => [
                'id'          => $m->id,
                'name'        => $m->full_name,
                'code'        => $m->employee_code,
                'active_jobs' => $m->assignments()->whereIn('status', ['assigned','accepted','in_progress'])->count(),
            ])
            ->sortBy('active_jobs')
            ->values();

        return response()->json($suggestions);
    }
}
