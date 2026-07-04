<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\TeamLocationTracking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LocationController extends Controller
{
    private function profile() { return Auth::user()->providerProfile; }

    public function live()
    {
        $profile = $this->profile();

        $members = TeamMember::with(['attendance' => fn($q) => $q->where('status', 'clocked_in')->latest('clock_in_time')->limit(1)])
            ->where('business_profile_id', $profile->id)
            ->where('status', 'active')
            ->get()
            ->map(function ($member) {
                $cached = Cache::get("team_location:{$member->id}");
                return [
                    'id'        => $member->id,
                    'name'      => $member->full_name,
                    'code'      => $member->employee_code,
                    'photo'     => $member->profile_photo ? \Storage::url($member->profile_photo) : null,
                    'clocked_in'=> $member->attendance->isNotEmpty(),
                    'location'  => $cached,
                ];
            });

        return view('business.location.live', compact('members'));
    }

    public function memberHistory(TeamMember $member)
    {
        abort_unless($member->business_profile_id === $this->profile()->id, 403);

        $locations = TeamLocationTracking::where('team_member_id', $member->id)
            ->whereDate('created_at', today())
            ->orderBy('created_at')
            ->get(['latitude', 'longitude', 'speed_kmh', 'is_moving', 'location_time']);

        return view('business.location.member-history', compact('member', 'locations'));
    }
}
