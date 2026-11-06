@extends('layouts.admin')
@section('title','Salary Days Calculation')
@section('content')
<div class="container-fluid"><h4>Employee Salary Days Calculation</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="alert alert-warning">Preliminary calculations use recorded attendance and approved leave dates. Missing attendance, weekends, holidays and paid/unpaid leave rules require HR verification. Finalized months cannot be changed.</div>
<form method="GET" class="d-flex gap-2 mb-3"><input type="month" name="month" value="{{ $month }}" class="form-control" style="max-width:220px"><button class="btn btn-outline-primary">View Month</button></form>
@if(!$run || $run->status!=='finalized')
<div class="card mb-3"><div class="card-header">Calculate Preliminary Paid Days</div><div class="card-body">
<form method="POST" action="{{ route('hr-payroll.attendance.paid-days.calculate') }}" class="row g-2">@csrf
<input type="hidden" name="month" value="{{ $month }}">
<div class="col-md-3"><label class="form-label">Working Days</label><input name="working_days" type="number" min="1" max="31" value="{{ $run->working_days??26 }}" class="form-control" required></div>
<div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary">Calculate for Salary Profiles</button></div>
</form></div></div>
@endif
<div class="table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Employee</th><th>Paid Days</th><th>Calculation Note</th><th>Manual Correction</th></tr></thead><tbody>
@forelse($staff as $employee)
@php($value=$rows->get($employee->id))
<tr><td>{{ $employee->name }}</td><td>{{ $value->paid_days??'Not calculated' }}</td><td>{{ $value->calculation_note??'—' }}</td>
<td>
@if(!$run || $run->status!=='finalized')
<form method="POST" action="{{ route('hr-payroll.attendance.paid-days.override') }}" class="d-flex flex-wrap gap-1">@csrf
<input type="hidden" name="user_id" value="{{ $employee->id }}"><input type="hidden" name="month" value="{{ $month }}">
<input type="number" name="paid_days" step="0.5" min="0" max="31" value="{{ $value->paid_days??'' }}" placeholder="Paid days" class="form-control form-control-sm" style="width:100px" required>
<input type="number" name="working_days" min="1" max="31" value="{{ $run->working_days??26 }}" class="form-control form-control-sm" style="width:85px" required>
<input name="calculation_note" placeholder="Reason required" class="form-control form-control-sm" style="width:190px" maxlength="2000" required>
<button class="btn btn-sm btn-outline-primary">Save</button>
</form>
@else Locked @endif
</td></tr>
@empty
<tr><td colspan="4">No employees found.</td></tr>
@endforelse
</tbody></table></div></div>
@endsection
