@extends('layouts.admin')
@section('title','Publish Notice')
@section('content')
<div class="container-fluid"><h4>Publish {{ ucfirst($type) }}</h4>
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('teacher.communication.notices.store',$type) }}">@csrf
<div class="card"><div class="card-body">
<div class="mb-3"><label class="form-label">Title</label><input class="form-control" name="title" maxlength="180" value="{{ old('title') }}" required></div>
<div class="mb-3"><label class="form-label">Publish Date</label><input type="date" class="form-control" name="published_on" value="{{ old('published_on',date('Y-m-d')) }}" required></div>
@if($type==='broadcast')
<div class="mb-3"><label class="form-label">Assigned Section</label><select name="section_id" class="form-select" required><option value="">Select section</option>
@foreach($assignments->unique('section_id') as $a)<option value="{{ $a->section_id }}" @selected(old('section_id')==$a->section_id)>Year {{ $a->academic_year_id }} / Class {{ $a->school_class_id }} / Section {{ $a->section_id }}</option>@endforeach
</select></div>
@else
<p class="text-muted">School-wide publishing is restricted to Admin / Super Admin.</p>
@endif
<div class="mb-3"><label class="form-label">Content</label><textarea class="form-control" name="body" rows="8" required>{{ old('body') }}</textarea></div>
<button class="btn btn-primary">Publish</button></div></div></form>
</div>
@endsection
