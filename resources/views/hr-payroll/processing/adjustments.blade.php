@extends('layouts.admin')
@section('title','Payroll Adjustments')
@section('content')
<div class="container-fluid"><h4>Monthly Salary Adjustments</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ implode(' | ',$errors->all()) }}</div>@endif
<div class="card mb-3"><div class="card-body"><form method="POST" action="{{ route('hr-payroll.processing.adjustments.save') }}" class="row g-2">@csrf
<div class="col-md-3"><label>Employee</label><select name="user_id" class="form-select" required><option value="">Select</option>@foreach($staff as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
<div class="col-md-2"><label>Month</label><input type="month" name="pay_month" value="{{ $month }}" class="form-control" required></div>
<div class="col-md-2"><label>Type</label><select name="type" class="form-select"><option value="earning">Earning</option><option value="deduction">Deduction</option></select></div>
<div class="col-md-3"><label>Description</label><input name="label" class="form-control" required maxlength="150"></div>
<div class="col-md-2"><label>Amount (₹)</label><input name="amount" type="number" step="0.01" min="0.01" class="form-control" required></div>
<div class="col-12"><button class="btn btn-primary">Add Adjustment</button></div></form></div></div>
<form method="GET" class="d-flex gap-2 mb-3"><input type="month" name="pay_month" value="{{ $month }}" class="form-control" style="max-width:210px"><button class="btn btn-outline-primary">Filter</button></form>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Employee</th><th>Month</th><th>Type</th><th>Label</th><th>Amount</th><th>Action</th></tr></thead><tbody>
@forelse($items as $a)<tr><td>{{ $a->name }}</td><td>{{ $a->pay_month }}</td><td>{{ ucfirst($a->type) }}</td><td>{{ $a->label }}</td><td>{{ $a->amount }}</td><td><form method="POST" action="{{ route('hr-payroll.processing.adjustments.delete',$a->id) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
@empty<tr><td colspan="6">No adjustments.</td></tr>@endforelse
</tbody></table></div>{{ $items->links() }}</div>
@endsection
