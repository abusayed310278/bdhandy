<?php
namespace App\Http\Controllers\Tech;

use App\Http\Controllers\Controller;
use App\Models\TeamLocationTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $member = Auth::user()->teamMember;
        abort_unless($member, 403);

        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'nullable|integer',
            'heading'   => 'nullable|numeric',
            'speed'     => 'nullable|numeric',
            'battery'   => 'nullable|integer',
            'is_moving' => 'nullable|boolean',
        ]);

        TeamLocationTracking::create([
            'team_member_id'      => $member->id,
            'business_profile_id' => $member->business_profile_id,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'accuracy_meters'     => $request->accuracy,
            'heading'             => $request->heading,
            'speed_kmh'           => $request->speed,
            'battery_level'       => $request->battery,
            'is_moving'           => $request->boolean('is_moving'),
            'location_time'       => now(),
        ]);

        Cache::put("team_location:{$member->id}", [
            'lat'        => $request->latitude,
            'lng'        => $request->longitude,
            'speed'      => $request->speed,
            'is_moving'  => $request->boolean('is_moving'),
            'updated_at' => now()->toIso8601String(),
        ], now()->addDay());

        return response()->json(['ok' => true]);
    }
}
