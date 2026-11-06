@extends('layouts.admin')
@section('title',$type==='self'?'Self Assessment':"Team's Assessment")
@section('content')
<div class="container-fluid">
<div class="d-flex justify-content-between align-items-center mb-3">
<h4>{{ $type==='self'?'Self Assessment':"Team's Assessment" }}</h4>
<a class="btn btn-primary" href="{{ route('teacher.performance.create',$type) }}">New Assessment</a>
</div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-responsive"><table class="table table-bordered table-striped align-middle">
<thead><tr><th>Period</th><th>Teacher User ID</th><th>Average Rating</th><th>Status</th><th>Action</th></tr></thead><tbody>
@forelse($reviews as $item)
<tr><td>{{ $item->period }}</td><td>{{ $item->teacher_user_id }}</td>
<td>{{ number_format(($item->teaching_rating+$item->classroom_rating+$item->communication_rating+$item->professionalism_rating)/4,2) }} / 5</td>
<td>{{ ucfirst($item->status) }}</td>
<td><a class="btn btn-sm btn-outline-primary" href="{{ route('teacher.performance.show',$item->id) }}">View</a></td></tr>
@empty
<tr><td colspan="5" class="text-center">No assessments found.</td></tr>
@endforelse
</tbody></table></div>{{ $reviews->links() }}
</div>
@endsection
