@extends('layouts.admin')
@section('title',ucfirst($type))
@section('content')
<div class="container-fluid">
<div class="d-flex justify-content-between mb-3"><h4>{{ ucfirst($type) }}s</h4><a href="{{ route('teacher.communication.notices.create',$type) }}" class="btn btn-primary">New {{ ucfirst($type) }}</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle"><thead><tr><th>Date</th><th>Title</th><th>Content</th><th>Section</th></tr></thead><tbody>
@forelse($notices as $notice)
<tr><td>{{ $notice->published_on }}</td><td>{{ $notice->title }}</td><td><div style="white-space:pre-wrap">{{ $notice->body }}</div></td><td>{{ $notice->section_id ?? 'School-wide' }}</td></tr>
@empty
<tr><td colspan="4" class="text-center">No notices found.</td></tr>
@endforelse
</tbody></table></div>
{{ $notices->links() }}
</div>
@endsection
