<?php
namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class TeacherAssignmentController extends Controller
{
    private function school(Request $request): int
    {
        $school = $request->user()?->school_id;
        abort_unless($school, 403, 'Your account must be assigned to a school.');
        return (int) $school;
    }

    private function scoped(Request $request)
    {
        return TeacherAssignment::query()->where('school_id', $this->school($request));
    }

    private function options(Request $request): array
    {
        $school = $this->school($request);
        return [
            'teachers' => User::role('Teacher')->where('school_id', $school)->orderBy('name')->get(['id','name','email']),
            'years' => AcademicYear::where('school_id', $school)->orderByDesc('id')->get(['id','name']),
            'classes' => SchoolClass::where('school_id', $school)->orderBy('sort_order')->get(['id','name']),
            'sections' => Section::where('school_id', $school)->orderBy('sort_order')->get(['id','name','school_class_id','academic_year_id']),
            'subjects' => Subject::where('school_id', $school)->orderBy('name')->get(['id','name']),
        ];
    }

    public function index(Request $request)
    {
        $query = $this->scoped($request)->with(['teacher','academicYear','schoolClass','section','subject'])->orderByDesc('id');
        if ($request->filled('teacher_user_id')) $query->where('teacher_user_id', $request->integer('teacher_user_id'));
        if ($request->filled('academic_year_id')) $query->where('academic_year_id', $request->integer('academic_year_id'));
        $assignments = $query->paginate(25)->withQueryString();
        return view('teacher.assignments.index', array_merge(compact('assignments'), $this->options($request)));
    }

    public function create(Request $request)
    {
        return view('teacher.assignments.form', array_merge(['assignment' => new TeacherAssignment()], $this->options($request)));
    }

    public function edit(Request $request, TeacherAssignment $assignment)
    {
        abort_unless((int)$assignment->school_id === $this->school($request), 404);
        return view('teacher.assignments.form', array_merge(compact('assignment'), $this->options($request)));
    }

    private function validated(Request $request, ?TeacherAssignment $assignment = null): array
    {
        $school = $this->school($request);
        $data = $request->validate([
            'teacher_user_id' => ['required','integer',Rule::exists('users','id')->where('school_id',$school)],
            'academic_year_id' => ['required','integer',Rule::exists('academic_years','id')->where('school_id',$school)],
            'school_class_id' => ['required','integer',Rule::exists('school_classes','id')->where('school_id',$school)],
            'section_id' => ['required','integer',Rule::exists('sections','id')->where('school_id',$school)],
            'subject_id' => ['nullable','integer',Rule::exists('subjects','id')->where('school_id',$school)],
            'is_class_teacher' => ['nullable','boolean'],
            'status' => ['nullable','boolean'],
        ]);
        $teacher = User::where('school_id',$school)->findOrFail($data['teacher_user_id']);
        if (!$teacher->hasRole('Teacher')) {
            throw ValidationException::withMessages(['teacher_user_id'=>'Select a user with the Teacher role.']);
        }
        $section = Section::where('school_id',$school)->findOrFail($data['section_id']);
        if ((int)$section->school_class_id !== (int)$data['school_class_id'] ||
            (int)$section->academic_year_id !== (int)$data['academic_year_id']) {
            throw ValidationException::withMessages(['section_id'=>'Section must belong to the selected class and academic year.']);
        }
        $data['subject_id'] = $data['subject_id'] ?? null;
        $data['is_class_teacher'] = $request->boolean('is_class_teacher');
        $data['status'] = $request->boolean('status');
        $data['school_id'] = $school;
        return $data;
    }

    private function save(Request $request, ?TeacherAssignment $assignment = null)
    {
        $data = $this->validated($request, $assignment);
        DB::transaction(function () use ($data, $assignment) {
            // Serialize assignment writes within a school to prevent conflicting class-teacher assignments.
            \App\Models\School::whereKey($data['school_id'])->lockForUpdate()->firstOrFail();
            $existing = TeacherAssignment::where('school_id',$data['school_id'])
                ->where('teacher_user_id',$data['teacher_user_id'])
                ->where('academic_year_id',$data['academic_year_id'])
                ->where('section_id',$data['section_id'])
                ->where('subject_id',$data['subject_id']);
            if ($assignment?->exists) $existing->whereKeyNot($assignment->id);
            if ($existing->exists()) {
                throw ValidationException::withMessages(['subject_id'=>'This teacher already has the same assignment.']);
            }
            if ($data['is_class_teacher'] && $data['status']) {
                $other = TeacherAssignment::where('school_id',$data['school_id'])
                    ->where('academic_year_id',$data['academic_year_id'])
                    ->where('section_id',$data['section_id'])
                    ->where('is_class_teacher',true)->where('status',true);
                if ($assignment?->exists) $other->whereKeyNot($assignment->id);
                if ($other->exists()) {
                    throw ValidationException::withMessages(['is_class_teacher'=>'This section already has an active class teacher.']);
                }
            }
            if ($assignment) $assignment->update($data);
            else TeacherAssignment::create($data);
        });
        return redirect()->route('teacher-assignments.index')->with('success','Teacher assignment saved successfully.');
    }

    public function store(Request $request) { return $this->save($request); }

    public function update(Request $request, TeacherAssignment $assignment)
    {
        abort_unless((int)$assignment->school_id === $this->school($request), 404);
        return $this->save($request, $assignment);
    }

    public function destroy(Request $request, TeacherAssignment $assignment)
    {
        abort_unless((int)$assignment->school_id === $this->school($request), 404);
        $assignment->delete();
        return redirect()->route('teacher-assignments.index')->with('success','Teacher assignment removed.');
    }
}
