<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamServiceController extends Controller
{
    public function sync(Request $request, TeamMember $member)
    {
        $profile = Auth::user()->providerProfile;
        abort_unless($member->business_profile_id === $profile->id, 403);

        $request->validate([
            'service_ids'   => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
            'skill_levels'  => 'nullable|array',
            'primary_service' => 'nullable|integer',
        ]);

        $member->services()->delete();
        foreach ($request->input('service_ids', []) as $serviceId) {
            $member->services()->create([
                'service_id'          => $serviceId,
                'business_profile_id' => $profile->id,
                'skill_level'         => $request->input("skill_levels.{$serviceId}", 'mid'),
                'is_primary'          => ($request->input('primary_service') == $serviceId),
            ]);
        }

        return back()->with('success', 'Service skills updated.');
    }
}
