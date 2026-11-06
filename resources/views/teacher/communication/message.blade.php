@extends('layouts.admin')
@section('title','Message')
@section('content')
<div class="container-fluid"><div class="card"><div class="card-header"><strong>{{ $row->subject }}</strong></div>
<div class="card-body"><p class="text-muted">Sent: {{ $row->created_at }}</p>
<div style="white-space:pre-wrap">{{ $row->body }}</div></div></div>
<a class="btn btn-secondary mt-3" href="{{ route('teacher.communication.inbox') }}">Back to Inbox</a></div>
@endsection
