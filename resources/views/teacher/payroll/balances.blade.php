@extends('layouts.admin')
@section('title','Leave Balances')
@section('content')
<div class="container-fluid"><h4>Leave Balances</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="GET" class="row g-2 mb-3">
<div class="col-auto"><input type="number" min="2000" max="2100" name="year" class="form-control" value="{{ $year }}"></div>
@if($isAdmin)
<div class="col-md-4"><select name="user_id" class="form-select">
@foreach($users as $user)<option value="{{ $user->id }}" @selected($userId==$user->id)>{{ $user->name }}</option>@endforeach
</select></div>
@endif
<div class="col-auto"><button class="btn btn-primary">View</button></div>
</form>
<div class="table-responsive"><table class="table table-bordered table-striped">
<thead><tr><th>Leave Type</th><th>Entitled Days</th><th>Approved Calendar Days</th><th>Balance</th></tr></thead><tbody>
@foreach($types as $type)
@php $ent=(float)($entitlements[$type]??0);$taken=(int)($used[$type]??0); @endphp
<tr><td>{{ ucfirst($type) }}</td><td>{{ number_format($ent,2) }}</td><td>{{ $taken }}</td><td>{{ number_format($ent-$taken,2) }}</td></tr>
@endforeach
</tbody></table></div>
@if($isAdmin)
<div class="card mb-3"><div class="card-header">Set Teacher Entitlement</div><div class="card-body">
<form method="POST" action="{{ route('teacher.payroll.balances.store') }}" class="row g-2">@csrf
<div class="col-md-3"><select name="user_id" class="form-select" required>
@foreach($users as $user)<option value="{{ $user->id }}" @selected($userId==$user->id)>{{ $user->name }}</option>@endforeach
</select></div>
<div class="col-md-2"><input type="number" name="year" value="{{ $year }}" min="2000" max="2100" class="form-control" required></div>
<div class="col-md-3"><select name="leave_type" class="form-select">
@foreach($types as $type)<option value="{{ $type }}">{{ ucfirst($type) }}</option>@endforeach
</select></div>
<div class="col-md-2"><input type="number" name="entitled_days" min="0" max="365" step="0.5" class="form-control" placeholder="Days" required></div>
<div class="col-auto"><button class="btn btn-primary">Save</button></div>
</form></div></div>
@endif
<p class="text-muted">Approved leave is counted as calendar days, including weekends/holidays. Overlapping requests may be counted twice. Negative balances are possible; this is a provisional summary, not a payroll entitlement policy engine.</p>
</div>
@endsection
