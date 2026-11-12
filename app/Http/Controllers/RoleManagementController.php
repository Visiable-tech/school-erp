<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleManagementController extends Controller
{
    private function superAdmin(): bool { return auth()->user()->hasRole('Super Admin'); }
    private function allowedRole(Role $role): void {
        abort_if($role->name === 'Super Admin', 403);
    }
    private function permissions() {
        return Permission::where('guard_name', 'web')->orderBy('name')->get()->groupBy(function ($p) {
            return str_contains($p->name, '.') ? explode('.', $p->name)[0] : 'Other';
        });
    }
    public function index() {
        $roles = Role::withCount(['permissions','users'])->orderBy('name')->get();
        return view('access.roles.index', compact('roles'));
    }
    public function create() {
        $permissions = $this->permissions();
        return view('access.roles.form', ['role'=>null,'permissions'=>$permissions,'selected'=>[]]);
    }
    public function store(Request $request) {
        $data=$request->validate([
            'name'=>['required','string','max:125',Rule::unique('roles','name')->where('guard_name','web')],
            'permissions'=>['array'], 'permissions.*'=>['string',Rule::exists('permissions','name')->where('guard_name','web')],
        ]);
        abort_if($data['name']==='Super Admin',403);
        $role=Role::create(['name'=>$data['name'],'guard_name'=>'web']);
        $role->syncPermissions($data['permissions']??[]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        return redirect()->route('access.roles.index')->with('success','Role created.');
    }
    public function edit(Role $role) {
        $this->allowedRole($role);
        $permissions=$this->permissions();
        $selected=$role->permissions->pluck('name')->all();
        return view('access.roles.form',compact('role','permissions','selected'));
    }
    public function update(Request $request, Role $role) {
        $this->allowedRole($role);
        $data=$request->validate([
            'name'=>['required','string','max:125',Rule::unique('roles','name')->where('guard_name','web')->ignore($role->id)],
            'permissions'=>['array'], 'permissions.*'=>['string',Rule::exists('permissions','name')->where('guard_name','web')],
        ]);
        abort_if($data['name']==='Super Admin',403);
        $role->update(['name'=>$data['name']]);
        $role->syncPermissions($data['permissions']??[]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        return redirect()->route('access.roles.index')->with('success','Role updated.');
    }
    public function destroy(Role $role) {
        $this->allowedRole($role);
        abort_if($role->users()->exists(),422,'Remove users from this role before deleting it.');
        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        return back()->with('success','Role deleted.');
    }
}
