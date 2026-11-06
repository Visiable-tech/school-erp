@extends('layouts.admin')
@section('title','My Profile')
@section('content')<h3>My Profile</h3><div class="card"><div class="card-body"><dl class="row"><dt class="col-sm-3">Name</dt><dd class="col-sm-9">{{ $teacher->name }}</dd><dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $teacher->email }}</dd><dt class="col-sm-3">School</dt><dd class="col-sm-9">{{ $teacher->school?->name }}</dd><dt class="col-sm-3">Roles</dt><dd class="col-sm-9">{{ $teacher->getRoleNames()->implode(', ') }}</dd></dl></div></div>@endsection
