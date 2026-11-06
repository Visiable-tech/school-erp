@extends('layouts.admin')
@section('title','Monthly Overtime')
@section('content')
<div class="container-fluid"><h4>Monthly Overtime Report</h4>
<form method="GET" class="d-flex gap-2 mb-3"><input type="month" name="month" value="{{ $month }}" class="form-control" style="max-width:220px"><button class="btn btn-outline-primary">View</button></form>
<div class="alert alert-info">Overtime minutes are recorded for reporting only. Overtime payment rates and payroll adjustments must be reviewed and entered separately.</div>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Date</th><th>Employee</th><th>Overtime Minutes</th><th>Note</th></tr></thead><tbody>
@forelse($rows as $row)
<tr><td>{{ $row->attendance_date }}</td><td>{{ $row->name }}</td><td>{{ $row->overtime_minutes }}</td><td>{{ $row->note??'—' }}</td></tr>
@empty
<tr><td colspan="4">No overtime recorded.</td></tr>
@endforelse
</tbody></table></div>{{ $rows->links() }}</div>
@endsection
