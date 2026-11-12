<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    private function superAdmin(): bool { return auth()->user()->hasRole('Super Admin'); }
    private function scoped() {
        $q=User::query();
        if (!$this->superAdmin()) $q->where('school_id',auth()->user()->school_id);
        return $q;
    }
    private function target(User $user): void {
        abort_unless($this->superAdmin() || ($user->school_id && $user->school_id===auth()->user()->school_id && !$user->hasAnyRole(['Super Admin','Admin'])),403);
    }
    private function roles() {
        return Role::where('guard_name','web')
            ->when(!$this->superAdmin(),fn($q)=>$q->whereNotIn('name',['Super Admin','Admin']))
            ->orderBy('name')->get();
    }
    public function index() {
        $users=$this->scoped()->with(['roles','school'])->orderByDesc('id')->paginate(20);
        return view('access.users.index',compact('users'));
    }
    public function create() {
        return view('access.users.form',['user'=>null,'roles'=>$this->roles(),'schools'=>$this->superAdmin()?School::orderBy('school_name')->get():collect(),'selected'=>[]]);
    }
    public function store(Request $request) {
        $data=$this->validateUser($request);
        $roles=$data['roles'];unset($data['roles']);
        $data['school_id']=$this->superAdmin()?($data['school_id']??null):auth()->user()->school_id;
        $data['password']=Hash::make($data['password']);
        $user=User::create($data);
        $user->syncRoles($roles);
        return redirect()->route('access.users.index')->with('success','User created.');
    }
    public function edit(User $user) {
        $this->target($user);
        return view('access.users.form',['user'=>$user,'roles'=>$this->roles(),'schools'=>$this->superAdmin()?School::orderBy('school_name')->get():collect(),'selected'=>$user->roles->pluck('name')->all()]);
    }
    public function update(Request $request, User $user) {
        $this->target($user);
        $data=$this->validateUser($request,$user);
        $roles=$data['roles'];unset($data['roles']);
        if (!$this->superAdmin()) $data['school_id']=auth()->user()->school_id;
        if (empty($data['password'])) unset($data['password']);
        else $data['password']=Hash::make($data['password']);
        if ($user->is(auth()->user()) && empty($data['status'])) return back()->withErrors(['status'=>'You cannot deactivate your own account.'])->withInput();
        if ($user->hasRole('Super Admin') && !in_array('Super Admin',$roles,true)) {
            abort_if(User::role('Super Admin')->count()<=1,422,'Cannot remove the last Super Admin.');
        }
        $user->update($data);
        $user->syncRoles($roles);
        return redirect()->route('access.users.index')->with('success','User updated.');
    }
    private function validateUser(Request $request, ?User $user=null): array {
        $allowed=$this->roles()->pluck('name')->all();
        // Existing Super Admin role is preserved only when a Super Admin edits a user.
        $data=$request->validate([
            'name'=>['required','string','max:255'],
            'email'=>['required','email','max:255',Rule::unique('users','email')->ignore($user?->id)],
            'password'=>[$user?'nullable':'required','string','min:8','confirmed'],
            'status'=>['required','boolean'],
            'school_id'=>[$this->superAdmin()?'nullable':'prohibited','nullable','integer',Rule::exists('schools','id')],
            'roles'=>['required','array','min:1'],
            'roles.*'=>['required','string',Rule::in($allowed)],
        ]);
        return $data;
    }
}
