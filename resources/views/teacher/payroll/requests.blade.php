@extends('layouts.admin')
@section('title',$kind==='investment'?'Investment Declarations':'Resignation Requests')
@section('content')
<div class="container-fluid">
<h4>{{ $kind==='investment'?'Investment Declarations':'Resignation Requests' }}</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card mb-3"><div class="card-header">Submit New Request</div><div class="card-body">
<form method="POST" action="{{ $kind==='investment'?route('teacher.payroll.investments.store'):route('teacher.payroll.resignations.store') }}">@csrf
<div class="row g-3">
@if($kind==='investment')
<div class="col-md-3"><label class="form-label">Financial Year</label><input name="financial_year" class="form-control" placeholder="2026-27" value="{{ old('financial_year') }}" required></div>
<div class="col-md-4"><label class="form-label">Category</label><input name="category" class="form-control" maxlength="100" value="{{ old('category') }}" required></div>
<div class="col-md-3"><label class="form-label">Declared Amount (INR)</label><input name="declared_amount" type="number" step="0.01" min="0.01" class="form-control" value="{{ old('declared_amount') }}" required></div>
<div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control">{{ old('description') }}</textarea></div>
@else
<div class="col-md-4"><label class="form-label">Requested Last Working Day</label><input type="date" name="requested_last_day" class="form-control" value="{{ old('requested_last_day') }}" required></div>
<div class="col-12"><label class="form-label">Reason</label><textarea name="reason" rows="3" class="form-control" required>{{ old('reason') }}</textarea></div>
@endif
<div class="col-12"><button class="btn btn-primary">Submit</button></div>
</div></form></div></div>
<div class="table-responsive"><table class="table table-bordered table-striped align-middle">
<thead><tr><th>ID</th><th>User ID</th><th>Details</th><th>Status</th><th>Review Note</th>@if($isAdmin)<th>Review</th>@endif</tr></thead><tbody>
@forelse($records as $item)
<tr><td>{{ $item->id }}</td><td>{{ $item->user_id }}</td>
<td>
@if($kind==='investment')
{{ $item->financial_year }} / {{ $item->category }} / ₹{{ number_format($item->declared_amount,2) }}
@if($item->description)<div>{{ $item->description }}</div>@endif
@else
Requested last day: {{ $item->requested_last_day }}<div>{{ $item->reason }}</div>
@endif
</td><td>{{ ucfirst($item->status) }}</td><td>{{ $item->review_note??'—' }}</td>
@if($isAdmin)
<td>
@if($item->status==='pending' && $item->user_id!==auth()->id())
<form method="POST" action="{{ route('teacher.payroll.review',[$kind,$item->id]) }}">@csrf
<input name="review_note" maxlength="500" class="form-control form-control-sm mb-1" placeholder="Review note">
<div class="d-flex gap-1"><button name="decision" value="approved" class="btn btn-sm btn-success">Approve</button><button name="decision" value="rejected" class="btn btn-sm btn-danger">Reject</button></div>
</form>
@endif
</td>
@endif
</tr>
@empty
<tr><td colspan="{{ $isAdmin?6:5 }}" class="text-center">No requests found.</td></tr>
@endforelse
</tbody></table></div>
{{ $records->links() }}
</div>
@endsection
