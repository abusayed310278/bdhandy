<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Mail\TeamMemberCredentialsMail;
use App\Mail\TeamMemberInviteMail;
use App\Models\Currency;
use App\Models\TeamCompensation;
use App\Models\TeamMember;
use App\Models\TeamRole;
use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class TeamMemberController extends Controller
{
    private function profile()
    {
        return Auth::user()->providerProfile;
    }

    public function index()
    {
        $profile = $this->profile();
        $members = TeamMember::with(['role', 'services.service', 'activeVehicle.vehicle'])
            ->where('business_profile_id', $profile->id)
            ->where('status', '!=', 'terminated')
            ->orderBy('full_name')
            ->paginate(20);

        $terminatedCount = TeamMember::where('business_profile_id', $profile->id)
            ->where('status', 'terminated')
            ->count();

        return view('business.team.index', compact('members', 'profile', 'terminatedCount'));
    }

    public function terminated()
    {
        $profile = $this->profile();
        $members = TeamMember::with(['role', 'services.service'])
            ->where('business_profile_id', $profile->id)
            ->where('status', 'terminated')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('business.team.terminated', compact('members', 'profile'));
    }

    public function invite()
    {
        $profile   = $this->profile();
        $roles     = TeamRole::where('business_profile_id', $profile->id)->get();
        $services  = Service::whereHas('providerServices', fn($q) => $q->where('provider_profile_id', $profile->id))
            ->where('status', 'active')->get();
        $currencies = Currency::where('status', 'active')->get();

        return view('business.team.invite', compact('roles', 'services', 'profile', 'currencies'));
    }

    public function inviteStore(Request $request)
    {
        $profile = $this->profile();

        $request->validate([
            'full_name'               => 'required|string|max:255',
            'phone'                   => 'required|string|max:20|unique:team_members,phone|unique:users,phone',
            'email'                   => 'required|email|unique:team_members,email|unique:users,email',
            'designation'             => 'nullable|string|max:100',
            'joining_date'            => 'nullable|date',
            'renewal_date'            => 'nullable|date',
            'compensation_type'       => 'required|in:salary,commission,hybrid',
            'team_role_id'            => 'nullable|exists:team_roles,id',
            'invite_method'           => 'required|in:email_link,manual_password',
            'manual_password'         => 'required_if:invite_method,manual_password|nullable|string|min:8|max:50',
            'service_ids'             => 'nullable|array',
            'service_ids.*'           => 'exists:services,id',
            'skill_levels'            => 'nullable|array',
            'primary_service'         => 'nullable|integer',
            // NID
            'nid_number'              => 'nullable|string|max:50',
            'nid_photo'               => 'nullable|image|max:2048',
            // Passport
            'passport_number'         => 'nullable|string|max:50',
            'passport_photo'          => 'nullable|image|max:2048',
            // Compensation
            'base_salary_monthly'     => 'nullable|numeric|min:0',
            'salary_currency_id'      => 'nullable|exists:currencies,id',
            'commission_type'         => 'nullable|in:percentage,fixed_per_job,tiered',
            'commission_value'        => 'nullable|numeric|min:0',
            'commission_currency_id'  => 'nullable|exists:currencies,id',
            'weekly_guarantee_amount' => 'nullable|numeric|min:0',
            'payment_frequency'       => 'nullable|in:weekly,biweekly,monthly',
        ]);

        $code     = $this->generateEmployeeCode($profile);
        $method   = $request->invite_method;
        $password = $method === 'manual_password'
            ? $request->manual_password
            : Str::random(32); // random throwaway — they'll set their own via reset link

        $nidPhotoPath      = $request->hasFile('nid_photo')      ? $request->file('nid_photo')->store('team/nid', 'public')           : null;
        $passportPhotoPath = $request->hasFile('passport_photo') ? $request->file('passport_photo')->store('team/passport', 'public') : null;

        $member = DB::transaction(function () use ($profile, $request, $code, $password, $method, $nidPhotoPath, $passportPhotoPath) {
            $user = User::create([
                'name'              => $request->full_name,
                'email'             => $request->email,
                'phone'             => $request->phone,
                'password'          => Hash::make($password),
                'user_code'         => mt_rand(100000, 999999),
                'email_verified_at' => $method === 'manual_password' ? now() : null,
            ]);
            $user->assignRole('team_member');

            $member = TeamMember::create([
                'user_id'             => $user->id,
                'business_profile_id' => $profile->id,
                'team_role_id'        => $request->team_role_id,
                'full_name'           => $request->full_name,
                'phone'               => $request->phone,
                'email'               => $request->email,
                'designation'         => $request->designation,
                'joining_date'        => $request->joining_date,
                'renewal_date'        => $request->renewal_date,
                'compensation_type'   => $request->compensation_type,
                'employee_code'       => $code,
                'status'              => 'active',
                'nid_number'          => $request->nid_number,
                'nid_photo'           => $nidPhotoPath,
                'passport_number'     => $request->passport_number,
                'passport_photo'      => $passportPhotoPath,
            ]);

            // Save initial compensation record
            $defaultCurrency = Currency::where('status', 'active')->value('id');
            TeamCompensation::create([
                'team_member_id'          => $member->id,
                'effective_from'          => $request->joining_date ?? today(),
                'base_salary_monthly'     => $request->base_salary_monthly,
                'salary_currency_id'      => $request->salary_currency_id ?? $defaultCurrency,
                'commission_type'         => $request->commission_type,
                'commission_value'        => $request->commission_value,
                'commission_currency_id'  => $request->commission_currency_id ?? $defaultCurrency,
                'weekly_guarantee_amount' => $request->weekly_guarantee_amount,
                'payment_frequency'       => $request->payment_frequency ?? 'monthly',
            ]);

            return $member;
        });

        $this->syncServices($member, $request);

        $emailStatus = $this->sendInviteEmail($member, $method, $password);

        return redirect()->route('business.team.show', $member)
            ->with('success', "Team member {$member->full_name} added with code {$code}. {$emailStatus}");
    }

    private function sendInviteEmail(TeamMember $member, string $method, string $password): string
    {
        try {
            if ($method === 'manual_password') {
                Mail::to($member->email)->queue(new TeamMemberCredentialsMail(
                    member:   $member,
                    password: $password,
                    loginUrl: route('login'),
                ));
                return 'Login credentials emailed.';
            }

            // email_link mode → generate a reset-password token and email a branded invite
            $token    = Password::broker()->createToken($member->user);
            $setupUrl = url(route('password.reset', ['token' => $token, 'email' => $member->email], false));

            Mail::to($member->email)->queue(new TeamMemberInviteMail(
                member:   $member,
                setupUrl: $setupUrl,
            ));
            return 'Setup invite email sent.';
        } catch (\Throwable $e) {
            \Log::error('Team member invite email failed: ' . $e->getMessage(), ['member_id' => $member->id]);
            return 'Member created but email failed — please share credentials manually.';
        }
    }

    public function show(TeamMember $member)
    {
        $this->authorizeAccess($member);
        $member->load(['role', 'services.service', 'currentCompensation.salaryCurrency',
            'activeVehicle.vehicle', 'equipmentAssignments' => fn($q) => $q->where('status', 'assigned')->with('equipment')]);

        $recentAttendance = $member->attendance()->latest('clock_in_time')->take(10)->get();
        $recentJobs = $member->assignments()->with('request')->latest()->take(5)->get();

        // Get this member's vehicle assignment history
        $vehicleHistory = $member->vehicleAssignments()
            ->with(['vehicle', 'assignedBy'])
            ->orderByDesc('assigned_at')
            ->get();

        return view('business.team.show', compact('member', 'recentAttendance', 'recentJobs', 'vehicleHistory'));
    }

    public function edit(TeamMember $member)
    {
        $this->authorizeAccess($member);
        $profile  = $this->profile();
        $roles    = TeamRole::where('business_profile_id', $profile->id)->get();
        $services = Service::whereHas('providerServices', fn($q) => $q->where('provider_profile_id', $profile->id))
            ->where('status', 'active')->get();
        $memberServiceIds = $member->services->pluck('service_id', 'service_id')->toArray();
        $skillLevels      = $member->services->pluck('skill_level', 'service_id')->toArray();
        $primaryServiceId = $member->services->where('is_primary', true)->value('service_id');

        return view('business.team.edit', compact('member', 'roles', 'services', 'memberServiceIds', 'skillLevels', 'primaryServiceId'));
    }

    public function update(Request $request, TeamMember $member)
    {
        $this->authorizeAccess($member);

        $request->validate([
            'full_name'         => 'required|string|max:255',
            'phone'             => 'required|string|max:20|unique:team_members,phone,' . $member->id,
            'email'             => 'nullable|email|unique:team_members,email,' . $member->id,
            'designation'       => 'nullable|string|max:100',
            'joining_date'      => 'nullable|date',
            'renewal_date'      => 'nullable|date',
            'compensation_type' => 'required|in:salary,commission,hybrid',
            'team_role_id'      => 'nullable|exists:team_roles,id',
            'status'            => 'required|in:active,inactive,suspended,terminated',
            'service_ids'       => 'nullable|array',
            'service_ids.*'     => 'exists:services,id',
        ]);

        $member->update($request->only([
            'full_name', 'phone', 'email', 'designation',
            'joining_date', 'renewal_date', 'compensation_type', 'team_role_id', 'status',
        ]));

        $this->syncServices($member, $request);

        return redirect()->route('business.team.show', $member)->with('success', 'Member updated.');
    }

    public function terminate(TeamMember $member)
    {
        $this->authorizeAccess($member);

        $unassigned = $member->assignments()
            ->whereNotIn('status', ['completed', 'rejected', 'reassigned'])
            ->count();

        $member->assignments()
            ->whereNotIn('status', ['completed', 'rejected', 'reassigned'])
            ->update(['status' => 'reassigned']);

        $member->update(['status' => 'terminated', 'team_role_id' => null]);

        if ($member->user) {
            $member->user->removeRole('team_member');
        }

        $msg = "{$member->full_name} has been terminated.";
        if ($unassigned > 0) {
            $msg .= " {$unassigned} incomplete job(s) have been unassigned.";
        }

        return redirect()->route('business.team.index')->with('success', $msg);
    }

    public function assignRole(Request $request, TeamMember $member)
    {
        $this->authorizeAccess($member);
        $request->validate(['team_role_id' => 'nullable|exists:team_roles,id']);
        $member->update(['team_role_id' => $request->team_role_id]);

        return back()->with('success', 'Role assigned.');
    }

    private function syncServices(TeamMember $member, Request $request): void
    {
        $member->services()->delete();
        foreach ($request->input('service_ids', []) as $serviceId) {
            $member->services()->create([
                'service_id'          => $serviceId,
                'business_profile_id' => $member->business_profile_id,
                'skill_level'         => $request->input("skill_levels.{$serviceId}", 'mid'),
                'is_primary'          => ($request->input('primary_service') == $serviceId),
            ]);
        }
    }

    private function authorizeAccess(TeamMember $member): void
    {
        abort_unless($member->business_profile_id === $this->profile()->id, 403);
    }

    private function generateEmployeeCode($profile): string
    {
        $city    = strtoupper(substr(optional($profile->serviceAreas->first()?->area)->name ?? 'HQ', 0, 3));
        $count   = TeamMember::withTrashed()->where('business_profile_id', $profile->id)->count() + 1;
        return 'EMP-' . $city . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
