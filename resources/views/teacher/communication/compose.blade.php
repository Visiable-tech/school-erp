@extends('layouts.admin')
@section('title','Compose Message')
@section('content')
<div class="container-fluid"><h4>Compose Message</h4>
 @if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
 <form method="POST" action="{{ route('teacher.communication.send') }}">@csrf
 <div class="card"><div class="card-body">
 <div class="mb-3"><label class="form-label">Recipient</label><select name="recipient_user_id" class="form-select" required><option value="">Select user</option>
 @foreach($users as $user)<option value="{{ $user->id }}" @selected(old('recipient_user_id')==$user->id)>{{ $user->name }} — {{ $user->email }}</option>@endforeach
 </select></div>
 <div class="mb-3"><label class="form-label">Subject</label><input class="form-control" name="subject" maxlength="180" required value="{{ old('subject') }}"></div>
 <div class="mb-3"><label class="form-label">Message</label><textarea class="form-control" name="body" rows="7" required>{{ old('body') }}</textarea></div>
 <button class="btn btn-primary">Send Message</button></div></div></form>
 <p class="text-muted mt-3">Messages are delivered inside this ERP only; no email, SMS, or WhatsApp is sent.</p>
</div>
@endsection
