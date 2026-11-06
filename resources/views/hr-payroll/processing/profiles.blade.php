@extends('layouts.admin')
@section('title','Salary Structures')
@section('content')
<div class="container-fluid"><h4>Employee Salary Structures</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ implode(' | ',$errors->all()) }}</div>@endif
<div class="card mb-3"><div class="card-header">Add / Update Salary Structure</div><div class="card-body">
<form method="POST" action="{{ route('hr-payroll.processing.profiles.save') }}" class="row g-3">@csrf
<div class="col-md-6"><label>Employee</label><select name="user_id" class="form-select" required><option value="">Select employee</option>
@foreach($staff as $person)<option value="{{ $person->id }}">{{ $person->name }} ({{ $person->email }})</option>@endforeach
</select></div>
<div class="col-md-3"><label>Status</label><select name="is_active" class="form-select"><option value="1">Active</option><option value="0">Inactive</option></select></div>
@foreach(['basic'=>'Basic','hra'=>'HRA','allowance'=>'Allowances','other_earning'=>'Other Earnings','pf'=>'PF (manual)','professional_tax'=>'Professional Tax (manual)','tds'=>'TDS (manual)','other_deduction'=>'Other Deduction'] as $field=>$label)
<div class="col-md-3"><label>{{ $label }} (₹ / month)</label><input type="number" name="{{ $field }}" value="{{ old($field,0) }}" min="0" step="0.01" class="form-control" required></div>
@endforeach
<div class="col-12"><button class="btn btn-primary">Save Structure</button></div>
</form></div></div>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Employee</th><th>Basic</th><th>HRA</th><th>Allowances</th><th>Other Earnings</th><th>PF</th><th>PT</th><th>TDS</th><th>Other Ded.</th><th>Active</th></tr></thead><tbody>
@forelse($profiles as $p)<tr><td>{{ $p->name }}</td><td>{{ $p->basic }}</td><td>{{ $p->hra }}</td><td>{{ $p->allowance }}</td><td>{{ $p->other_earning }}</td><td>{{ $p->pf }}</td><td>{{ $p->professional_tax }}</td><td>{{ $p->tds }}</td><td>{{ $p->other_deduction }}</td><td>{{ $p->is_active?'Yes':'No' }}</td></tr>
@empty<tr><td colspan="10">No salary structures yet.</td></tr>@endforelse
</tbody></table></div>{{ $profiles->links() }}
<p class="text-muted small">Select an employee and enter all monthly amounts to replace their structure. Statutory deductions must be entered manually and independently verified.</p></div>
@endsection
