<?php
namespace App\Http\Controllers\Api\Teacher;
use App\Http\Controllers\Controller;
use App\Models\TeacherAssignment;
use App\Models\TeacherClasswork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class ClassworkController extends Controller {
 private function own(Request $r){return TeacherClasswork::where('school_id',$r->user()->school_id)->where('teacher_user_id',$r->user()->id);}
 private function assigned(Request $r){return TeacherAssignment::where('school_id',$r->user()->school_id)->where('teacher_user_id',$r->user()->id)->where('status',1);}
 private function ok($data,$status=200){return response()->json(['success'=>true,'data'=>$data],$status);}
 public function assignments(Request $r){return $this->ok($this->assigned($r)->with(['schoolClass','section','subject'])->get());}
 public function index(Request $r){$d=$r->validate(['type'=>'nullable|in:classwork,homework,assignment','status'=>'nullable|in:pending,approved,rejected','date'=>'nullable|date_format:Y-m-d']);$q=$this->own($r)->with(['assignment.schoolClass','assignment.section','assignment.subject']);foreach(['type'=>'type','status'=>'approval_status','date'=>'work_date'] as $key=>$col)if(!empty($d[$key]))$q->where($col,$d[$key]);return $this->ok($q->orderByDesc('work_date')->orderByDesc('id')->paginate(25));}
 public function show(Request $r,$id){return $this->ok($this->own($r)->with(['assignment.schoolClass','assignment.section','assignment.subject'])->findOrFail($id));}
 private function validateWork(Request $r){return $r->validate(['teacher_assignment_id'=>['required','integer',Rule::exists('teacher_assignments','id')->where('school_id',$r->user()->school_id)->where('teacher_user_id',$r->user()->id)->where('status',1)],'type'=>'required|in:classwork,homework,assignment','title'=>'required|string|max:200','description'=>'required|string|max:10000','work_date'=>'required|date_format:Y-m-d','due_date'=>'nullable|date_format:Y-m-d|after_or_equal:work_date']);}
 public function store(Request $r){$d=$this->validateWork($r);$item=TeacherClasswork::create($d+['school_id'=>$r->user()->school_id,'teacher_user_id'=>$r->user()->id,'approval_status'=>'approved']);return $this->ok($item,201);}
 public function update(Request $r,$id){$d=$this->validateWork($r);$item=DB::transaction(function()use($r,$id,$d){$item=$this->own($r)->lockForUpdate()->findOrFail($id);abort_unless(in_array($item->approval_status,['pending','approved'],true),409,'Rejected work cannot be edited.');abort_unless((int)$item->teacher_assignment_id===(int)$d['teacher_assignment_id'],422,'Assignment cannot be changed.');$item->update($d+['approval_status'=>'approved']);return $item->fresh();});return $this->ok($item);}
}
