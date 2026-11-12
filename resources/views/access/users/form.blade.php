@extends('layouts.admin')
@section('title',$user ? 'Edit User' : 'Add User')
@section('content')
<h4 class="mb-3">{{ $user ? 'Edit User' : 'Add User' }}</h4>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ $user ? route('access.users.update',$user) : route('access.users.store') }}">@csrf @if($user) @method('PUT') @endif
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><strong>User Information</strong></div><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Name *</label><input name="name" class="form-control" value="{{ old('name',$user?->name) }}" required></div>
<div class="col-md-6"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email',$user?->email) }}" required></div>
@if(auth()->user()->hasRole('Super Admin'))
<div class="col-md-6"><label class="form-label">School</label><select class="form-select" name="school_id"><option value="">No school / Global</option>@foreach($schools as $school)<option value="{{ $school->id }}" @selected(old('school_id',$user?->school_id)==$school->id)>{{ $school->school_name }}</option>@endforeach</select></div>
@endif
<div class="col-md-6"><label class="form-label">Status *</label><select name="status" class="form-select"><option value="1" @selected(old('status',$user?->status??1)==1)>Active</option><option value="0" @selected(old('status',$user?->status??1)==0)>Inactive</option></select></div>
<div class="col-md-6"><label class="form-label">Password {{ $user ? '(leave blank to keep)' : '*' }}</label><input type="password" name="password" class="form-control" {{ $user?'':'required' }} autocomplete="new-password"></div>
<div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control" autocomplete="new-password"></div>
</div></div></div>
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><strong>Assign Roles *</strong></div><div class="card-body row g-3">
@foreach($roles as $role)<div class="col-md-4"><label class="form-check-label"><input type="checkbox" class="form-check-input me-1" name="roles[]" value="{{ $role->name }}" @checked(in_array($role->name,old('roles',$selected)))> {{ $role->name }}</label></div>@endforeach
</div></div>
<button class="btn btn-primary mb-5">Save User</button> <a class="btn btn-outline-secondary mb-5" href="{{ route('access.users.index') }}">Cancel</a>
</form>
@endsection