<?php
namespace App\Http\Controllers;
use App\Models\{AdmissionCandidate,AcademicYear,SchoolClass};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,DB,Storage};
use Illuminate\Validation\Rule;
class AdmissionCandidateController extends Controller {
 private function schoolId(): int { $id=Auth::user()?->school_id; abort_unless($id,403,'School not assigned'); return (int)$id; }
 private function candidate(int $id): AdmissionCandidate {return AdmissionCandidate::where('school_id',$this->schoolId())->findOrFail($id);}
 private function choices(): array {return ['years'=>AcademicYear::where('school_id',$this->schoolId())->orderByDesc('id')->get(), 'classes'=>SchoolClass::where('school_id',$this->schoolId())->orderBy('sort_order')->get()];}
 private function rules(): array { $school=$this->schoolId(); return [
 'candidate_name'=>['required','string','max:150'], 'father_name'=>['nullable','string','max:150'], 'mother_name'=>['nullable','string','max:150'],
 'mobile'=>['nullable','string','max:20'], 'date_of_birth'=>['nullable','date','before_or_equal:today'],
 'academic_year_id'=>['nullable',Rule::exists('academic_years','id')->where('school_id',$school)],
 'school_class_id'=>['nullable',Rule::exists('school_classes','id')->where('school_id',$school)],
 'photo'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
 ]; }
 public function index(Request $r){ $q=AdmissionCandidate::where('school_id',$this->schoolId());
 if($r->filled('status') && array_key_exists($r->status,AdmissionCandidate::STATUSES)) $q->where('status',$r->status);
 if($r->filled('search')){ $term=trim($r->search); $q->where(function($sub)use($term){$sub->where('candidate_name','like',"%{$term}%")->orWhere('father_name','like',"%{$term}%")->orWhere('mother_name','like',"%{$term}%")->orWhere('mobile','like',"%{$term}%");}); }
 $candidates=$q->orderBy('candidate_name')->paginate(50)->withQueryString(); return view('admission.candidates.index',compact('candidates')); }
 public function create(){return view('admission.candidates.form',$this->choices());}
 public function store(Request $r){$data=$r->validate($this->rules());unset($data['photo']);$data['school_id']=$this->schoolId();$data['status']='new';
 if($r->hasFile('photo'))$data['photo_path']=$r->file('photo')->store('admission-candidates','public');
 DB::transaction(function()use($data){$c=AdmissionCandidate::create($data);$c->histories()->create(['status'=>'new','created_by'=>Auth::id(),'remarks'=>'Candidate created']);});
 return redirect()->route('admission-candidates.index')->with('success','Candidate created.'); }
 public function edit(int $id){$candidate=$this->candidate($id);return view('admission.candidates.form',array_merge($this->choices(),compact('candidate')));}
 public function update(Request $r,int $id){$candidate=$this->candidate($id);$data=$r->validate($this->rules());unset($data['photo']);
 if($r->hasFile('photo')){$data['photo_path']=$r->file('photo')->store('admission-candidates','public');}
 $oldPhoto=$candidate->photo_path;$candidate->update($data);if(isset($data['photo_path']) && $oldPhoto)Storage::disk('public')->delete($oldPhoto);
 return redirect()->route('admission-candidates.index')->with('success','Candidate updated.');}
 public function track(int $id){$candidate=$this->candidate($id);$histories=$candidate->histories()->with('creator')->orderBy('id')->paginate(50);return view('admission.candidates.track',compact('candidate','histories'));}
 public function status(Request $r,int $id){$candidate=$this->candidate($id);$data=$r->validate(['status'=>['required',Rule::in(array_keys(AdmissionCandidate::STATUSES))], 'enquiry_level'=>['nullable','string','max:100'],'registration_level'=>['nullable','string','max:100'],'remarks'=>['nullable','string','max:2000']]);
 // Admitted is a result of admission confirmation, not a manual status switch.
 if($data['status']==='admitted')return back()->withErrors(['status'=>'Use Admission Confirmations to mark a candidate admitted.']);
 DB::transaction(function()use($candidate,$data){$locked=AdmissionCandidate::whereKey($candidate->id)->lockForUpdate()->firstOrFail();if($locked->status==='admitted')abort(422,'An admitted candidate cannot be changed here.');$locked->update(['status'=>$data['status']]);$locked->histories()->create($data+['created_by'=>Auth::id()]);});
 return redirect()->route('admission-candidates.track',$candidate->id)->with('success','Status updated.');}
}
