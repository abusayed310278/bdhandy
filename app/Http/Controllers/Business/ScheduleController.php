<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\TeamDailySchedule;
use App\Models\TeamJobAssignment;
use App\Models\TeamMember;
use App\Models\TeamScheduleWaypoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    private function profile() { return Auth::user()->providerProfile; }

    public function index(Request $request)
    {
        $profile = $this->profile();
        
        // Month navigation
        $monthStr = $request->input('month', now()->format('Y-m'));
        try {
            $month = \Carbon\Carbon::parse($monthStr . '-01');
        } catch (\Throwable $e) {
            $month = today()->startOfMonth();
        }

        $prevMonth = $month->copy()->subMonth()->format('Y-m');
        $nextMonth = $month->copy()->addMonth()->format('Y-m');

        // Fetch team members for filter dropdown and calendars
        $members = TeamMember::where('business_profile_id', $profile->id)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        // Query team job assignments for the selected month
        $assignmentsQuery = TeamJobAssignment::with(['member', 'request'])
            ->where('business_profile_id', $profile->id)
            ->whereBetween('scheduled_start_time', [
                $month->copy()->startOfMonth()->startOfDay(),
                $month->copy()->endOfMonth()->endOfDay()
            ]);

        // Filter by team member
        if ($request->filled('team_member_id')) {
            $assignmentsQuery->where('team_member_id', $request->team_member_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $assignmentsQuery->where('status', $request->status);
        }

        $assignments = $assignmentsQuery->get();

        // Group assignments by date (Y-m-d) for easy lookup in the calendar grid
        $assignmentsByDate = $assignments->groupBy(function ($item) {
            return $item->scheduled_start_time ? $item->scheduled_start_time->format('Y-m-d') : '';
        });

        // Calendar grid calculations
        $daysInMonth = $month->daysInMonth;
        $firstDayOfWeek = $month->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
        
        // Blank cells before the 1st of the month
        $blankDays = $firstDayOfWeek; 

        // Shorthand for jumping to day-level schedule
        $today = today()->format('Y-m-d');

        return view('business.schedule.index', compact(
            'members',
            'month',
            'prevMonth',
            'nextMonth',
            'daysInMonth',
            'blankDays',
            'assignmentsByDate',
            'today'
        ));
    }

    public function show(string $date)
    {
        $profile = $this->profile();
        $day     = \Carbon\Carbon::parse($date);

        $schedules = TeamDailySchedule::with(['member', 'waypoints.jobAssignment.request'])
            ->where('business_profile_id', $profile->id)
            ->whereDate('schedule_date', $day)
            ->get();

        $unscheduled = TeamJobAssignment::with(['member', 'request'])
            ->where('business_profile_id', $profile->id)
            ->whereDate('scheduled_start_time', $day)
            ->whereDoesntHave('waypoint')
            ->whereNotIn('status', ['completed', 'rejected', 'reassigned'])
            ->get();

        return view('business.schedule.show', compact('schedules', 'unscheduled', 'day'));
    }

    public function optimize(Request $request)
    {
        $profile = $this->profile();
        $request->validate([
            'member_id' => 'required|exists:team_members,id',
            'date'      => 'required|date',
        ]);

        $member = TeamMember::findOrFail($request->member_id);
        abort_unless($member->business_profile_id === $profile->id, 403);

        $jobs = TeamJobAssignment::with('request')
            ->where('team_member_id', $member->id)
            ->whereDate('scheduled_start_time', $request->date)
            ->whereNotIn('status', ['completed', 'rejected', 'reassigned'])
            ->get();

        // Nearest-neighbor heuristic sort by scheduled_start_time
        $sorted = $jobs->sortBy('scheduled_start_time')->values();

        $schedule = TeamDailySchedule::updateOrCreate(
            ['team_member_id' => $member->id, 'schedule_date' => $request->date],
            [
                'business_profile_id'  => $profile->id,
                'total_jobs_assigned'  => $sorted->count(),
                'is_published'         => false,
            ]
        );

        $schedule->waypoints()->delete();
        foreach ($sorted as $i => $job) {
            TeamScheduleWaypoint::create([
                'daily_schedule_id' => $schedule->id,
                'job_assignment_id' => $job->id,
                'sequence_order'    => $i + 1,
            ]);
        }

        return back()->with('success', "Schedule optimized for {$member->full_name}.");
    }

    public function publish(Request $request, TeamDailySchedule $schedule)
    {
        abort_unless($schedule->business_profile_id === $this->profile()->id, 403);
        $schedule->update(['is_published' => true]);

        return back()->with('success', 'Schedule published to technician.');
    }

    public function reorder(Request $request)
    {
        $profile = $this->profile();
        $request->validate([
            'waypoints' => 'required|array',
            'waypoints.*' => 'required|exists:team_schedule_waypoints,id',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $profile) {
            // 1. Fetch matching waypoints belonging to this business to ensure security
            $waypoints = TeamScheduleWaypoint::whereIn('id', $request->waypoints)
                ->whereHas('schedule', function ($q) use ($profile) {
                    $q->where('business_profile_id', $profile->id);
                })->get()->keyBy('id');

            // 2. Temporarily set all sequence_orders to a high value to avoid constraint collision
            foreach ($request->waypoints as $index => $wpId) {
                if (isset($waypoints[$wpId])) {
                    $waypoints[$wpId]->update(['sequence_order' => 10000 + $index]);
                }
            }

            // 3. Set the final correct sequence_order values
            foreach ($request->waypoints as $index => $wpId) {
                if (isset($waypoints[$wpId])) {
                    $waypoints[$wpId]->update(['sequence_order' => $index + 1]);
                }
            }
        });

        return response()->json(['success' => true]);
    }
}
