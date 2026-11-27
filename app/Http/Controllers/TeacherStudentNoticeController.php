<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
class TeacherStudentNoticeController extends Controller {
 private function school($r){abort_unless($r->user()->school_id,403);return $r->user()->school_id;}
 private function scope($r){return DB::table('student_notices')->where('school_id',$this->school($r))->when(!$r->user()->can('teacher-notice.manage'),fn($q)=>$q->where('created_by',$r->user()->id));}
 private function audience($r,$d){
 $school=$this->school($r);$type=$d['recipient_type'];$year=$d['academic_year_id'];
 abort_unless(DB::table('academic_years')->where('school_id',$school)->where('id',$year)->exists(),422);
 $q=DB::table('student_enrollments as e')->join('students as s','s.id','=','e.student_id')->where('e.school_id',$school)->where('s.school_id',$school)->where('e.academic_year_id',$year)->where('e.is_current',1)->where('e.status',1)->where('s.status',1);
 if($type==='school'){abort_unless($r->user()->can('teacher-notice.school-wide'),403);return $q;}
 $q->where('e.school_class_id',$d['school_class_id']);
 if(in_array($type,['section','student']))$q->where('e.section_id',$d['section_id']);
 if($type==='student')$q->where('e.student_id',$d['student_id']);
 if(!$r->user()->can('teacher-notice.manage')){
 $a=DB::table('teacher_assignments')->where('school_id',$school)->where('teacher_user_id',$r->user()->id)->where('academic_year_id',$year)->where('school_class_id',$d['school_class_id'])->where('status',1)->pluck('section_id');
 if($type==='class'){abort_unless($a->isNotEmpty(),403);abort_if((clone $q)->whereNotIn('e.section_id',$a)->exists(),403,'Class-wide notices require assignment to every enrolled section.');}
 else abort_unless($a->contains($d['section_id']),403);
 }
 return $q;
 }
 public function index(Request $r){$q=$this->scope($r);if($r->filled('q'))$q->where('title','like','%'.$r->q.'%');$rows=$q->orderByDesc('id')->paginate(20)->withQueryString();return view('teacher.student-notices.index',compact('rows'));}
 public function create(Request $r){$school=$this->school($r);$years=DB::table('academic_years')->where('school_id',$school)->orderByDesc('start_date')->get();$classes=DB::table('school_classes')->where('school_id',$school)->orderBy('sort_order')->get();return view('teacher.student-notices.create',compact('years','classes'));}
 public function recipients(Request $r){$d=$r->validate(['academic_year_id'=>'required|integer','school_class_id'=>'required|integer','section_id'=>'nullable|integer']);$school=$this->school($r);$q=DB::table('sections')->where('school_id',$school)->where('academic_year_id',$d['academic_year_id'])->where('school_class_id',$d['school_class_id']);if(!$r->user()->can('teacher-notice.manage'))$q->whereIn('id',DB::table('teacher_assignments')->where('school_id',$school)->where('teacher_user_id',$r->user()->id)->where('academic_year_id',$d['academic_year_id'])->where('school_class_id',$d['school_class_id'])->where('status',1)->select('section_id'));$sections=$q->get(['id','name']);$students=[];if(!empty($d['section_id'])&&$sections->pluck('id')->contains($d['section_id'])){$students=$this->audience($r,$d+['recipient_type'=>'section'])->select('s.id','s.student_name','s.admission_no')->distinct()->orderBy('s.student_name')->get();}return response()->json(compact('sections','students'));}
 private function clean($html){ // HTML Purifier is already a dependency of neither Laravel nor this ERP; install as documented.
 $config=\HTMLPurifier_Config::createDefault();$config->set('HTML.Allowed','p,br,strong,b,em,i,u,h2,h3,h4,ul,ol,li,blockquote,a[href|title],table,thead,tbody,tr,td,th');$config->set('URI.AllowedSchemes',['https'=>true,'http'=>true,'mailto'=>true]);$config->set('Cache.DefinitionImpl',null);return (new \HTMLPurifier($config))->purify($html);
 }
 public function store(Request $r){$d=$r->validate(['title'=>'required|string|max:255','body'=>'nullable|string|max:50000','recipient_type'=>'required|in:school,class,section,student','academic_year_id'=>'required|integer','school_class_id'=>'required_unless:recipient_type,school|nullable|integer','section_id'=>'required_if:recipient_type,section,student|nullable|integer','student_id'=>'required_if:recipient_type,student|nullable|integer','priority'=>'required|in:normal,important,urgent','publish_at'=>'required|date','expires_at'=>'nullable|date|after:publish_at','action'=>'required|in:draft,publish','files'=>'nullable|array|max:8','files.*'=>'file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,webm|max:51200']);
 $d['school_class_id']=$d['recipient_type']==='school'?null:$d['school_class_id'];$d['section_id']=in_array($d['recipient_type'],['section','student'])?$d['section_id']:null;$d['student_id']=$d['recipient_type']==='student'?$d['student_id']:null;
 $body=$this->clean($d['body']??'');if(!trim(strip_tags($body))&&!$r->hasFile('files'))throw ValidationException::withMessages(['body'=>'Enter a message or attach a file.']);
 $ids=$this->audience($r,$d)->distinct()->pluck('e.student_id');abort_if($ids->isEmpty(),422,'No eligible students.');if($d['action']==='publish')abort_unless($r->user()->can('teacher-notice.publish'),403);
 $paths=[];try{$id=DB::transaction(function()use($r,$d,$body,$ids,&$paths){$id=DB::table('student_notices')->insertGetId(collect($d)->except(['action','files','body'])->all()+['body'=>$body,'school_id'=>$this->school($r),'created_by'=>$r->user()->id,'status'=>$d['action']==='publish'?'published':'draft','created_at'=>now(),'updated_at'=>now()]);foreach($r->file('files',[]) as $f){$path=$f->store('student-notices/'.$this->school($r),'local');if(!$path)throw new \RuntimeException('Upload failed');$paths[]=$path;DB::table('student_notice_attachments')->insert(['notice_id'=>$id,'path'=>$path,'original_name'=>substr($f->getClientOriginalName(),0,255),'mime'=>$f->getMimeType(),'size'=>$f->getSize()]);}if($d['action']==='publish')$this->snapshot($id,$ids);return $id;});}catch(\Throwable $e){Storage::disk('local')->delete($paths);throw $e;}return redirect()->route('teacher.student-notices.show',$id)->with('success','Notice saved.');}
 private function snapshot($id,$ids){foreach($ids->chunk(500) as $chunk)DB::table('student_notice_recipients')->insert($chunk->map(fn($s)=>['notice_id'=>$id,'student_id'=>$s])->values()->all());}
 public function publish(Request $r,$id){DB::transaction(function()use($r,$id){$n=$this->scope($r)->where('id',$id)->lockForUpdate()->first();abort_unless($n&&$n->status==='draft',404);$ids=$this->audience($r,(array)$n)->distinct()->pluck('e.student_id');abort_if($ids->isEmpty(),422);abort_if($n->expires_at&&now()->gte($n->expires_at),422,'Notice expired.');$this->snapshot($id,$ids);$this->scope($r)->where('id',$id)->update(['status'=>'published','updated_at'=>now()]);});return back();}
 public function withdraw(Request $r,$id){abort_unless($this->scope($r)->where('id',$id)->where('status','published')->update(['status'=>'withdrawn','updated_at'=>now()]),404);return back();}
 public function show(Request $r,$id){$n=$this->scope($r)->where('id',$id)->first();abort_unless($n,404);$files=DB::table('student_notice_attachments')->where('notice_id',$id)->get();$count=DB::table('student_notice_recipients')->where('notice_id',$id)->count();return view('teacher.student-notices.show',compact('n','files','count'));}
 public function download(Request $r,$id,$file){abort_unless($this->scope($r)->where('id',$id)->exists(),404);$f=DB::table('student_notice_attachments')->where('notice_id',$id)->where('id',$file)->first();abort_unless($f,404);return Storage::disk('local')->download($f->path,$f->original_name,['X-Content-Type-Options'=>'nosniff']);}
}
