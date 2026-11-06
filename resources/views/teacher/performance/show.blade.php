@extends('layouts.admin')
@section('title','Assessment Details')
@section('content')
<div class="container-fluid"><h4>Assessment Details</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="card"><div class="card-body">
<dl class="row">
<dt class="col-md-4">Type</dt><dd class="col-md-8">{{ ucfirst($item->review_type) }}</dd>
<dt class="col-md-4">Teacher User ID</dt><dd class="col-md-8">{{ $item->teacher_user_id }}</dd>
<dt class="col-md-4">Period</dt><dd class="col-md-8">{{ $item->period }}</dd>
<dt class="col-md-4">Status</dt><dd class="col-md-8">{{ ucfirst($item->status) }}</dd>
@foreach(['teaching_rating'=>'Teaching','classroom_rating'=>'Classroom','communication_rating'=>'Communication','professionalism_rating'=>'Professionalism'] as $key=>$label)
<dt class="col-md-4">{{ $label }}</dt><dd class="col-md-8">{{ $item->$key }} / 5</dd>
@endforeach
<dt class="col-md-4">Strengths</dt><dd class="col-md-8" style="white-space:pre-wrap">{{ $item->strengths }}</dd>
<dt class="col-md-4">Improvement Areas</dt><dd class="col-md-8" style="white-space:pre-wrap">{{ $item->improvement_areas }}</dd>
<dt class="col-md-4">Goals</dt><dd class="col-md-8" style="white-space:pre-wrap">{{ $item->goals??'—' }}</dd>
<dt class="col-md-4">Review Note</dt><dd class="col-md-8">{{ $item->review_note??'—' }}</dd>
</dl>
@hasanyrole('Admin|Super Admin')
@if($item->status==='submitted' && $item->teacher_user_id!==auth()->id() && $item->reviewer_user_id!==auth()->id())
<form method="POST" action="{{ route('teacher.performance.review',$item->id) }}">@csrf
<div class="mb-3"><label class="form-label">Review Note</label><textarea name="review_note" class="form-control" rows="2"></textarea></div>
<button name="decision" value="approved" class="btn btn-success">Approve</button>
<button name="decision" value="rejected" class="btn btn-danger">Reject</button>
</form>
@endif
@endhasanyrole
</div></div>
</div>
@endsection
