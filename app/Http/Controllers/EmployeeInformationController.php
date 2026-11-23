<?php
namespace App\Http\Controllers;
use App\Models\ErpEmployee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use App\Models\ErpEmployeeEvent;
use App\Models\ErpEmployeeCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EmployeeInformationController extends Controller {
 private function school(): int
{
    $user = auth()->user();

    if (!$user) {
        abort(401, 'Please login to access Employee Information.');
    }

    if (!$user->school_id) {
        abort(403, 'No school is assigned to this account.');
    }

    return (int) $user->school_id;
}
 private function employeeCodePrefix(): string {
    $school = DB::table('schools')->where('id', $this->school())->first();
    abort_unless($school, 403, 'School not found.');
    $code = Schema::hasColumn('schools', 'code') ? trim((string) ($school->code ?? '')) : '';
    if ($code === '') {
        $name = (string) ($school->school_name ?? $school->name ?? '');
        $code = implode('', array_map(fn($word) => strtoupper(substr($word, 0, 1)), preg_split('/\s+/', trim($name)) ?: []));
    }
    $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));
    abort_if($code === '', 422, 'Please configure the school code.');
    return $code;
 }
 private function employeeCodePreview(): string {
    $year = (int) now()->format('Y');
    $last = (int) (DB::table('employee_code_sequences')
        ->where('school_id', $this->school())->where('year', $year)->value('last_number') ?? 0);
    $prefix = $this->employeeCodePrefix().'/'.$year.'/';
    $existing = DB::table('erp_employees')->where('school_id', $this->school())
        ->where('employee_code', 'like', $prefix.'%')->pluck('employee_code');
    foreach ($existing as $code) {
        if (preg_match('/^'.preg_quote($prefix, '/').'([0-9]+)$/', $code, $match)) {
            $last = max($last, (int) $match[1]);
        }
    }
    return $prefix.str_pad((string)($last + 1), 4, '0', STR_PAD_LEFT);
 }
 private function nextEmployeeCode(): string {
    $schoolId = $this->school();
    $year = (int) now()->format('Y');
    DB::table('employee_code_sequences')->insertOrIgnore([
        'school_id' => $schoolId, 'year' => $year, 'last_number' => 0,
        'created_at' => now(), 'updated_at' => now(),
    ]);
    $sequence = DB::table('employee_code_sequences')
        ->where('school_id', $schoolId)->where('year', $year)->lockForUpdate()->first();
    $number = (int) $sequence->last_number;
    $prefix = $this->employeeCodePrefix().'/'.$year.'/';
    // Also account for employee IDs created before the sequence table was installed.
    $existing = DB::table('erp_employees')->where('school_id', $schoolId)
        ->where('employee_code', 'like', $prefix.'%')->pluck('employee_code');
    foreach ($existing as $code) {
        if (preg_match('/^'.preg_quote($prefix, '/').'([0-9]+)$/', $code, $match)) {
            $number = max($number, (int) $match[1]);
        }
    }
    $number++;
    DB::table('employee_code_sequences')->where('school_id', $schoolId)
        ->where('year', $year)->update(['last_number' => $number, 'updated_at' => now()]);
    return $prefix.str_pad((string)$number, 4, '0', STR_PAD_LEFT);
 }
 private function employees(){return ErpEmployee::where('school_id',$this->school());}
 private function events(){return ErpEmployeeEvent::where('school_id',$this->school());}
 private function employee(int $id): ErpEmployee {return $this->employees()->findOrFail($id);}
 private function types(): array {return ['relieved'=>'Relieved Employees','suspension'=>'Employee Suspension','resignation'=>'Resignation','holiday'=>'Employee Holidays','extra_duty'=>'Extra Duties','remark'=>'Employee Remarks','mapping'=>'Employee Organization Mapping'];}
 public function dashboard(Request $request){
  $month=$request->input('month',now()->format('Y-m'));
  if(!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/',$month)) $month=now()->format('Y-m');
  $date=Carbon::createFromFormat('!Y-m',$month);
  $q=$this->employees();$total=(clone $q)->count();
  $new=(clone $q)->whereBetween('joining_date',[$date->copy()->startOfMonth()->toDateString(),$date->copy()->endOfMonth()->toDateString()])->count();
  $male=(clone $q)->whereRaw('LOWER(gender)=?', ['male'])->count();$female=(clone $q)->whereRaw('LOWER(gender)=?', ['female'])->count();
  $birthdays=(clone $q)->whereNotNull('dob')->pluck('dob');$avgAge=$birthdays->count()?round($birthdays->avg(fn($d)=>Carbon::parse($d)->diffInMonths(now())/12),1):null;
  $departments=(clone $q)->select('department','gender',DB::raw('COUNT(*) as total'))->groupBy('department','gender')->get();
  $service=(clone $q)->whereNotNull('joining_date')->get(['name','department','joining_date'])->map(fn($e)=>['name'=>$e->name,'department'=>$e->department?:'Unassigned','months'=>Carbon::parse($e->joining_date)->diffInMonths(now())])->sortByDesc('months')->take(8)->values();
  $joining=$this->employees()->whereBetween('joining_date',[$date->copy()->startOfMonth(),$date->copy()->endOfMonth()])->orderByDesc('joining_date')->limit(10)->get();
  $relieved=$this->events()->with('employee')->where('type','relieved')->whereBetween('event_date',[$date->copy()->startOfMonth(),$date->copy()->endOfMonth()])->latest('event_date')->limit(10)->get();
  $expiring=ErpEmployeeCertificate::where('school_id',$this->school())->with('employee')->whereBetween('expiry_date',[today(),today()->addDays(60)])->orderBy('expiry_date')->limit(10)->get();
  return view('employee-information.dashboard',compact('month','total','new','male','female','avgAge','departments','service','joining','relieved','expiring'));
 }
 public function index(Request $request){$query=$this->employees();if($request->filled('search')){$s=$request->search;$query->where(fn($q)=>$q->where('name','like',"%{$s}%")->orWhere('employee_code','like',"%{$s}%"));}if($request->filled('status'))$query->where('status',$request->status);$employees=$query->orderBy('name')->paginate(20)->withQueryString();return view('employee-information.index',compact('employees'));}
 private function canManageLogins(): bool
{
    $user = auth()->user();

    if (!$user) {
        return false;
    }

    return $user->hasAnyRole(['Super Admin', 'Admin']);
}
 private function allowedRoles() {
    $super = auth()->user()?->hasRole('Super Admin') ?? false;
    return Role::where('guard_name', 'web')
        ->when(!$super, fn($q) => $q->whereNotIn('name', ['Super Admin', 'Admin']))
        ->orderBy('name')->get();
 }
 private function loginFormData(?ErpEmployee $employee = null): array {
    $canManageLogins = $this->canManageLogins();
    $super = auth()->user()?->hasRole('Super Admin') ?? false;
    $availableUsers = collect();
    if ($canManageLogins) {
        // Avoid depending on an unconfirmed User::employeeProfile() relationship.
        $availableUsers = User::where('school_id', $this->school())
            ->whereNotIn('id', ErpEmployee::whereNotNull('user_id')
                ->when($employee?->exists, fn($q) => $q->where('id', '!=', $employee->id))
                ->select('user_id'))
            ->when(!$super, fn($q) => $q->whereDoesntHave('roles',
                fn($r) => $r->whereIn('name', ['Super Admin', 'Admin'])))
            ->orderBy('name')->get(['id', 'name', 'email']);
    }
    $loginRoles = $this->allowedRoles();
    $designations = DB::table('hr_master_options')
        ->where('school_id', $this->school())->where('category', 'designations')
        ->where(function($q) use ($employee) {
            $q->where('is_active', 1);
            if ($employee?->designation) $q->orWhere('name', $employee->designation);
        })->orderBy('name')->get(['id', 'name']);
    return compact('canManageLogins', 'availableUsers', 'loginRoles', 'designations');
 }
 public function create(){return view('employee-information.form',['employee'=>new ErpEmployee(), 'previewEmployeeCode'=>$this->employeeCodePreview()] + $this->loginFormData());}
 public function edit(int $id){$employee=$this->employee($id);return view('employee-information.form',['employee'=>$employee] + $this->loginFormData($employee));}
 private function employeeData(Request $request, ?int $id = null): array {
    $data = $request->validate([
      // Employee code is generated server-side; never trust submitted values.
      // On update, preserve the employee's existing code.
            'salutation' => 'nullable|in:Mr,Mrs,Ms,Dr',
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:Male,Female,Other',
            'dob' => 'nullable|date',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'marital_status' => 'nullable|in:Single,Married,Divorced,Widowed',
            'place_of_birth' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'passport_expiry_date' => 'nullable|date',
            'official_email' => 'nullable|email|max:255',
            'user_type' => ['nullable','string','max:255'],
            'designation' => ['required','string','max:255'],
            'department' => 'nullable|string|max:255',
            'employment_type' => 'nullable|in:Permanent,Contract,Part-Time,Temporary',
            'joining_date' => 'required|date',
            'rejoin' => 'sometimes|boolean',
            'staff_category' => 'nullable|string|max:255',
            'probation_ends_on' => 'nullable|date',
            'reporting_authority' => 'nullable|string|max:255',
            'confirmation_date' => 'nullable|date',
            'create_user_login' => 'sometimes|boolean',
            'cost_center' => 'nullable|string|max:255',
            'pay_scale' => 'nullable|string|max:255',
            'is_principal' => 'sometimes|boolean',
            'coordinating_sections' => 'nullable|string|max:255',
            'coordinating_classes' => 'nullable|string|max:255',
            'salary_mode' => 'nullable|in:Bank,Cash,Cheque',
            'qualifications' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'specialization_subjects' => 'nullable|string|max:255',
            'experience_teaching_years' => 'nullable|integer|min:0|max:100',
            'experience_admin_years' => 'nullable|integer|min:0|max:100',
            'default_attendance_status' => 'nullable|in:Present,Absent,Leave',
            'previous_employee_code' => 'nullable|string|max:255',
            'increment_month' => 'nullable|in:January,February,March,April,May,June,July,August,September,October,November,December',
            'date_of_separation' => 'nullable|date',
            'status' => 'required|in:active,suspended,relieved,resigned',
            'phone' => 'nullable|string|max:255',
            'alternate_phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address_from_date' => 'nullable|date',
            'address' => 'nullable|string|max:3000',
            'correspondence_country' => 'nullable|string|max:255',
            'correspondence_state' => 'nullable|string|max:255',
            'correspondence_city' => 'nullable|string|max:255',
            'correspondence_pincode' => 'nullable|string|max:255',
            'permanent_address' => 'nullable|string|max:3000',
            'same_as_correspondence' => 'sometimes|boolean',
            'permanent_country' => 'nullable|string|max:255',
            'permanent_state' => 'nullable|string|max:255',
            'permanent_city' => 'nullable|string|max:255',
            'permanent_pincode' => 'nullable|string|max:255',
            'father_salutation' => 'nullable|in:Mr,Dr',
            'father_name' => 'nullable|string|max:255',
            'father_contact' => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'mother_salutation' => 'nullable|in:Mrs,Ms,Dr',
            'mother_name' => 'nullable|string|max:255',
            'mother_contact' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'bank_from_date' => 'nullable|date',
            'pf_no' => 'nullable|string|max:255',
            'esi_no' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_no' => 'nullable|string|max:255',
            'bank_other_org_accounts' => 'sometimes|boolean',
            'ifsc_code' => 'nullable|string|max:255',
            'pf_activation_date' => 'nullable|date',
            'pf_appear_date' => 'nullable|date',
            'uan_no' => 'nullable|string|max:255',
            'exclude_ptax' => 'sometimes|boolean',
            'eps_no' => 'nullable|string|max:255',
            'pan_no' => 'nullable|string|max:255',
            'aadhaar_no' => 'nullable|string|max:255',
            'voter_id_no' => 'nullable|string|max:255',
            'punch_card_no' => 'nullable|string|max:255',
            'skip_salary_generation' => 'sometimes|boolean',
            'salary_slip_format' => 'nullable|string|max:255',
            'skip_salary_months' => 'nullable|string|max:255',
            'exclude_from_substitution' => 'sometimes|boolean',
            'personal_meeting_link' => 'nullable|string|max:255',
            'allow_marking_locked_attendance' => 'sometimes|boolean',
            'emergency_contact' => 'nullable|string|max:255',
      'image_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
      'signature_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);
    // HR designations are school-specific master options, not arbitrary text.
    $designationExists = DB::table('hr_master_options')
        ->where('school_id', $this->school())->where('category', 'designations')
        ->where('name', $data['designation'])
        ->where(function($q) use ($id) {
            $q->where('is_active', 1);
            if ($id) {
                $current = ErpEmployee::where('school_id', $this->school())->find($id);
                if ($current?->designation) $q->orWhere('name', $current->designation);
            }
        })->exists();
    if (!$designationExists) throw ValidationException::withMessages(['designation' => 'Select a valid HR designation.']);
    // Do not let a non-admin set or modify privileged role metadata.
    if ($this->canManageLogins()) {
        if (!empty($data['user_type']) && !$this->allowedRoles()->contains('name', $data['user_type'])) {
            throw ValidationException::withMessages(['user_type' => 'Select an authorized ERP role.']);
        }
    } else {
        $current = $id ? $this->employee($id) : null;
        if ($request->filled('user_type') && $request->input('user_type') !== ($current?->user_type)) abort(403);
        $data['user_type'] = $current?->user_type;
    }
    $data['exclude_ptax'] = $request->boolean('exclude_ptax');
    $data['rejoin'] = $request->boolean('rejoin');
    $data['exclude_from_substitution'] = $request->boolean('exclude_from_substitution');
    $data['create_user_login'] = false;
    $data['skip_salary_generation'] = $request->boolean('skip_salary_generation');
    $data['bank_other_org_accounts'] = $request->boolean('bank_other_org_accounts');
    $data['same_as_correspondence'] = $request->boolean('same_as_correspondence');
    $data['allow_marking_locked_attendance'] = $request->boolean('allow_marking_locked_attendance');
    $data['is_principal'] = $request->boolean('is_principal');
    $data['name'] = trim(implode(' ',array_filter([$data['first_name']??null,$data['middle_name']??null,$data['last_name']??null])));
    if ($data['name'] === '') { throw \Illuminate\Validation\ValidationException::withMessages(['first_name'=>'First name or last name is required.']); }
    foreach (['image_path','signature_path'] as $field) {
      unset($data[$field]);
      if ($request->hasFile($field)) {
        $data[$field] = $request->file($field)->store('employee-private/'.$this->school(), 'local');
      }
    }
    return $data;
 }
 private function validateLogin(Request $request, ?ErpEmployee $employee): array {
    if (!$this->canManageLogins()) {
        // Never accept account management input from unauthorized staff.
        abort_if($request->hasAny(['login_action','linked_user_id','login_username','login_password','login_password_confirmation','login_roles']),403);
        return ['action'=>'preserve'];
    }
    $action = $request->input('login_action', 'none');
    $rules = ['login_action'=>['required',Rule::in(['none','existing','new'])]];
    if ($action === 'existing') {
        $rules['linked_user_id']=['required','integer',Rule::exists('users','id')->where('school_id',$this->school())];
    } elseif ($action === 'new') {
        $rules['official_email'] = ['required', 'email', 'max:255', Rule::unique('users', 'email')];
        $rules['login_password']=['required','string','min:8','confirmed'];
        // The role comes from the employee's User Type / ERP Role field.
        $rules['user_type']=['required','string',Rule::in($this->allowedRoles()->pluck('name')->all())];
    }
    $validated=$request->validate($rules);
    if ($action === 'new' && $employee?->user_id) {
        throw ValidationException::withMessages(['login_action'=>'Unlink the current account before creating a different one.']);
    }
    if ($action === 'existing') {
        $user=User::where('school_id',$this->school())->findOrFail($validated['linked_user_id']);
        if (!auth()->user()->hasRole('Super Admin') && $user->hasAnyRole(['Super Admin','Admin'])) abort(403);
        if (ErpEmployee::where('user_id',$user->id)->when($employee && $employee->exists,fn($q)=>$q->where('id','!=',$employee->id))->exists()) {
            throw ValidationException::withMessages(['linked_user_id'=>'This user is already linked to another employee.']);
        }
    }
    return ['action'=>$action] + $validated;
 }
 private function applyLogin(ErpEmployee $employee, array $login): void {
    if ($login['action'] === 'preserve') return;
    if ($login['action'] === 'none') {
        $employee->update(['user_id'=>null,'create_user_login'=>false]);
        return; // Never delete an existing user account when unlinking.
    }
    if ($login['action'] === 'existing') {
        $employee->update(['user_id'=>$login['linked_user_id'],'create_user_login'=>true]);
        return; // Existing user's roles and password remain unchanged.
    }
    $user=User::create([
        'school_id'=>$this->school(), 'name'=>$employee->name,
        'username'=>'emp_'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(24)), 'email'=>strtolower(trim($employee->official_email)),
        'password'=>Hash::make($login['login_password']), 'status'=>true,
    ]);
    $user->syncRoles([$login['user_type']]);
    $employee->update(['user_id'=>$user->id,'create_user_login'=>true]);
 }
 public function store(Request $request){
    $data=$this->employeeData($request);
    $login=$this->validateLogin($request,null);
    $data['school_id']=$this->school();
    $data['create_user_login']=false;
    DB::transaction(function () use ($data,$login) {
        $data['employee_code'] = $this->nextEmployeeCode();
        $employee=ErpEmployee::create($data);
        $this->applyLogin($employee,$login);
    });
    return redirect()->route('employee-info.index')->with('success','Employee added.');
 }
 public function update(Request $request,int $id){
    $employee=$this->employee($id);
    $data=$this->employeeData($request,$id);
    $login=$this->validateLogin($request,$employee);
    unset($data['create_user_login']);
    DB::transaction(function () use ($employee,$data,$login) {
        $employee->update($data);
        $this->applyLogin($employee,$login);
    });
    return redirect()->route('employee-info.index')->with('success','Employee updated.');
 }
 public function destroy(int $id){$e=$this->employee($id);if($e->events()->exists()||$e->certificates()->exists())return back()->with('error','Remove linked records before deleting employee.');$e->delete();return back()->with('success','Employee deleted.');}
 public function eventIndex(Request $request,string $type){abort_unless(isset($this->types()[$type]),404);$events=$this->events()->where('type',$type)->with('employee')->latest('event_date')->paginate(20);$employees=$this->employees()->orderBy('name')->get(['id','name','employee_code']);$title=$this->types()[$type];return view('employee-information.events',compact('events','employees','title','type'));}
 public function eventStore(Request $request,string $type){abort_unless(isset($this->types()[$type]),404);$data=$request->validate(['employee_id'=>'required|integer','event_date'=>'required|date','end_date'=>'nullable|date|after_or_equal:event_date','title'=>'nullable|string|max:255','remarks'=>'nullable|string|max:5000']);$employee=$this->employee((int)$data['employee_id']);$data['school_id']=$this->school();$data['type']=$type;DB::transaction(function()use($data,$employee,$type){ErpEmployeeEvent::create($data);$status=['relieved'=>'relieved','suspension'=>'suspended','resignation'=>'resigned'][$type]??null;if($status)$employee->update(['status'=>$status]);});return back()->with('success','Record saved.');}
 public function eventDestroy(string $type,int $id){abort_unless(isset($this->types()[$type]),404);$this->events()->where('type',$type)->findOrFail($id)->delete();return back()->with('success','Record deleted. Employee status must be reviewed separately.');}
 public function certificates(){ $certificates=ErpEmployeeCertificate::where('school_id',$this->school())->with('employee')->latest()->paginate(20);$employees=$this->employees()->orderBy('name')->get(['id','name','employee_code']);return view('employee-information.certificates',compact('certificates','employees'));}
 public function certificateStore(Request $request){$data=$request->validate(['employee_id'=>'required|integer','title'=>'required|string|max:255','expiry_date'=>'nullable|date','file'=>'required|file|mimes:pdf,jpg,jpeg,png|max:5120']);$this->employee((int)$data['employee_id']);$path=$request->file('file')->store('employee-certificates/'.$this->school(),'public');ErpEmployeeCertificate::create(['school_id'=>$this->school(),'employee_id'=>$data['employee_id'],'title'=>$data['title'],'expiry_date'=>$data['expiry_date']??null,'file_path'=>$path]);return back()->with('success','Certificate uploaded.');}
 public function certificateDownload(int $id){$cert=ErpEmployeeCertificate::where('school_id',$this->school())->findOrFail($id);abort_unless(Storage::disk('public')->exists($cert->file_path),404);return Storage::disk('public')->download($cert->file_path);}
 public function certificateDestroy(int $id){$cert=ErpEmployeeCertificate::where('school_id',$this->school())->findOrFail($id);Storage::disk('public')->delete($cert->file_path);$cert->delete();return back()->with('success','Certificate deleted.');}
 public function reports(Request $request){$query=$this->employees();if($request->filled('department'))$query->where('department',$request->department);if($request->filled('status'))$query->where('status',$request->status);$employees=$query->orderBy('name')->get();$departments=$this->employees()->whereNotNull('department')->distinct()->orderBy('department')->pluck('department');return view('employee-information.reports',compact('employees','departments'));}
}
