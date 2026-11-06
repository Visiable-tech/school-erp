@extends('layouts.admin')
@section('title','Upload Classwork')
@section('content')
<div class="container-fluid"><h4>{{ isset($classwork)?'Edit':'Upload' }} Classwork / Assignment</h4>
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ isset($classwork)?route('teacher.classwork.update',$classwork):route('teacher.classwork.store') }}">@csrf @if(isset($classwork))@method('PUT')@endif
<div class="card"><div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Assigned Class / Section / Subject</label>
@if(isset($classwork))<input class="form-control" disabled value="{{ $classwork->assignment?->schoolClass?->name }} / {{ $classwork->assignment?->section?->name }}"><input type="hidden" name="teacher_assignment_id" value="{{ $classwork->teacher_assignment_id }}">
@else<select name="teacher_assignment_id" required class="form-select"><option value="">Select assignment</option>@foreach($assignments as $a)<option value="{{ $a->id }}" @selected(old('teacher_assignment_id')==$a->id)>{{ $a->schoolClass?->name }} / {{ $a->section?->name }} / {{ $a->subject?->name ?? 'General' }}</option>@endforeach</select>@endif</div>
<div class="col-md-3"><label class="form-label">Work Date</label><input type="date" name="work_date" required class="form-control" value="{{ old('work_date',isset($classwork)?$classwork->work_date?->toDateString():now()->toDateString()) }}"></div>
<div class="col-md-3"><label class="form-label">Type</label><select name="type" required class="form-select">@foreach(['classwork','homework','assignment'] as $type)<option value="{{ $type }}" @selected(old('type',$classwork->type??'classwork')===$type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
<div class="col-md-8"><label class="form-label">Title</label><input class="form-control" name="title" maxlength="200" required value="{{ old('title',$classwork->title??'') }}"></div>
<div class="col-md-4"><label class="form-label">Due Date (optional)</label><input class="form-control" type="date" name="due_date" value="{{ old('due_date',isset($classwork)?$classwork->due_date?->toDateString():'') }}"></div>
<div class="col-12"><label class="form-label">Description / Instructions</label><textarea class="form-control" rows="7" name="description" required>{{ old('description',$classwork->description??'') }}</textarea></div>
</div></div><div class="card-footer"><button class="btn btn-primary">{{ isset($classwork)?'Save Changes':'Submit for Approval' }}</button> <a class="btn btn-secondary" href="{{ route('teacher.classwork.index') }}">Cancel</a></div></div></form></div>
@endsection
