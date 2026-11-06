@extends('layouts.admin')
@section('title','Employee Shifts')
@section('content')
<div class="container-fluid"><h4>Employee Shift Assignments</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="card mb-3"><div class="card-header">Assign Shift</div><div class="card-body">
<form method="POST" action="{{ route('hr-payroll.attendance.shifts.store') }}" class="row g-2">@csrf
<div class="col-md-4"><label class="form-label">Employee</label><select name="user_id" class="form-select" required><option value="">Select</option>@foreach($staff as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Shift</label><select name="shift_id" class="form-select" required><option value="">Select</option>@foreach($shifts as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ substr($s->start_time,0,5) }}–{{ substr($s->end_time,0,5) }})</option>@endforeach</select></div>
<div class="col-md-2"><label class="form-label">Effective From</label><input type="date" name="effective_from" class="form-control" required></div>
<div class="col-md-2"><label class="form-label">Effective To</label><input type="date" name="effective_to" class="form-control"></div>
<div class="col-12"><button class="btn btn-primary">Assign Shift</button></div>
</form></div></div>
<div class="table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Employee</th><th>Shift</th><th>From</th><th>To</th></tr></thead><tbody>
@forelse($assignments as $a)
<tr><td>{{ $a->employee_name }}</td><td>{{ $a->shift_name }}</td><td>{{ $a->effective_from }}</td><td>{{ $a->effective_to??'Ongoing' }}</td></tr>
@empty
<tr><td colspan="4">No shift assignments found.</td></tr>
@endforelse
</tbody></table></div>{{ $assignments->links() }}</div>
@endsection
