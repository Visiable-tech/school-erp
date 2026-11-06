@extends('layouts.admin')
@section('title',$mode==='issued'?'Issued Resources':'Issue/Return History')
@section('content')
<div class="container-fluid">
<h4 class="mb-3">{{ $mode==='issued'?'Issued Resources':'Issue/Return History' }}</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-responsive"><table class="table table-bordered table-striped align-middle">
<thead><tr><th>Book</th><th>Borrower User ID</th><th>Issued</th><th>Due</th><th>Returned</th><th>Status</th>@hasanyrole('Admin|Super Admin')<th>Action</th>@endhasanyrole</tr></thead><tbody>
@forelse($loans as $loan)
<tr><td>{{ $loan->title }}</td><td>{{ $loan->borrower_user_id }}</td><td>{{ $loan->issued_on }}</td><td>{{ $loan->due_on }}</td>
<td>{{ $loan->returned_on ?? '—' }}</td><td>{{ $loan->returned_on?'Returned':(now()->toDateString()>$loan->due_on?'Overdue':'Issued') }}</td>
@hasanyrole('Admin|Super Admin')
<td>
@if(!$loan->returned_on)
<form method="POST" action="{{ route('teacher.library.return',$loan->id) }}">@csrf
<div class="d-flex gap-1"><input type="date" name="returned_on" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required><button class="btn btn-sm btn-success">Return</button></div>
</form>
@endif
</td>
@endhasanyrole
</tr>
@empty
<tr><td colspan="7" class="text-center">No issue records found.</td></tr>
@endforelse
</tbody></table></div>
{{ $loans->links() }}
</div>
@endsection
