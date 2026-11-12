@extends('layouts.admin')
@section('title','Roles & Permissions')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4>Roles & Permissions</h4><a class="btn btn-primary" href="{{ route('access.roles.create') }}">Add Role</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card shadow-sm border-0"><div class="card-body table-responsive"><table class="table table-bordered table-striped align-middle"><thead><tr><th>Role</th><th>Permissions</th><th>Users</th><th>Action</th></tr></thead><tbody>
@foreach($roles as $role)<tr><td>{{ $role->name }}</td><td>{{ $role->permissions_count }}</td><td>{{ $role->users_count }}</td><td>
@if($role->name !== 'Super Admin' || auth()->user()->hasRole('Super Admin'))
<a class="btn btn-sm btn-outline-primary" href="{{ route('access.roles.edit',$role) }}">Permissions</a>
@endif
@if($role->name !== 'Super Admin' && !$role->users_count)
<form method="POST" class="d-inline" action="{{ route('access.roles.destroy',$role) }}" onsubmit="return confirm('Delete this role?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
@endif
</td></tr>@endforeach
</tbody></table></div></div>
@endsection