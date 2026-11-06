@extends('layouts.admin')
@section('title','New Assessment')
@section('content')
<div class="container-fluid"><h4>New {{ ucfirst($type) }} Assessment</h4>
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('teacher.performance.store',$type) }}">@csrf
<div class="card"><div class="card-body"><div class="row g-3">
@if($type==='team')
<div class="col-md-6"><label class="form-label">Teacher</label><select name="teacher_user_id" class="form-select" required>
<option value="">Select teacher</option>
@foreach($teachers as $teacher)
<option value="{{ $teacher->id }}" @selected(old('teacher_user_id')==$teacher->id)>{{ $teacher->name }}</option>
@endforeach
</select></div>
@endif
<div class="col-md-6"><label class="form-label">Assessment Month</label><input type="month" name="period" value="{{ old('period',date('Y-m')) }}" class="form-control" required></div>
@foreach(['teaching_rating'=>'Teaching Effectiveness','classroom_rating'=>'Classroom Management','communication_rating'=>'Communication','professionalism_rating'=>'Professionalism'] as $field=>$label)
<div class="col-md-6"><label class="form-label">{{ $label }} (1–5)</label>
<select name="{{ $field }}" class="form-select" required>
@for($i=1;$i<=5;$i++)
<option value="{{ $i }}" @selected(old($field)==$i)>{{ $i }} / 5</option>
@endfor
</select></div>
@endforeach
<div class="col-12"><label class="form-label">Strengths</label><textarea name="strengths" class="form-control" rows="3" required>{{ old('strengths') }}</textarea></div>
<div class="col-12"><label class="form-label">Areas for Improvement</label><textarea name="improvement_areas" class="form-control" rows="3" required>{{ old('improvement_areas') }}</textarea></div>
<div class="col-12"><label class="form-label">Goals / Action Plan</label><textarea name="goals" class="form-control" rows="3">{{ old('goals') }}</textarea></div>
<div class="col-12 d-flex gap-2"><button name="action" value="draft" class="btn btn-outline-secondary">Save Draft</button><button name="action" value="submit" class="btn btn-primary">Submit Assessment</button></div>
</div></div></div></form>
</div>
@endsection
