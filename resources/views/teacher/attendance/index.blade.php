@extends('layouts.admin')
@section('title','Day Wise Attendance')
@section('content')
<div class="container-fluid"><h4>Day Wise Attendance</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<form method="GET" class="row g-2 mb-3"><div class="col-md-5"><select name="assignment_id" class="form-select" required><option value="">Select assigned class / section</option>@foreach($assignments as $a)<option value="{{ $a->id }}" @selected($selected?->id==$a->id)>{{ $a->academicYear?->name }} — {{ $a->schoolClass?->name }} / {{ $a->section?->name }}</option>@endforeach</select></div><div class="col-md-3"><input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}" class="form-control" required></div><div class="col-md-2"><button class="btn btn-primary">Load Students</button></div></form>
@if($selected)
<form method="POST" action="{{ route('teacher.attendance.store') }}">@csrf<input type="hidden" name="assignment_id" value="{{ $selected->id }}"><input type="hidden" name="date" value="{{ $date }}">
<div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle"><thead><tr><th>Roll</th><th>Student</th><th>Attendance</th></tr></thead><tbody>
@forelse($students as $enrollment)<tr><td>{{ $enrollment->roll_no }}</td><td>{{ $enrollment->student?->student_name }}</td><td><select class="form-select" name="marks[{{ $enrollment->id }}]" required>@foreach(['present'=>'Present','absent'=>'Absent','late'=>'Late','leave'=>'Leave','half_day'=>'Half Day'] as $key=>$label)<option value="{{ $key }}" @selected(old('marks.'.$enrollment->id,$marks->get($enrollment->id)?->attendance_status??'present')===$key)>{{ $label }}</option>@endforeach</select></td></tr>
@empty<tr><td colspan="3">No current enrollments in this section.</td></tr>@endforelse
</tbody></table></div>@if($students->isNotEmpty())<button class="btn btn-success">Save Attendance</button>@endif</form>
@endif
</div>
@endsection
