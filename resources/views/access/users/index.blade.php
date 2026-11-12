@extends('layouts.admin')
@section('title','User Management')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4>User Management</h4><a class="btn btn-primary" href="{{ route('access.users.create') }}">Add User</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card border-0 shadow-sm"><div class="card-body table-responsive"><table class="table table-bordered table-striped align-middle"><thead><tr><th>Name</th><th>Email</th><th>School</th><th>Roles</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->school?->school_name ?: '—' }}</td><td>{{ $user->roles->pluck('name')->join(', ') }}</td><td><span class="badge bg-{{ $user->status?'success':'secondary' }}">{{ $user->status?'Active':'Inactive' }}</span></td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('access.users.edit',$user) }}">Edit / Assign Roles</a></td></tr>
@empty<tr><td colspan="6" class="text-center">No users found</td></tr>@endforelse
</tbody></table>{{ $users->links() }}</div></div>
@endsection