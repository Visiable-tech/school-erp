<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class TeacherPerformanceController extends Controller {
 private function school(Request $r):int {
  abort_unless($r->user()?->school_id,403,'School assignment required');
  return (int)$r->user()->school_id;
 }
 private function admin(Request $r):bool {
  return $r->user()->hasAnyRole(['Admin','Super Admin']);
 }
 private function manager(Request $r):void {
  abort_unless($this->admin($r),403,'Administrator access required');
 }
 private function base(Request $r) {
  return DB::table('teacher_performance_reviews')->where('school_id',$this->school($r));
 }
 private function valid(Request $r):array {
  return $r->validate([
   'period'=>['required','regex:/^20\d{2}-(0[1-9]|1[0-2])$/'],
   'teaching_rating'=>'required|integer|between:1,5',
   'classroom_rating'=>'required|integer|between:1,5',
   'communication_rating'=>'required|integer|between:1,5',
   'professionalism_rating'=>'required|integer|between:1,5',
   'strengths'=>'required|string|max:3000',
   'improvement_areas'=>'required|string|max:3000',
   'goals'=>'nullable|string|max:3000',
   'action'=>['required',Rule::in(['draft','submit'])]
  ]);
 }
 public function selfIndex(Request $r) {
  $reviews=$this->base($r)->where('review_type','self')
   ->where('teacher_user_id',$r->user()->id)->orderByDesc('period')->paginate(20);
  return view('teacher.performance.index',compact('reviews')+['type'=>'self']);
 }
 public function teamIndex(Request $r) {
  $this->manager($r);
  $reviews=$this->base($r)->where('review_type','team')->orderByDesc('period')->paginate(20);
  return view('teacher.performance.index',compact('reviews')+['type'=>'team']);
 }
 public function create(Request $r,string $type) {
  abort_unless(in_array($type,['self','team'],true),404);
  if($type==='team')$this->manager($r);
  $teachers=$type==='team'?User::where('school_id',$this->school($r))->role('Teacher')->orderBy('name')->get(['id','name']):collect();
  return view('teacher.performance.form',compact('type','teachers'));
 }
 public function store(Request $r,string $type) {
  abort_unless(in_array($type,['self','team'],true),404);
  if($type==='team')$this->manager($r);
  $data=$this->valid($r);
  $target=$type==='self'?$r->user()->id:(int)$r->validate([
   'teacher_user_id'=>['required','integer',Rule::exists('users','id')->where('school_id',$this->school($r))]
  ])['teacher_user_id'];
  if($type==='team')abort_unless(User::whereKey($target)->role('Teacher')->exists(),422,'Select a teacher');
  $exists=$this->base($r)->where('teacher_user_id',$target)->where('review_type',$type)->where('period',$data['period'])->exists();
  abort_if($exists,422,'An assessment for this teacher and period already exists.');
  $status=$data['action']==='submit'?'submitted':'draft';unset($data['action']);
  DB::table('teacher_performance_reviews')->insert([
   'school_id'=>$this->school($r),'teacher_user_id'=>$target,
   'reviewer_user_id'=>$type==='team'?$r->user()->id:null,
   'review_type'=>$type,...$data,'status'=>$status,
   'created_at'=>now(),'updated_at'=>now()
  ]);
  return redirect()->route($type==='self'?'teacher.performance.self':'teacher.performance.team')->with('success','Assessment saved');
 }
 public function show(Request $r,int $review) {
  $item=$this->base($r)->where('id',$review)->first();
  abort_unless($item,404);
  abort_unless($this->admin($r)||((int)$item->teacher_user_id===(int)$r->user()->id && $item->review_type==='self'),403);
  return view('teacher.performance.show',compact('item'));
 }
 public function review(Request $r,int $review) {
  $this->manager($r);
  $data=$r->validate([
   'decision'=>['required',Rule::in(['approved','rejected'])],
   'review_note'=>'nullable|string|max:3000'
  ]);
  DB::transaction(function()use($r,$review,$data){
   $item=$this->base($r)->where('id',$review)->lockForUpdate()->first();
   abort_unless($item,404);
   abort_unless($item->status==='submitted',422,'Only submitted assessments can be reviewed.');
   abort_if((int)$item->teacher_user_id===(int)$r->user()->id,403,'Cannot review own assessment.');
   abort_if($item->review_type==='team' && (int)$item->reviewer_user_id===(int)$r->user()->id,403,'Cannot approve assessment you authored.');
   DB::table('teacher_performance_reviews')->where('id',$review)->update([
    'status'=>$data['decision'],'review_note'=>$data['review_note']??null,
    'reviewed_by'=>$r->user()->id,'reviewed_at'=>now(),'updated_at'=>now()
   ]);
  });
  return back()->with('success','Assessment reviewed');
 }
}
