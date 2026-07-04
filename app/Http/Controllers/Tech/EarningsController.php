<?php
namespace App\Http\Controllers\Tech;

use App\Http\Controllers\Controller;
use App\Models\TeamJobAssignment;
use Illuminate\Support\Facades\Auth;

class EarningsController extends Controller
{
    private function member()
    {
        $m = Auth::user()->teamMember;
        abort_unless($m, 403, 'Not linked to a team member.');
        return $m;
    }

    public function index()
    {
        return $this->period('month');
    }

    public function period(string $period)
    {
        $member = $this->member();
        abort_unless($member->hasTeamPermission('earnings', 'view_own_earnings'), 403, 'No permission to view earnings.');
        $start  = match($period) {
            'today' => today(),
            'week'  => now()->startOfWeek(),
            'year'  => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $jobs = TeamJobAssignment::where('team_member_id', $member->id)
            ->where('status', 'completed')
            ->where('completed_at', '>=', $start)
            ->orderByDesc('completed_at')
            ->paginate(20);

        $total = TeamJobAssignment::where('team_member_id', $member->id)
            ->where('status', 'completed')
            ->where('completed_at', '>=', $start)
            ->sum('commission_earned') ?? 0;

        return view('tech.earnings.index', compact('jobs', 'total', 'period', 'start'));
    }
}
