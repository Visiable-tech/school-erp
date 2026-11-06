@extends('layouts.admin')
@section('title','Tickets / Tasks')
@section('content')
<div class="container-fluid"><h4>Tickets / Tasks</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('teacher.operations.tickets.store') }}" class="card mb-4"><div class="card-header">Create Ticket</div><div class="card-body row g-2">@csrf
<div class="col-md-4"><input name="title" class="form-control" placeholder="Ticket title" required></div><div class="col-md-2"><select name="priority" class="form-select"><option value="normal">Normal</option><option value="low">Low</option><option value="high">High</option></select></div><div class="col-md-5"><textarea name="description" class="form-control" placeholder="Describe the issue or task" required></textarea></div><div class="col-auto"><button class="btn btn-primary">Create</button></div></div></form>
<div class="table-responsive"><table class="table table-bordered table-striped align-middle"><thead><tr><th>ID</th><th>Title</th><th>Description</th><th>Priority</th><th>Status</th><th>Assigned To</th>@if($isAdmin)<th>Update</th>@endif</tr></thead><tbody>
@forelse($tickets as $ticket)
<tr><td>{{ $ticket->id }}</td><td>{{ $ticket->title }}</td><td>{{ $ticket->description }}</td><td>{{ ucfirst($ticket->priority) }}</td><td>{{ str_replace('_',' ',ucfirst($ticket->status)) }}</td><td>{{ $ticket->assigned_to??'Unassigned' }}</td>
@if($isAdmin)<td><form method="POST" action="{{ route('teacher.operations.tickets.update',$ticket->id) }}">@csrf<select name="assigned_to" class="form-select form-select-sm mb-1"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected($ticket->assigned_to==$user->id)>{{ $user->name }}</option>@endforeach</select><select name="status" class="form-select form-select-sm mb-1">@foreach(['open','in_progress','resolved','closed'] as $status)<option value="{{ $status }}" @selected($ticket->status===$status)>{{ ucwords(str_replace('_',' ',$status)) }}</option>@endforeach</select><button class="btn btn-sm btn-primary">Update</button></form></td>@endif
</tr>
@empty
<tr><td colspan="7" class="text-center">No tickets.</td></tr>
@endforelse
</tbody></table></div>{{ $tickets->links() }}</div>
@endsection
