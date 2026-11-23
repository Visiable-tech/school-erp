@extends('layouts.admin')
@section('content')
<div class="container-fluid py-3"><h4>Student Mobile App Logins</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<form method="GET" class="row g-2 mb-3" id="student-filters">
<div class="col-md-3"><label class="form-label">Academic Year</label><select class="form-select" name="academic_year_id" id="academic_year_id"><option value="">All years</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected(request('academic_year_id')==$year->id)>{{ $year->name ?? $year->year_name ?? $year->id }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Class</label><select class="form-select" name="school_class_id" id="school_class_id"><option value="">All classes</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('school_class_id')==$class->id)>{{ $class->name ?? $class->class_name ?? $class->id }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Section</label><select class="form-select" name="section_id" id="section_id"><option value="">All sections</option>@foreach($sections as $section)<option value="{{ $section->id }}" @selected(request('section_id')==$section->id)>{{ $section->name ?? $section->section_name ?? $section->id }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Student</label><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Name or admission no."></div>
<div class="col-12"><button class="btn btn-outline-primary">Filter Students</button> <a href="{{ route('student-app-logins.index') }}" class="btn btn-outline-secondary">Clear</a></div></form>
<form method="POST" action="{{ route('student-app-logins.generate-filtered') }}" class="mb-3">@csrf
<input type="hidden" name="academic_year_id" value="{{ request('academic_year_id') }}"><input type="hidden" name="school_class_id" value="{{ request('school_class_id') }}"><input type="hidden" name="section_id" value="{{ request('section_id') }}">
<button class="btn btn-success" type="submit" @disabled(!request('academic_year_id') || !request('school_class_id') || !request('section_id')) onclick="return confirm('Generate passwords for ALL eligible students without accounts in this academic year, class and section (maximum 500)?')">Generate All Passwords for Filtered Class/Section</button>
<small class="d-block text-muted">Choose academic year, class and section first. Existing accounts are skipped.</small></form>
<form method="POST" action="{{ route('student-app-logins.generate') }}" id="bulk-form">@csrf
<button class="btn btn-primary mb-3" type="submit" onclick="return confirm('Generate temporary passwords for selected students without accounts? Download the CSV immediately and store it securely.')">Generate Selected Passwords</button>
<div class="table-responsive"><table class="table table-bordered table-striped align-middle"><thead><tr><th><input type="checkbox" id="select-all"></th><th>Student</th><th>Login ID (Admission No.)</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($students as $s)<tr><td><input type="checkbox" name="student_ids[]" value="{{ $s->id }}" @disabled($s->account_id || !$s->status)></td><td>{{ $s->student_name }}</td><td>{{ $s->admission_no }}</td><td>@if(!$s->account_id)Not generated @elseif($s->account_active)Active @else Disabled @endif</td><td>
@if($s->account_id)<button type="submit" form="reset-{{ $s->id }}" class="btn btn-sm btn-outline-warning" onclick="return confirm('Reset password and revoke existing sessions?')">Reset Password</button> <button type="submit" form="toggle-{{ $s->id }}" class="btn btn-sm btn-outline-secondary">{{ $s->account_active ? 'Disable' : 'Enable' }}</button>@endif
</td></tr>@empty<tr><td colspan="5">No students found.</td></tr>@endforelse
</tbody></table></div></form>
@foreach($students as $s)@if($s->account_id)
<form id="reset-{{ $s->id }}" method="POST" action="{{ route('student-app-logins.reset',$s->id) }}">@csrf</form>
<form id="toggle-{{ $s->id }}" method="POST" action="{{ route('student-app-logins.toggle',$s->id) }}">@csrf</form>
@endif @endforeach
{{ $students->links() }}
<p class="text-muted small">Passwords are never stored in plain text. The generated CSV is available only in the immediate response. Generate Selected skips accounts that already exist.</p>
</div><script>document.getElementById('select-all').addEventListener('change',function(){document.querySelectorAll('input[name="student_ids[]"]:not(:disabled)').forEach(x=>x.checked=this.checked)});</script>
@endsection
