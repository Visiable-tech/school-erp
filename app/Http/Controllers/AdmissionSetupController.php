<?php

namespace App\Http\Controllers;

use App\Models\AdmissionSetup;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdmissionSetupController extends Controller
{
    private function schoolId(): int {
        $id = auth()->user()?->school_id;
        abort_unless($id, 403, 'School is not assigned.');
        return (int) $id;
    }
    private function lists(): array {
        $schoolId = $this->schoolId();
        return [
            'years' => AcademicYear::where('school_id',$schoolId)->orderByDesc('start_date')->get(),
            'classes' => SchoolClass::where('school_id',$schoolId)->where('status',1)->orderBy('sort_order')->orderBy('name')->get(),
        ];
    }
    public function index(Request $request) {
        $q = AdmissionSetup::with(['academicYear','schoolClass'])->where('school_id',$this->schoolId());
        if (in_array($request->input('status'), ['open','closed'],true)) $q->where('status',$request->input('status'));
        if ($request->filled('academic_year_id')) $q->where('academic_year_id',$request->integer('academic_year_id'));
        if ($request->filled('school_class_id')) $q->where('school_class_id',$request->integer('school_class_id'));
        return view('admission.setups.index',array_merge($this->lists(),[
            'setups'=>$q->orderByDesc('academic_year_id')->orderBy('school_class_id')->paginate(25)->withQueryString()
        ]));
    }
    public function initiate(Request $request) {
        $schoolId = $this->schoolId();
        $data = $request->validate([
            'academic_year_id'=>['required',Rule::exists('academic_years','id')->where('school_id',$schoolId)],
            'no_of_seats'=>['required','integer','min:0','max:100000'],
        ]);
        $year = AcademicYear::where('school_id',$schoolId)->findOrFail($data['academic_year_id']);
        $classes = SchoolClass::where('school_id',$schoolId)->where('status',1)->orderBy('sort_order')->get();
        if ($classes->isEmpty()) return back()->withErrors(['academic_year_id'=>'No active classes exist.']);
        $count=0;
        DB::transaction(function () use ($classes,$year,$schoolId,$data,&$count) {
            foreach ($classes as $class) {
                // Use stable ID suffix to prevent class-name/code collisions.
                $code = 'ADM-'.$year->id.'-'.$class->id;
                $created = AdmissionSetup::firstOrCreate(
                    ['school_id'=>$schoolId,'academic_year_id'=>$year->id,'school_class_id'=>$class->id],
                    ['admission_code'=>$code,'no_of_seats'=>$data['no_of_seats'],'status'=>'open']
                );
                if ($created->wasRecentlyCreated) $count++;
            }
        });
        return redirect()->route('admission-setups.index',['academic_year_id'=>$year->id])
            ->with('success',"Created {$count} admission setup(s); existing setups were preserved.");
    }
    public function edit(AdmissionSetup $admissionSetup) {
        abort_unless($admissionSetup->school_id === $this->schoolId(),403);
        return view('admission.setups.edit',['setup'=>$admissionSetup->load(['academicYear','schoolClass'])]);
    }
    public function update(Request $request, AdmissionSetup $admissionSetup) {
        $schoolId=$this->schoolId();
        abort_unless($admissionSetup->school_id === $schoolId,403);
        $data=$request->validate([
            'status'=>['required',Rule::in(['open','closed'])],
            'admission_code'=>['required','string','max:100',Rule::unique('admission_setups','admission_code')->where('school_id',$schoolId)->ignore($admissionSetup->id)],
            'no_of_seats'=>['required','integer','min:0','max:100000'],
            'required_age_months'=>['nullable','integer','min:0','max:300'],
            'dob_check_date'=>['nullable','date'],
            'dob_check_start'=>['nullable','date'],
            'dob_check_end'=>['nullable','date','after_or_equal:dob_check_start'],
            'opens_on'=>['nullable','date'],
            'closes_on'=>['nullable','date','after_or_equal:opens_on'],
            'default_exam_at'=>['nullable','date'],
            'form_fee'=>['required','numeric','min:0'],
            'form_fee_label'=>['nullable','string','max:150'],
            'registration_fee'=>['required','numeric','min:0'],
            'registration_fee_label'=>['nullable','string','max:150'],
            'online_registration_fee'=>['required','numeric','min:0'],
            'syllabus'=>['nullable','file','mimes:pdf','max:5120'],
        ]);
        unset($data['syllabus']);
        if ($request->hasFile('syllabus')) {
            $newPath=$request->file('syllabus')->store('admission-syllabi','public');
            $oldPath=$admissionSetup->syllabus_path;
            $data['syllabus_path']=$newPath;
        }
        try { $admissionSetup->update($data); }
        catch (\Throwable $e) {
            if (isset($newPath)) Storage::disk('public')->delete($newPath);
            throw $e;
        }
        if (isset($newPath) && $oldPath) Storage::disk('public')->delete($oldPath);
        return redirect()->route('admission-setups.index',['academic_year_id'=>$admissionSetup->academic_year_id])
            ->with('success','Admission setup updated successfully.');
    }
}
