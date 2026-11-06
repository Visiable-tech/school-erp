@extends('layouts.admin')
@section('title', $assignment->exists ? 'Edit Teacher Assignment' : 'Assign Teacher')
@section('content')
<div class="container-fluid">
 <h4 class="mb-3">{{ $assignment->exists ? 'Edit Teacher Assignment' : 'Assign Teacher to Class / Section / Subject' }}</h4>
 @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
 <form method="POST" action="{{ $assignment->exists ? route('teacher-assignments.update',$assignment) : route('teacher-assignments.store') }}">
 @csrf @if($assignment->exists)@method('PUT')@endif
 <div class="card"><div class="card-body"><div class="row g-3">
 <div class="col-md-6"><label class="form-label">Teacher *</label><select name="teacher_user_id" class="form-select" required><option value="">Select Teacher</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}" @selected(old('teacher_user_id',$assignment->teacher_user_id)==$teacher->id)>{{ $teacher->name }} ({{ $teacher->email }})</option>@endforeach</select></div>
 <div class="col-md-6"><label class="form-label">Academic Year *</label><select id="year" name="academic_year_id" class="form-select" required><option value="">Select Year</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected(old('academic_year_id',$assignment->academic_year_id)==$year->id)>{{ $year->name }}</option>@endforeach</select></div>
 <div class="col-md-6"><label class="form-label">Class *</label><select id="schoolClass" name="school_class_id" class="form-select" required><option value="">Select Class</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(old('school_class_id',$assignment->school_class_id)==$class->id)>{{ $class->name }}</option>@endforeach</select></div>
 <div class="col-md-6"><label class="form-label">Section *</label><select id="section" name="section_id" class="form-select" required><option value="">Select Section</option>@foreach($sections as $section)<option data-class="{{ $section->school_class_id }}" data-year="{{ $section->academic_year_id }}" value="{{ $section->id }}" @selected(old('section_id',$assignment->section_id)==$section->id)>{{ $section->name }}</option>@endforeach</select><small class="text-muted">Sections must match the selected class and academic year.</small></div>
 <div class="col-md-6"><label class="form-label">Subject</label><select name="subject_id" class="form-select"><option value="">General / All Subjects</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(old('subject_id',$assignment->subject_id)==$subject->id)>{{ $subject->name }}</option>@endforeach</select></div>
 <div class="col-md-6 d-flex align-items-end gap-4"><div class="form-check"><input type="hidden" name="is_class_teacher" value="0"><input class="form-check-input" type="checkbox" name="is_class_teacher" value="1" id="classTeacher" @checked(old('is_class_teacher',$assignment->is_class_teacher))><label class="form-check-label" for="classTeacher">Class Teacher</label></div>
 <div class="form-check"><input type="hidden" name="status" value="0"><input class="form-check-input" type="checkbox" name="status" value="1" id="active" @checked(old('status',$assignment->exists ? $assignment->status : true))><label class="form-check-label" for="active">Active</label></div></div>
 </div></div><div class="card-footer"><button class="btn btn-primary">Save Assignment</button> <a class="btn btn-secondary" href="{{ route('teacher-assignments.index') }}">Cancel</a></div></div>
 </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
 const year=document.getElementById('year'), cls=document.getElementById('schoolClass'), sec=document.getElementById('section');
 function filterSections() {
   for(const option of sec.options) {
     if(!option.value) continue;
     option.hidden=option.dataset.class!==cls.value || option.dataset.year!==year.value;
   }
   if(sec.selectedOptions[0]?.hidden) sec.value='';
 }
 year.addEventListener('change',filterSections);cls.addEventListener('change',filterSections);filterSections();
});
</script>
@endsection
