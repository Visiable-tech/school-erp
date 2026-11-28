@extends('layouts.admin')
@section('title','Notice Details')
@section('content')
<div class="container-fluid"><a href="{{ route('teacher.student-notices.index') }}">Back to notices</a><div class="card card-body mt-3"><h4>{{ $n->title }}</h4><p class="text-muted">{{ ucfirst($n->recipient_type) }} | {{ $n->status }} | {{ $count }} recipients | {{ $n->publish_at }}</p><div class="border rounded p-3 mb-3" style="overflow-wrap:anywhere">{!! $n->body !!}</div><h5>Attachments</h5>@forelse($files as $f)<p><a href="{{ route('teacher.student-notices.download',[$n->id,$f->id]) }}">{{ $f->original_name }}</a> ({{ number_format($f->size/1024) }} KB)</p>@empty<p>No attachments.</p>@endforelse
@if($n->status==='draft')<form method="POST" action="{{ route('teacher.student-notices.publish',$n->id) }}">@csrf<button class="btn btn-primary">Publish Draft</button></form>@elseif($n->status==='published')<form method="POST" action="{{ route('teacher.student-notices.withdraw',$n->id) }}">@csrf<button class="btn btn-outline-danger">Withdraw Notice</button></form>@endif </div></div>
@endsection
