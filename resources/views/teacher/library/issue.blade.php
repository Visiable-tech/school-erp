@extends('layouts.admin')
@section('title','Issue Library Resource')
@section('content')
<div class="container-fluid"><h4>Issue Library Resource</h4>
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('teacher.library.issue.store') }}">@csrf
<div class="card"><div class="card-body">
<div class="mb-3"><label class="form-label">Book</label><select name="book_id" class="form-select" required><option value="">Select available book</option>
@foreach($books as $book)
<option value="{{ $book->id }}" @selected(old('book_id')==$book->id)>{{ $book->title }} ({{ $book->available_copies }} available)</option>
@endforeach
</select></div>
<div class="mb-3"><label class="form-label">Borrower</label><select name="borrower_user_id" class="form-select" required><option value="">Select school user</option>
@foreach($users as $user)
<option value="{{ $user->id }}" @selected(old('borrower_user_id')==$user->id)>{{ $user->name }} — {{ $user->email }}</option>
@endforeach
</select></div>
<div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label">Issue Date</label><input type="date" name="issued_on" class="form-control" value="{{ old('issued_on',date('Y-m-d')) }}" required></div>
<div class="col-md-6"><label class="form-label">Due Date</label><input type="date" name="due_on" class="form-control" value="{{ old('due_on',date('Y-m-d',strtotime('+14 days'))) }}" required></div></div>
<div class="mb-3"><label class="form-label">Remarks (optional)</label><textarea name="remarks" class="form-control" maxlength="500">{{ old('remarks') }}</textarea></div>
<button class="btn btn-primary">Issue Book</button>
</div></div></form></div>
@endsection
