@extends('layouts.admin')
@section('title','Payslips')
@section('content')
<div class="container-fluid"><h4>{{ $isAdmin?'Employee Payslips':'My Payslips' }}</h4>
<form method="GET" class="d-flex gap-2 mb-3"><input type="month" name="pay_month" value="{{ request('pay_month') }}" class="form-control" style="max-width:210px"><button class="btn btn-outline-primary">Filter</button></form>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Month</th><th>Employee</th><th>Gross</th><th>Paid Days</th><th>Net Salary</th><th>Status</th><th>View</th></tr></thead><tbody>
@forelse($slips as $s)<tr><td>{{ $s->pay_month }}</td><td>{{ $s->name }}</td><td>₹{{ $s->gross_salary }}</td><td>{{ $s->paid_days }}/{{ $s->working_days }}</td><td>₹{{ $s->net_salary }}</td><td>{{ ucfirst($s->run_status) }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('hr-payroll.processing.slips.show',$s->id) }}">View / Print</a></td></tr>
@empty<tr><td colspan="7">No payslips available.</td></tr>@endforelse
</tbody></table></div>{{ $slips->links() }}</div>
@endsection
