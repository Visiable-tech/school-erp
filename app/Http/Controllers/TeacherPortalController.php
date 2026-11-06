<?php
namespace App\Http\Controllers;
use App\Models\TeacherAssignment;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class TeacherPortalController extends Controller {
 private function assignments(Request $request) {
  $user=$request->user();
  abort_unless($user && $user->school_id,403,'A school must be assigned to your account.');
  return TeacherAssignment::query()->where('school_id',$user->school_id)->where('teacher_user_id',$user->id)->where('status',true);
 }
 public function dashboard(Request $request) {
  $assignments=$this->assignments($request)->with(['academicYear','schoolClass','section','subject'])->orderByDesc('id')->get();
  $sectionIds=$assignments->pluck('section_id')->unique()->values();
  $studentCount=$sectionIds->isEmpty()?0:StudentEnrollment::query()->where('school_id',$request->user()->school_id)->whereIn('section_id',$sectionIds)->where('is_current',true)->distinct('student_id')->count('student_id');
  return view('teacher.dashboard',compact('assignments','studentCount'));
 }
 public function students(Request $request) {
  $sectionIds=$this->assignments($request)->pluck('section_id')->unique();
  $students=StudentEnrollment::query()->with(['student','academicYear','schoolClass','section'])
   ->where('school_id',$request->user()->school_id)->where('is_current',true)
   ->whereIn('section_id',$sectionIds)->orderBy('section_id')->orderBy('roll_no')->paginate(25);
  return view('teacher.students',compact('students'));
 }
 public function profile(Request $request){return view('teacher.profile',['teacher'=>$request->user()]);}
}
