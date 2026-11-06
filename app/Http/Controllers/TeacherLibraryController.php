<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;

class TeacherLibraryController extends Controller
{
 private function school(Request $r):int {
  abort_unless($r->user() && $r->user()->school_id,403,'School assignment required.');
  return (int)$r->user()->school_id;
 }
 private function manager(Request $r):void {
  abort_unless($r->user()->hasAnyRole(['Admin','Super Admin']),403,'Library management requires an administrator.');
 }
 public function search(Request $r) {
  $q=DB::table('teacher_library_books')->where('school_id',$this->school($r))->where('status',1);
  if($r->filled('q')){
   $term=trim((string)$r->input('q'));
   $q->where(function($b)use($term){
    $b->where('title','like',"%{$term}%")->orWhere('author','like',"%{$term}%")
      ->orWhere('isbn','like',"%{$term}%")->orWhere('category','like',"%{$term}%");
   });
  }
  $books=$q->orderBy('title')->paginate(20)->withQueryString();
  return view('teacher.library.search',compact('books'));
 }
 public function issued(Request $r) {
  $loans=$this->loanQuery($r)->whereNull('l.returned_on')->orderByDesc('l.id')->paginate(20);
  return view('teacher.library.loans',compact('loans')+['mode'=>'issued']);
 }
 public function history(Request $r) {
  $loans=$this->loanQuery($r)->orderByDesc('l.id')->paginate(20);
  return view('teacher.library.loans',compact('loans')+['mode'=>'history']);
 }
 private function loanQuery(Request $r) {
  $q=DB::table('teacher_library_loans as l')
   ->join('teacher_library_books as b',function($j){$j->on('b.id','=','l.book_id')->on('b.school_id','=','l.school_id');})
   ->where('l.school_id',$this->school($r))
   ->select('l.*','b.title','b.author');
  if(!$r->user()->hasAnyRole(['Admin','Super Admin']))
   $q->where('l.borrower_user_id',$r->user()->id);
  return $q;
 }
 public function catalog(Request $r) {
  $this->manager($r);
  $books=DB::table('teacher_library_books')->where('school_id',$this->school($r))->orderByDesc('id')->paginate(20);
  return view('teacher.library.catalog',compact('books'));
 }
 public function storeBook(Request $r) {
  $this->manager($r);
  $data=$r->validate([
   'title'=>'required|string|max:200','author'=>'nullable|string|max:150',
   'isbn'=>'nullable|string|max:30','category'=>'nullable|string|max:100',
   'shelf'=>'nullable|string|max:100','total_copies'=>'required|integer|min:1|max:100000'
  ]);
  DB::table('teacher_library_books')->insert([
   'school_id'=>$this->school($r),...$data,
   'available_copies'=>$data['total_copies'],'status'=>1,
   'created_at'=>now(),'updated_at'=>now()
  ]);
  return back()->with('success','Library book added.');
 }
 public function issueForm(Request $r) {
  $this->manager($r);
  $school=$this->school($r);
  $books=DB::table('teacher_library_books')->where('school_id',$school)->where('status',1)
   ->where('available_copies','>',0)->orderBy('title')->get(['id','title','available_copies']);
  $users=User::where('school_id',$school)->orderBy('name')->get(['id','name','email']);
  return view('teacher.library.issue',compact('books','users'));
 }
 public function issue(Request $r) {
  $this->manager($r);$school=$this->school($r);
  $data=$r->validate([
   'book_id'=>['required','integer',Rule::exists('teacher_library_books','id')->where('school_id',$school)],
   'borrower_user_id'=>['required','integer',Rule::exists('users','id')->where('school_id',$school)],
   'issued_on'=>'required|date','due_on'=>'required|date|after_or_equal:issued_on',
   'remarks'=>'nullable|string|max:500'
  ]);
  DB::transaction(function()use($data,$school,$r){
   $book=DB::table('teacher_library_books')->where('school_id',$school)->where('id',$data['book_id'])->lockForUpdate()->first();
   abort_unless($book && $book->status && $book->available_copies>0,422,'No copies available.');
   DB::table('teacher_library_books')->where('id',$book->id)->decrement('available_copies');
   DB::table('teacher_library_loans')->insert([
    'school_id'=>$school,'book_id'=>$book->id,'borrower_user_id'=>$data['borrower_user_id'],
    'issued_by'=>$r->user()->id,'issued_on'=>$data['issued_on'],'due_on'=>$data['due_on'],
    'remarks'=>$data['remarks']??null,'created_at'=>now(),'updated_at'=>now()
   ]);
  });
  return redirect()->route('teacher.library.issued')->with('success','Book issued successfully.');
 }
 public function returnBook(Request $r,int $loan) {
  $this->manager($r);$school=$this->school($r);
  $r->validate(['returned_on'=>'required|date']);
  DB::transaction(function()use($r,$loan,$school){
   $row=DB::table('teacher_library_loans')->where('id',$loan)->where('school_id',$school)->lockForUpdate()->first();
   abort_unless($row,404);
   abort_if($row->returned_on,422,'Book already returned.');
   abort_if($r->input('returned_on')<$row->issued_on,422,'Return date cannot precede issue date.');
   DB::table('teacher_library_loans')->where('id',$row->id)->update([
    'returned_on'=>$r->input('returned_on'),'returned_by'=>$r->user()->id,'updated_at'=>now()
   ]);
   DB::table('teacher_library_books')->where('id',$row->book_id)->where('school_id',$school)->increment('available_copies');
  });
  return back()->with('success','Book returned successfully.');
 }
}
