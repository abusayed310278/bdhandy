<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\TeamRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamRoleController extends Controller
{
    private function profile() { return Auth::user()->providerProfile; }

    public function index()
    {
        $profile = $this->profile();
        $roles   = TeamRole::withCount('members')
            ->where('business_profile_id', $profile->id)
            ->orderBy('role_name')
            ->get();

        return view('business.team.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('business.team.roles.form', ['role' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name'   => 'required|string|max:100',
            'permissions' => 'required|array',
            'is_default'  => 'boolean',
        ]);

        $profile = $this->profile();

        TeamRole::create([
            'business_profile_id' => $profile->id,
            'role_name'           => $request->role_name,
            'permissions'         => $request->permissions,
            'is_default'          => $request->boolean('is_default'),
        ]);

        return redirect()->route('business.team.roles.index')->with('success', 'Role created.');
    }

    public function edit(TeamRole $role)
    {
        $this->authorizeAccess($role);
        return view('business.team.roles.form', compact('role'));
    }

    public function update(Request $request, TeamRole $role)
    {
        $this->authorizeAccess($role);
        $request->validate([
            'role_name'   => 'required|string|max:100',
            'permissions' => 'required|array',
            'is_default'  => 'boolean',
        ]);

        $role->update([
            'role_name'   => $request->role_name,
            'permissions' => $request->permissions,
            'is_default'  => $request->boolean('is_default'),
        ]);

        return redirect()->route('business.team.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(TeamRole $role)
    {
        $this->authorizeAccess($role);
        abort_if($role->members()->exists(), 422, 'Cannot delete a role that has members assigned.');
        $role->delete();

        return redirect()->route('business.team.roles.index')->with('success', 'Role deleted.');
    }

    private function authorizeAccess(TeamRole $role): void
    {
        abort_unless($role->business_profile_id === $this->profile()->id, 403);
    }
}
