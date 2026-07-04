<?php
namespace App\Http\Controllers\Tech;

use App\Http\Controllers\Controller;
use App\Models\TeamAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    private function member()
    {
        $m = Auth::user()->teamMember;
        abort_if(! $m, 403, 'Not linked to a team member.');
        return $m;
    }

    public function history()
    {
        $member  = $this->member();
        abort_unless($member->hasTeamPermission('attendance', 'view_own_history'), 403, 'No permission to view attendance history.');

        $openRecord = TeamAttendance::where('team_member_id', $member->id)
            ->where('status', 'clocked_in')
            ->latest('clock_in_time')
            ->first();

        $records = TeamAttendance::where('team_member_id', $member->id)
            ->orderByDesc('clock_in_time')
            ->paginate(30);

        $canClockInOut = $member->hasTeamPermission('attendance', 'clock_in_out');

        return view('tech.attendance.history', compact('records', 'openRecord', 'canClockInOut', 'member'));
    }

    public function clockIn(Request $request)
    {
        $member = $this->member();
        abort_unless($member->hasTeamPermission('attendance', 'clock_in_out'), 403, 'No permission to clock in.');

        $request->validate([
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address'   => 'nullable|string|max:500',
        ]);

        $open = TeamAttendance::where('team_member_id', $member->id)
            ->where('status', 'clocked_in')
            ->exists();

        if ($open) {
            return back()->with('error', 'You are already clocked in.');
        }

        TeamAttendance::create([
            'team_member_id'      => $member->id,
            'business_profile_id' => $member->business_profile_id,
            'clock_in_time'       => now(),
            'clock_in_latitude'   => $request->latitude,
            'clock_in_longitude'  => $request->longitude,
            'clock_in_address'    => $request->address,
            'status'              => 'clocked_in',
        ]);

        return back()->with('success', 'Clocked in successfully.');
    }

    public function clockOut(Request $request)
    {
        $member = $this->member();
        abort_unless($member->hasTeamPermission('attendance', 'clock_in_out'), 403, 'No permission to clock out.');

        $record = TeamAttendance::where('team_member_id', $member->id)
            ->where('status', 'clocked_in')
            ->latest('clock_in_time')
            ->firstOrFail();

        $record->clock_out_time       = now();
        $record->clock_out_latitude   = $request->latitude;
        $record->clock_out_longitude  = $request->longitude;
        $record->status               = 'clocked_out';
        $record->computeTotalHours();
        $record->save();

        return back()->with('success', 'Clocked out. Total: ' . $record->total_hours . 'h');
    }
}
