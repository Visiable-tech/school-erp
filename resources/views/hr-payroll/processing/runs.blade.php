@extends('layouts.admin')
@section('title','Payroll Runs')
@section('content')
<div class="container-fluid"><h4>Salary Months / Payroll Runs</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ implode(' | ',$errors->all()) }}</div>@endif
<div class="alert alert-warning"><strong>Important:</strong> Paid days are applied uniformly to every active salary profile in this version. Confirm attendance, unpaid leave, statutory deductions and salary structures before finalizing. Finalization is irreversible in the UI.</div>
<div class="card mb-3"><div class="card-header">Generate / Regenerate Draft Payroll</div><div class="card-body"><form method="POST" action="{{ route('hr-payroll.processing.runs.generate') }}" class="row g-2">@csrf
<div class="col-md-3"><label>Salary Month</label><input type="month" name="pay_month" value="{{ date('Y-m') }}" class="form-control" required></div>
<div class="col-md-3"><label>Working Days</label><input type="number" name="working_days" value="30" min="1" max="31" class="form-control" required></div>
<div class="col-md-3"><label>Default Paid Days</label><input type="number" name="default_paid_days" value="30" min="0" max="31" class="form-control" required></div>
<div class="col-md-3 d-flex align-items-end"><button class="btn btn-primary">Generate Draft</button></div>
</form></div></div>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Month</th><th>Working Days</th><th>Employees</th><th>Net Total (₹)</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($runs as $run)<tr><td>{{ $run->pay_month }}</td><td>{{ $run->working_days }}</td><td>{{ $run->employee_count }}</td><td>{{ number_format($run->net_total,2) }}</td><td>{{ ucfirst($run->status) }}</td><td>
<a class="btn btn-sm btn-outline-primary" href="{{ route('hr-payroll.processing.slips',['pay_month'=>$run->pay_month]) }}">Review Payslips</a>
@if($run->status==='draft')<form method="POST" action="{{ route('hr-payroll.processing.runs.finalize',$run->id) }}" class="d-inline" onsubmit="return confirm('Finalize this payroll? Review all payslips first. This action locks the run.')">@csrf<button class="btn btn-sm btn-danger">Finalize</button></form>@endif
</td></tr>@empty<tr><td colspan="6">No payroll runs.</td></tr>@endforelse
</tbody></table></div>{{ $runs->links() }}</div>
@endsection
