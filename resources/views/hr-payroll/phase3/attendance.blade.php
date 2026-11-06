@extends('layouts.admin')
@section('title','Daily HR Attendance')
@section('content')
<div class="container-fluid"><h4>Daily Employee Attendance</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="GET" class="mb-3 d-flex gap-2"><input type="date" name="date" value="{{ $date }}" class="form-control" style="max-width:200px"><button class="btn btn-outline-primary">View</button></form>
<div class="card mb-3"><div class="card-header">Mark / Update Attendance</div><div class="card-body">
<form method="POST" action="{{ route('hr-payroll.attendance.daily.store') }}" class="row g-2">@csrf
<div class="col-md-4"><label class="form-label">Employee</label><select name="user_id" class="form-select" required><option value="">Choose employee</option>@foreach($staff as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Date</label><input type="date" name="attendance_date" value="{{ $date }}" class="form-control" required></div>
<div class="col-md-5"><label class="form-label">Attendance Status</label><select name="attendance_status_id" class="form-select" required><option value="">Choose status</option>@foreach($statuses as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }})</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Check In</label><input type="time" name="check_in" class="form-control"></div>
<div class="col-md-3"><label class="form-label">Check Out</label><input type="time" name="check_out" class="form-control"></div>
<div class="col-md-3"><label class="form-label">Overtime (minutes)</label><input type="number" name="overtime_minutes" min="0" max="1440" value="0" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Note</label><input name="note" class="form-control"></div>
<div class="col-12"><button class="btn btn-primary">Save Attendance</button></div>
</form></div></div>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Employee</th><th>Status</th><th>Check In</th><th>Check Out</th><th>Overtime</th><th>Note</th></tr></thead><tbody>
@forelse($entries as $a)
<tr><td>{{ $a->employee_name }}</td><td>{{ $a->status_name }}</td><td>{{ $a->check_in??'—' }}</td><td>{{ $a->check_out??'—' }}</td><td>{{ $a->overtime_minutes }} min</td><td>{{ $a->note??'—' }}</td></tr>
@empty
<tr><td colspan="6" class="text-center">No attendance recorded for this date.</td></tr>
@endforelse
</tbody></table></div></div>
@endsection
