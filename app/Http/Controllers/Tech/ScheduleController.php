<?php
namespace App\Http\Controllers\Tech;

use App\Http\Controllers\Controller;
use App\Models\TeamDailySchedule;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    private function member()
    {
        $m = Auth::user()->teamMember;
        abort_if(! $m, 403, 'Not linked to a team member.');
        return $m;
    }

    public function today()
    {
        return $this->show(today()->format('Y-m-d'));
    }

    public function show(string $date)
    {
        $member = $this->member();
        abort_unless($member->hasTeamPermission('schedule', 'view_daily_schedule'), 403, 'No permission to view schedule.');
        $day = \Carbon\Carbon::parse($date);

        $schedule = TeamDailySchedule::with(['waypoints' => fn($q) => $q->with('jobAssignment.request')])
            ->where('team_member_id', $member->id)
            ->whereDate('schedule_date', $day)
            ->where('is_published', true)
            ->first();

        $lastLocation = $member->locations()->latest('location_time')->first();

        $mapPins = $schedule?->waypoints
            ->filter(fn($wp) => $wp->jobAssignment?->request?->latitude && $wp->jobAssignment?->request?->longitude)
            ->map(fn($wp) => [
                'seq'     => $wp->sequence_order,
                'lat'     => (float) $wp->jobAssignment->request->latitude,
                'lng'     => (float) $wp->jobAssignment->request->longitude,
                'number'  => $wp->jobAssignment->request->request_number ?? '#',
                'address' => $wp->jobAssignment->request->address ?? '',
                'status'  => $wp->jobAssignment->status ?? 'assigned',
            ])->values()->toArray() ?? [];

        return view('tech.schedule.today', compact('schedule', 'day', 'member', 'lastLocation', 'mapPins'));
    }
}
