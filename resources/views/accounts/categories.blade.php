@extends('accounts.shell')
@section('accounts_content')
<div class="card card-body mb-3"><h5>Add expense category</h5><form method="POST" action="{{ route('accounts.categories.store') }}" class="d-flex gap-2">@csrf<input class="form-control" name="name" required maxlength="150" placeholder="Electricity, stationery, maintenance..." value="{{ old('name') }}"><button class="btn btn-success">Add</button></form></div><div class="card card-body"><table class="table table-striped"><thead><tr><th>ID</th><th>Category</th><th>Status</th></tr></thead><tbody>@foreach($categories as $c)<tr><td>{{ $c->id }}</td><td>{{ $c->name }}</td><td>{{ $c->active?'Active':'Inactive' }}</td></tr>@endforeach</tbody></table></div>
@endsection
