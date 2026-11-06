@extends('layouts.admin')
@section('title','Salary Advances')
@section('content')
<div class="container-fluid"><h4>Salary Advances</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ implode(' | ',$errors->all()) }}</div>@endif
<div class="card mb-3"><div class="card-header">New Salary Advance</div><div class="card-body"><form method="POST" action="{{ route('hr-payroll.processing.advances.save') }}" class="row g-2">@csrf
<div class="col-md-4"><label>Employee</label><select name="user_id" class="form-select" required><option value="">Select</option>@foreach($staff as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
<div class="col-md-2"><label>Advance Amount (₹)</label><input type="number" name="amount" min="0.01" step="0.01" class="form-control" required></div>
<div class="col-md-2"><label>Monthly Recovery (₹)</label><input type="number" name="monthly_recovery" min="0.01" step="0.01" class="form-control" required></div>
<div class="col-md-4"><label>Reason</label><input name="reason" class="form-control" maxlength="500"></div>
<div class="col-12"><button class="btn btn-primary">Create Request</button></div></form></div></div>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Employee</th><th>Amount</th><th>Monthly Recovery</th><th>Recovered</th><th>Balance</th><th>Status</th><th>Review</th></tr></thead><tbody>
@forelse($advances as $a)<tr><td>{{ $a->name }}</td><td>{{ $a->amount }}</td><td>{{ $a->monthly_recovery }}</td><td>{{ $a->recovered }}</td><td>{{ number_format(max(0,(float)$a->amount-(float)$a->recovered),2) }}</td><td>{{ ucfirst($a->status) }}</td><td>
@if($a->status==='pending')<form method="POST" action="{{ route('hr-payroll.processing.advances.review',$a->id) }}">@csrf<button name="decision" value="approved" class="btn btn-sm btn-success">Approve</button> <button name="decision" value="rejected" class="btn btn-sm btn-danger">Reject</button></form>@endif
</td></tr>@empty<tr><td colspan="7">No advances.</td></tr>@endforelse
</tbody></table></div>{{ $advances->links() }}
<p class="text-muted small">Approval records authorization only. Actual bank disbursement must be handled separately. Approved advances are recovered from generated payroll, subject to available net pay.</p></div>
@endsection
