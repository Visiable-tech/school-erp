@extends('layouts.admin')
@section('title','Period Wise Attendance')
@section('content')
<div class="container-fluid"><h4>Period Wise Attendance</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<form method="GET" class="row g-2 mb-3"><div class="col-md-5"><select name="assignment_id" class="form-select" required><option value="">Select Assigned Class / Subject</option>@foreach($assignments as $a)<option value="{{ $a->id }}" @selected($assignment?->id==$a->id)>{{ $a->academicYear?->name }} — {{ $a->schoolClass?->name }} / {{ $a->section?->name }} — {{ $a->subject?->name ?? 'General' }}</option>@endforeach</select></div><div class="col-md-3"><input type="date" name="attendance_date" class="form-control" value="{{ $date }}" max="{{ now()->toDateString() }}" required></div><div class="col-md-2"><input type="number" min="1" max="20" name="period_number" value="{{ $period }}" class="form-control" required></div><div class="col-md-2"><button class="btn btn-primary">Load Students</button></div></form>
@if($assignment)
<form method="POST" action="{{ route('teacher.period-attendance.store') }}">@csrf
<input type="hidden" name="assignment_id" value="{{ $assignment->id }}"><input type="hidden" name="attendance_date" value="{{ $date }}"><input type="hidden" name="period_number" value="{{ $period }}">
<div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle"><thead><tr><th>Roll No.</th><th>Student</th><th>Attendance</th></tr></thead><tbody>
@forelse($students as $e)<tr><td>{{ $e->roll_no }}</td><td>{{ $e->student?->first_name }} {{ $e->student?->last_name }}</td><td><select class="form-select" name="statuses[{{ $e->id }}]">@foreach(['present','absent','late','leave'] as $s)<option value="{{ $s }}" @selected(($records[$e->id]??'present')===$s)>{{ ucfirst($s) }}</option>@endforeach</select></td></tr>@empty<tr><td colspan="3">No enrolled students.</td></tr>@endforelse
</tbody></table></div>@if($students->isNotEmpty())<button class="btn btn-success">Save Period Attendance</button>@endif</form>
@endif</div>
@endsection
