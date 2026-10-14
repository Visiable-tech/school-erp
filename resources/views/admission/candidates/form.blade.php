@extends('layouts.admin')
@section('title',isset($candidate)?'Edit Candidate':'Add Candidate')
@section('content')
<div class="container-fluid"><div class="card shadow-sm"><div class="card-header"><h5 class="mb-0">{{ isset($candidate)?'Edit Candidate':'Add Candidate' }}</h5></div><div class="card-body">
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" enctype="multipart/form-data" action="{{ isset($candidate)?route('admission-candidates.update',$candidate->id):route('admission-candidates.store') }}">@csrf @if(isset($candidate))@method('PUT')@endif
<div class="row g-3">
@foreach(['candidate_name'=>'Candidate Name','father_name'=>'Father’s Name','mother_name'=>'Mother’s Name','mobile'=>'Mobile Number'] as $field=>$label)<div class="col-md-6"><label class="form-label">{{ $label }} @if($field==='candidate_name')<span class="text-danger">*</span>@endif</label><input class="form-control" name="{{ $field }}" value="{{ old($field,$candidate->$field??'') }}" @if($field==='candidate_name')required@endif></div>@endforeach
<div class="col-md-4"><label class="form-label">Date of Birth</label><input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth',isset($candidate)?$candidate->date_of_birth?->format('Y-m-d'):'') }}"></div>
<div class="col-md-4"><label class="form-label">Academic Year</label><select name="academic_year_id" class="form-select"><option value="">Select</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected((string)old('academic_year_id',$candidate->academic_year_id??'')===(string)$year->id)>{{ $year->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Class</label><select name="school_class_id" class="form-select"><option value="">Select</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected((string)old('school_class_id',$candidate->school_class_id??'')===(string)$class->id)>{{ $class->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Photograph (JPG/PNG/WebP, max 2 MB)</label><input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="form-control"></div>
</div><div class="mt-4 d-flex gap-2"><button class="btn btn-primary">Save</button><a class="btn btn-secondary" href="{{ route('admission-candidates.index') }}">Cancel</a></div></form>
</div></div></div>
@endsection
