<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\TeamAttendance;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    private function profile() { return Auth::user()->providerProfile; }

    public function index(Request $request)
    {
        $profile = $this->profile();
        $date    = $request->date ? \Carbon\Carbon::parse($request->date) : now();

        $records = TeamAttendance::with('member')
            ->where('business_profile_id', $profile->id)
            ->whereDate('clock_in_time', $date)
            ->orderBy('clock_in_time')
            ->get();

        $members = TeamMember::where('business_profile_id', $profile->id)
            ->where('status', 'active')->get();

        return view('business.attendance.index', compact('records', 'members', 'date'));
    }

    public function show(Request $request, string $date)
    {
        $profile = $this->profile();
        $day     = \Carbon\Carbon::parse($date);

        $records = TeamAttendance::with('member')
            ->where('business_profile_id', $profile->id)
            ->whereDate('clock_in_time', $day)
            ->orderBy('clock_in_time')
            ->get();

        return view('business.attendance.show', compact('records', 'day'));
    }

    public function memberHistory(TeamMember $member, Request $request)
    {
        abort_unless($member->business_profile_id === $this->profile()->id, 403);

        $records = TeamAttendance::where('team_member_id', $member->id)
            ->orderByDesc('clock_in_time')
            ->paginate(30);

        $stats = TeamAttendance::where('team_member_id', $member->id)
            ->selectRaw('COUNT(*) as total_days, SUM(CASE WHEN status="clocked_out" THEN 1 ELSE 0 END) as completed_days, SUM(total_hours) as total_hours, SUM(CASE WHEN is_verified=1 THEN 1 ELSE 0 END) as verified_days')
            ->first();

        return view('business.attendance.member-history', compact('member', 'records', 'stats'));
    }

    public function verify(TeamAttendance $attendance)
    {
        abort_unless($attendance->business_profile_id === $this->profile()->id, 403);
        $attendance->update(['is_verified' => true]);

        return back()->with('success', 'Attendance verified.');
    }
}
