<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\TeamJobAssignment;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    private function profile() { return Auth::user()->providerProfile; }

    public function team()
    {
        $profile = $this->profile();
        $members = TeamMember::where('business_profile_id', $profile->id)
            ->where('status', 'active')
            ->get();

        $start = now()->startOfMonth();
        $end   = now()->endOfMonth();

        $stats = $members->map(function ($m) use ($start, $end) {
            $jobs = TeamJobAssignment::where('team_member_id', $m->id)
                ->whereBetween('assigned_at', [$start, $end])->get();
            $completed = $jobs->where('status', 'completed');
            return [
                'member'        => $m,
                'total_jobs'    => $jobs->count(),
                'completed'     => $completed->count(),
                'rate'          => $jobs->count() ? round($completed->count() / $jobs->count() * 100, 1) : 0,
                'avg_rating'    => round($completed->avg('customer_rating') ?? 0, 2),
            ];
        })->sortByDesc('rate')->values();

        $totals = [
            'jobs'      => $stats->sum('total_jobs'),
            'completed' => $stats->sum('completed'),
            'avg_rate'  => $stats->avg('rate') ?? 0,
            'avg_rating'=> round($stats->avg('avg_rating') ?? 0, 2),
        ];

        return view('business.analytics.team', compact('stats', 'totals'));
    }

    public function member(TeamMember $member)
    {
        abort_unless($member->business_profile_id === $this->profile()->id, 403);
        $member->load('services.service');

        $start = now()->subDays(30);
        $jobs  = TeamJobAssignment::where('team_member_id', $member->id)
            ->where('assigned_at', '>=', $start)
            ->get();
        $completed = $jobs->where('status', 'completed');

        $kpis = [
            'total_jobs' => $jobs->count(),
            'completed'  => $completed->count(),
            'avg_rating' => round($completed->avg('customer_rating') ?? 0, 2),
            'avg_travel' => round($completed->avg('actual_travel_time_minutes') ?? 0, 1),
            'avg_work'   => round($completed->avg('work_duration_minutes') ?? 0, 1),
        ];

        return view('business.analytics.member', compact('member', 'kpis', 'jobs'));
    }
}
