@extends('layouts.admin')
@section('title',$role ? 'Edit Role' : 'Add Role')
@section('content')
<h4 class="mb-3">{{ $role ? 'Edit Role & Permissions' : 'Create Role' }}</h4>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ $role ? route('access.roles.update',$role) : route('access.roles.store') }}">@csrf @if($role) @method('PUT') @endif
<div class="card shadow-sm border-0 mb-3"><div class="card-body"><label class="form-label">Role Name *</label><input class="form-control" name="name" value="{{ old('name',$role?->name) }}" required maxlength="125"></div></div>
@php $chosen=old('permissions',$selected); @endphp
<div class="d-flex justify-content-between align-items-center mb-3"><h5>Module Permissions</h5><button class="btn btn-outline-secondary btn-sm" type="button" onclick="document.querySelectorAll('.permission-check').forEach(x=>x.checked=true)">Select All</button></div>
<div class="row g-3">
@foreach($permissions as $module=>$items)
<div class="col-lg-6"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-white d-flex justify-content-between"><strong>{{ ucwords(str_replace('-',' ',$module)) }}</strong><label><input type="checkbox" class="module-check" onchange="this.closest('.card').querySelectorAll('.permission-check').forEach(x=>x.checked=this.checked)"> All</label></div>
<div class="card-body row g-2">
@foreach($items as $permission)
<div class="col-sm-6"><label class="form-check-label"><input class="form-check-input permission-check me-1" type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name,$chosen))> {{ substr($permission->name,strlen($module)+1) }}</label></div>
@endforeach
</div></div></div>
@endforeach
</div>
<div class="mt-4 mb-5"><button class="btn btn-primary">Save Role</button> <a class="btn btn-outline-secondary" href="{{ route('access.roles.index') }}">Cancel</a></div>
</form>
@endsection