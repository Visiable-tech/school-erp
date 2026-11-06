@extends('layouts.admin')
@section('title','Create Assessment')
@section('content')
<div class="container-fluid"><h4>Create Assessment</h4>
 @if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
 <form method="POST" action="{{ route('teacher.examination.store') }}">@csrf
 <div class="card"><div class="card-body row g-3">
 <div class="col-md-12"><label class="form-label">Assigned Class / Section / Subject</label><select class="form-select" name="teacher_assignment_id" required><option value="">Select Assignment</option>@foreach($assignments as $a)<option value="{{ $a->id }}" @selected(old('teacher_assignment_id')==$a->id)>{{ $a->academicYear?->name }} — {{ $a->schoolClass?->name }} / {{ $a->section?->name }} / {{ $a->subject?->name ?? 'General' }}</option>@endforeach</select></div>
 <div class="col-md-6"><label class="form-label">Term</label><input class="form-control" name="term" maxlength="80" value="{{ old('term','Term 1') }}" required></div>
 <div class="col-md-6"><label class="form-label">Assessment Title</label><input class="form-control" name="title" value="{{ old('title') }}" required></div>
 <div class="col-md-6"><label class="form-label">Assessment Date</label><input type="date" class="form-control" name="assessment_date" value="{{ old('assessment_date',date('Y-m-d')) }}" required></div>
 <div class="col-md-6"><label class="form-label">Maximum Marks</label><input type="number" min="0.01" step="0.01" class="form-control" name="max_marks" value="{{ old('max_marks',100) }}" required></div>
 </div><div class="card-footer"><button class="btn btn-primary">Create</button> <a href="{{ route('teacher.examination.index') }}" class="btn btn-secondary">Back</a></div></div></form></div>
@endsection
