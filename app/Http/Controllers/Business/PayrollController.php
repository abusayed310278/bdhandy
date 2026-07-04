<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\TeamAttendance;
use App\Models\TeamJobAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    private function profile() { return Auth::user()->providerProfile; }

    public function index(Request $request)
    {
        $profile = $this->profile();
        $month   = $request->month ?? now()->format('Y-m');
        $start   = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $end     = $start->copy()->endOfMonth();

        $rows = TeamMember::with('currentCompensation.salaryCurrency')
            ->where('business_profile_id', $profile->id)
            ->where('status', 'active')
            ->get()
            ->map(fn($m) => $this->computeRow($m, $start, $end));

        return view('business.payroll.index', compact('rows', 'month', 'start', 'end'));
    }

    public function calculate(Request $request)
    {
        return $this->index($request);
    }

    public function process(Request $request)
    {
        // Mark next_payout_date forward; in a future build, write to a payouts table.
        $profile = $this->profile();
        $request->validate(['month' => 'required|date_format:Y-m']);

        TeamMember::where('business_profile_id', $profile->id)
            ->where('status', 'active')
            ->get()
            ->each(function ($m) use ($request) {
                $comp = $m->currentCompensation;
                if ($comp) {
                    $comp->update(['next_payout_date' => \Carbon\Carbon::parse($request->month . '-01')->addMonth()]);
                }
            });

        return back()->with('success', 'Payroll for ' . $request->month . ' processed.');
    }

    public function reports(Request $request)
    {
        return $this->index($request);
    }

    public function export(Request $request)
    {
        $profile = $this->profile();
        $month   = $request->month ?? now()->format('Y-m');
        $start   = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $end     = $start->copy()->endOfMonth();

        $rows = TeamMember::with('currentCompensation')
            ->where('business_profile_id', $profile->id)
            ->where('status', 'active')
            ->get()
            ->map(fn($m) => $this->computeRow($m, $start, $end));

        $filename = "payroll-{$month}.csv";
        $handle   = fopen('php://memory', 'w+');
        fputcsv($handle, ['Employee Code', 'Name', 'Type', 'Hours', 'Jobs Completed', 'Total Pay']);
        foreach ($rows as $r) {
            fputcsv($handle, [$r['code'], $r['name'], $r['type'], $r['hours'], $r['jobs'], $r['total']]);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    private function computeRow(TeamMember $member, $start, $end): array
    {
        $hours = TeamAttendance::where('team_member_id', $member->id)
            ->whereBetween('clock_in_time', [$start, $end])
            ->sum('total_hours') ?? 0;

        $completed = TeamJobAssignment::where('team_member_id', $member->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end])
            ->count();

        $commission = TeamJobAssignment::where('team_member_id', $member->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end])
            ->sum('commission_earned') ?? 0;

        $comp     = $member->currentCompensation;
        $salary   = ($member->compensation_type === 'salary' || $member->compensation_type === 'hybrid') ? ($comp?->base_salary_monthly ?? 0) : 0;
        $total    = $salary + ($member->compensation_type !== 'salary' ? $commission : 0);
        $currency = $comp?->salaryCurrency?->symbol ?? '৳';

        return [
            'id'       => $member->id,
            'name'     => $member->full_name,
            'code'     => $member->employee_code,
            'type'     => $member->compensation_type,
            'hours'    => $hours,
            'jobs'     => $completed,
            'salary'   => $salary,
            'commission' => $commission,
            'total'    => $total,
            'currency' => $currency,
        ];
    }
}
