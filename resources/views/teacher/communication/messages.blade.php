@extends('layouts.admin')
@section('title',ucfirst($mode))
@section('content')
<div class="container-fluid">
 <div class="d-flex justify-content-between align-items-center mb-3"><h4>{{ ucfirst($mode) }} Messages</h4><a class="btn btn-primary" href="{{ route('teacher.communication.compose') }}">Compose</a></div>
 @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
 <div class="table-responsive"><table class="table table-bordered table-striped table-hover align-middle">
 <thead><tr><th>Date</th><th>{{ $mode==='inbox'?'From':'To' }}</th><th>Subject</th><th>Status</th><th>Open</th></tr></thead><tbody>
 @forelse($messages as $message)
 <tr><td>{{ $message->created_at }}</td><td>{{ $senders[$mode==='inbox'?$message->sender_user_id:$message->recipient_user_id] ?? 'User' }}</td>
 <td>{{ $message->subject }}</td><td>{{ $message->read_at ? 'Read':'Unread' }}</td>
 <td><a href="{{ route('teacher.communication.message',$message->id) }}" class="btn btn-sm btn-outline-primary">View</a></td></tr>
 @empty
 <tr><td colspan="5" class="text-center">No messages found.</td></tr>
 @endforelse
 </tbody></table></div>
 {{ $messages->links() }}
</div>
@endsection
