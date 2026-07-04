<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\DayOfWeek;
use App\Models\Holiday;
use App\Models\ProviderBusinessHour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HoursHolidayController extends Controller
{
    private function profile()
    {
        return Auth::user()->providerProfile;
    }

    public function index(): View
    {
        $profile  = $this->profile();
        $days     = DayOfWeek::orderBy('id')->get();

        // Build a keyed array: day_of_week_id => ProviderBusinessHour
        $existing = ProviderBusinessHour::where('provider_profile_id', $profile->id)
            ->get()
            ->keyBy('day_of_week_id');

        $holidays = Holiday::where('provider_profile_id', $profile->id)
            ->orderBy('date_of_holiday')
            ->get();

        return view('provider.hours.index', compact('days', 'existing', 'holidays'));
    }

    public function updateHours(Request $request): RedirectResponse
    {
        $profile = $this->profile();
        $days    = DayOfWeek::orderBy('id')->get();

        foreach ($days as $day) {
            $isClosed  = $request->boolean("hours.{$day->id}.is_closed");
            $startTime = $request->input("hours.{$day->id}.start_time");
            $endTime   = $request->input("hours.{$day->id}.end_time");

            ProviderBusinessHour::updateOrCreate(
                ['provider_profile_id' => $profile->id, 'day_of_week_id' => $day->id],
                [
                    'is_closed'  => $isClosed,
                    'start_time' => $isClosed ? null : $startTime,
                    'end_time'   => $isClosed ? null : $endTime,
                ]
            );
        }

        return back()->with('success', 'Business hours updated.');
    }

    public function storeHoliday(Request $request): RedirectResponse
    {
        $request->validate([
            'date_of_holiday' => ['required', 'date'],
            'reason'          => ['nullable', 'string', 'max:255'],
        ]);

        Holiday::create([
            'provider_profile_id' => $this->profile()->id,
            'date_of_holiday'     => $request->date_of_holiday,
            'reason'              => $request->reason,
        ]);

        return back()->with('success', 'Holiday added.');
    }

    public function destroyHoliday(Holiday $holiday): RedirectResponse
    {
        if ($holiday->provider_profile_id !== $this->profile()->id) {
            abort(403);
        }

        $holiday->delete();

        return back()->with('success', 'Holiday removed.');
    }
}
