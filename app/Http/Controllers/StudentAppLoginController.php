<?php
namespace App\Http\Controllers;
use App\Models\StudentAppAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
class StudentAppLoginController extends Controller {
 private function school(Request $r):int { abort_unless($r->user(),401); abort_unless($r->user()->school_id,403); return (int)$r->user()->school_id; }
 private function authorizeManage(Request $r):void { abort_unless($r->user() && $r->user()->hasAnyRole(['Super Admin','Admin']),403); }
 private function students(Request $r)
{
    return DB::table('students')
        ->where('students.school_id', $this->school($r));
}
 private function filterData(Request $r):array {
  $school=$this->school($r);
  $years=DB::table('academic_years')->where('school_id',$school)->orderByDesc('id')->get();
  $classes=DB::table('school_classes')->where('school_id',$school)->orderBy('id')->get();
  $sections=DB::table('sections')->where('school_id',$school)->orderBy('id')->get();
  return compact('years','classes','sections');
 }
 private function filteredStudents(Request $r) {
  $q=$this->students($r)->whereExists(function($query) use($r) {
   $query->selectRaw('1')->from('student_enrollments as se')
    ->whereColumn('se.student_id','students.id')
    ->whereColumn('se.school_id','students.school_id')
    ->where('se.is_current',1)->where('se.status',1);
   if($r->filled('academic_year_id'))$query->where('se.academic_year_id',$r->integer('academic_year_id'));
   if($r->filled('school_class_id'))$query->where('se.school_class_id',$r->integer('school_class_id'));
   if($r->filled('section_id'))$query->where('se.section_id',$r->integer('section_id'));
  });
  if($r->filled('search')){$term=mb_substr((string)$r->input('search'),0,100);$q->where(fn($x)=>$x->where('students.student_name','like',"%{$term}%")->orWhere('students.admission_no','like',"%{$term}%"));}
  return $q;
 }
 public function index(Request $r)
{
    $this->authorizeManage($r);

    $students = $this->filteredStudents($r)
        ->leftJoin('student_app_accounts as a', function ($join) {
            $join->on('a.student_id', '=', 'students.id')
                 ->on('a.school_id', '=', 'students.school_id');
        })
        ->select(
            'students.id',
            'students.student_name',
            'students.admission_no',
            'students.status',
            'a.id as account_id',
            'a.is_active as account_active',
            'a.last_login_at'
        )
        ->orderBy('students.student_name')
        ->paginate(25)
        ->withQueryString();

    return view(
        'student-app-logins.index',
        array_merge(
            compact('students'),
            $this->filterData($r)
        )
    );
}
 public function generateFiltered(Request $r){
  $this->authorizeManage($r);
  $r->validate(['academic_year_id'=>'required|integer','school_class_id'=>'required|integer','section_id'=>'required|integer']);
  $ids=$this->filteredStudents($r)->where('students.status',1)->where('se.enrollment_status', 'active')->whereNotExists(function($q){$q->selectRaw('1')->from('student_app_accounts as a')->whereColumn('a.student_id','students.id');})->limit(501)->pluck('students.id')->all();
  if(count($ids)>500)throw ValidationException::withMessages(['students'=>'More than 500 accounts match. Please generate in smaller batches.']);
  if(!$ids) return back()->with('success','No eligible students without app accounts in this selection.');
  $r->merge(['student_ids'=>$ids]);
  return $this->generate($r);
 }
 private function generatePassword():string {return Str::password(12,letters:true,numbers:true,symbols:false,spaces:false);}
 private function ensureUniqueAdmissionNumbers(array $ids, int $school):void {
 $duplicates=DB::table('students')->whereIn('id',$ids)->where('school_id',$school)->select('admission_no')->groupBy('admission_no')->havingRaw('COUNT(*) > 1')->pluck('admission_no');
 if($duplicates->isNotEmpty())throw ValidationException::withMessages(['students'=>'Duplicate admission numbers found: '.$duplicates->take(5)->implode(', ')]);
 }
 public function generate(Request $r){$this->authorizeManage($r);$school=$this->school($r);$v=$r->validate(['student_ids'=>'required|array|min:1|max:500','student_ids.*'=>'required|integer|distinct']);$ids=$v['student_ids'];
 $rows=$this->students($r)->whereIn('id',$ids)->get(['id','admission_no','student_name','status']);
 if($rows->count()!==count($ids))throw ValidationException::withMessages(['student_ids'=>'One or more students do not belong to this school.']);
 $this->ensureUniqueAdmissionNumbers($ids,$school);
 $credentials=[];
 DB::transaction(function()use($rows,$school,&$credentials){foreach($rows as $s){
 if(!$s->status)continue;
 if(StudentAppAccount::where('student_id',$s->id)->exists())continue;
 $password=$this->generatePassword();
 StudentAppAccount::create(['school_id'=>$school,'student_id'=>$s->id,'login_id'=>$s->admission_no,'password'=>$password,'is_active'=>true,'must_change_password'=>true]);
 $credentials[]=['student_name'=>$s->student_name,'login_id'=>$s->admission_no,'password'=>$password];
 }});
 return $this->credentialDownload($credentials);
 }
 public function reset(Request $r,int $student){$this->authorizeManage($r);$s=$this->students($r)->where('id',$student)->first();abort_unless($s,404);
 $a=StudentAppAccount::where('student_id',$student)->where('school_id',$this->school($r))->firstOrFail();$password=$this->generatePassword();$a->update(['password'=>$password,'must_change_password'=>true]);$a->tokens()->delete();
 return $this->credentialDownload([['student_name'=>$s->student_name,'login_id'=>$a->login_id,'password'=>$password]]);
 }
 public function toggle(Request $r,int $student){$this->authorizeManage($r);$this->students($r)->where('id',$student)->first() ?? abort(404);$a=StudentAppAccount::where('student_id',$student)->where('school_id',$this->school($r))->firstOrFail();$a->update(['is_active'=>!$a->is_active]);if(!$a->is_active)$a->tokens()->delete();return back()->with('success','Student app account status updated.');}
 private function credentialDownload(array $credentials){$f=fopen('php://temp','w+');fputcsv($f,['Student Name','Login ID','Temporary Password']);foreach($credentials as $row)fputcsv($f,$row);rewind($f);$csv=stream_get_contents($f);fclose($f);return response($csv,200,['Content-Type'=>'text/csv; charset=UTF-8','Content-Disposition'=>'attachment; filename="student-app-credentials.csv"','Cache-Control'=>'no-store, private','Pragma'=>'no-cache']);}
}
