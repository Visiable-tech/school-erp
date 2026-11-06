@extends('layouts.admin')
@section('title','Attendance Payroll Summary')
@section('content')
<div class="container-fluid"><h4>Attendance Payroll Summary</h4>
<form method="GET" class="row g-2 mb-3"><div class="col-auto"><input type="month" name="month" value="{{ $month }}" class="form-control"></div><div class="col-auto"><button class="btn btn-primary">View</button></div></form>
<div class="table-responsive"><table class="table table-bordered table-striped">
<thead><tr><th>Teacher</th><th>Present</th><th>Absent</th><th>Late</th><th>Half Day</th><th>On Leave</th><th>Marked Days</th></tr></thead><tbody>
@forelse($users as $user)
@php $items=$rows->get($user->id,collect()); @endphp
<tr><td>{{ $user->name }}</td>
@foreach(['present','absent','late','half_day','on_leave'] as $status)
<td>{{ $items->where('status',$status)->count() }}</td>
@endforeach
<td>{{ $items->count() }}</td></tr>
@empty
<tr><td colspan="7" class="text-center">No teacher records found.</td></tr>
@endforelse
</tbody></table></div>
<p class="text-muted">Attendance-only summary. No gross/net salary, paid-day proration, tax deduction, or payslip calculation is performed.</p>
</div>
@endsection
