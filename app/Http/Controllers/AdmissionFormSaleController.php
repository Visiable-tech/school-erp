<?php
namespace App\Http\Controllers;
use App\Models\AdmissionFormSale;
use App\Models\AdmissionCandidate;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class AdmissionFormSaleController extends Controller {
 private function school(): int { $id=auth()->user()?->school_id; abort_unless($id,403,'Your account has no school assigned.'); return (int)$id; }
 private function options(): array { $id=$this->school();return [
  'years'=>AcademicYear::where('school_id',$id)->orderByDesc('id')->get(),
  'classes'=>SchoolClass::where('school_id',$id)->orderBy('sort_order')->get(),
  'candidates'=>AdmissionCandidate::where('school_id',$id)->orderBy('candidate_name')->get(['id','candidate_name','father_name','mother_name','mobile','date_of_birth','academic_year_id','school_class_id'])
 ]; }
 public function index(Request $request) {
  $school=$this->school(); $q=AdmissionFormSale::with(['academicYear','schoolClass','creator'])->where('school_id',$school);
  foreach(['academic_year_id','school_class_id','status','payment_mode'] as $field){if($request->filled($field)) $q->where($field,$request->input($field));}
  foreach(['hostel_required','approved_for_registration','gender'] as $field){if($request->filled($field)) $q->where($field,$request->input($field));}
  if($request->filled('from_date'))$q->whereDate('application_date','>=',$request->from_date);
  if($request->filled('to_date'))$q->whereDate('application_date','<=',$request->to_date);
  if($request->filled('search')) { $term=$request->search;$q->where(function($sub)use($term){$sub->where('candidate_name','like',"%{$term}%")->orWhere('form_no','like',"%{$term}%")->orWhere('mobile','like',"%{$term}%")->orWhere('father_name','like',"%{$term}%");}); }
  return view('admission.form-sales.index',array_merge($this->options(),['sales'=>$q->latest('id')->paginate(50)->withQueryString()]));
 }
 public function create(){return view('admission.form-sales.form',array_merge($this->options(),['sale'=>new AdmissionFormSale()]));}
 public function edit(AdmissionFormSale $sale){$this->guard($sale);return view('admission.form-sales.form',array_merge($this->options(),['sale'=>$sale]));}
 private function guard(AdmissionFormSale $sale): void {abort_unless((int)$sale->school_id===$this->school(),404);}
 private function validateSale(Request $r,?AdmissionFormSale $sale=null): array {
  $s=$this->school();return $r->validate([
   'academic_year_id'=>['required','integer',Rule::exists('academic_years','id')->where('school_id',$s)],
   'school_class_id'=>['required','integer',Rule::exists('school_classes','id')->where('school_id',$s)],
   'admission_candidate_id'=>['nullable','integer',Rule::exists('admission_candidates','id')->where('school_id',$s)],
   'form_no'=>['required','string','max:40',Rule::unique('admission_form_sales','form_no')->where('school_id',$s)->ignore($sale?->id)],
   'candidate_name'=>'required|string|max:150','father_name'=>'nullable|string|max:150','mother_name'=>'nullable|string|max:150',
   'mobile'=>'nullable|string|max:20','date_of_birth'=>'nullable|date','gender'=>['nullable',Rule::in(['Male','Female','Other'])],
   'application_date'=>'required|date','amount'=>'required|numeric|min:0|max:9999999999',
   'receipt_no'=>'nullable|string|max:60','payment_mode'=>['required',Rule::in(['Cash','Online','Credit/Debit Card','UPI','Cheque','DD','Other'])],
   'status'=>['required',Rule::in(['form_issued','form_submitted','form_rejected','registration_approved','closed'])],
   'approved_for_registration'=>'required|boolean','hostel_required'=>'required|boolean','login_id'=>'nullable|string|max:100','remarks'=>'nullable|string|max:5000'
  ]);
 }
 public function store(Request $r){$data=$this->validateSale($r);$data['school_id']=$this->school();$data['created_by']=auth()->id();AdmissionFormSale::create($data);return redirect()->route('admission-form-sales.index')->with('success','Form sale created.');}
 public function update(Request $r,AdmissionFormSale $sale){$this->guard($sale);$sale->update($this->validateSale($r,$sale));return redirect()->route('admission-form-sales.index')->with('success','Form sale updated.');}
 public function bulk(Request $r){$school=$this->school();$data=$r->validate(['ids'=>'required|array|min:1|max:100','ids.*'=>'required|integer|distinct','action'=>['required',Rule::in(['approve','reject','close'])]]);
  $count=DB::transaction(function()use($data,$school){$q=AdmissionFormSale::where('school_id',$school)->whereIn('id',$data['ids']);
   return match($data['action']){
    'approve'=>$q->update(['approved_for_registration'=>true,'status'=>'registration_approved']),
    'reject'=>$q->update(['approved_for_registration'=>false,'status'=>'form_rejected']),
    'close'=>$q->update(['status'=>'closed']),
   };
  });return back()->with('success',"Updated {$count} form sale(s).");
 }
 public function export(Request $request){$school=$this->school();$rows=AdmissionFormSale::with(['academicYear','schoolClass'])->where('school_id',$school)->latest('id')->limit(10000)->get();
  return response()->streamDownload(function()use($rows){$h=fopen('php://output','w');fputcsv($h,['Form No','Year','Class','Candidate','Father','Mother','Mobile','DOB','Gender','Application Date','Amount','Receipt No','Payment Mode','Status','Approved','Hostel']);foreach($rows as $r){fputcsv($h,[$r->form_no,$r->academicYear?->name,$r->schoolClass?->name,$r->candidate_name,$r->father_name,$r->mother_name,$r->mobile,$r->date_of_birth?->format('Y-m-d'),$r->gender,$r->application_date?->format('Y-m-d'),$r->amount,$r->receipt_no,$r->payment_mode,$r->status,$r->approved_for_registration?'Yes':'No',$r->hostel_required?'Yes':'No']);}fclose($h);},'form-sales.csv',['Content-Type'=>'text/csv']);
 }
}
