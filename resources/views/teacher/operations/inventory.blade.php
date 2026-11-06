@extends('layouts.admin')
@section('title','Inventory')
@section('content')
<div class="container-fluid"><h4>Inventory</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@if($isAdmin)
<form method="POST" action="{{ route('teacher.operations.inventory.items') }}" class="card mb-3"><div class="card-header">Add Inventory Item</div><div class="card-body row g-2">@csrf
<div class="col-md-3"><input name="name" class="form-control" placeholder="Item name" required></div><div class="col-md-2"><input name="sku" class="form-control" placeholder="SKU"></div><div class="col-md-2"><input type="number" min="0" name="quantity" class="form-control" placeholder="Quantity" required></div><div class="col-md-2"><input name="unit" value="pcs" class="form-control" required></div><div class="col-auto"><button class="btn btn-primary">Add Item</button></div></div></form>
@endif
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Item</th><th>SKU</th><th>Quantity</th><th>Unit</th></tr></thead><tbody>
@forelse($items as $item)
<tr><td>{{ $item->name }}</td><td>{{ $item->sku??'—' }}</td><td>{{ $item->quantity }}</td><td>{{ $item->unit }}</td></tr>
@empty
<tr><td colspan="4" class="text-center">No items.</td></tr>
@endforelse
</tbody></table></div>
<form method="POST" action="{{ route('teacher.operations.inventory.request') }}" class="card mb-4"><div class="card-header">Request Inventory</div><div class="card-body row g-2">@csrf
<div class="col-md-4"><select name="item_id" class="form-select" required><option value="">Select item</option>@foreach($items as $item)<option value="{{ $item->id }}">{{ $item->name }} ({{ $item->quantity }} {{ $item->unit }})</option>@endforeach</select></div>
<div class="col-md-2"><input type="number" name="quantity" min="1" class="form-control" placeholder="Quantity" required></div><div class="col-md-4"><input name="reason" class="form-control" placeholder="Reason" required></div><div class="col-auto"><button class="btn btn-primary">Request</button></div></div></form>
<h5>Inventory Requests</h5><div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Item</th><th>Requested By</th><th>Quantity</th><th>Reason</th><th>Status</th>@if($isAdmin)<th>Action</th>@endif</tr></thead><tbody>
@forelse($requests as $req)
<tr><td>{{ $req->item_name }}</td><td>{{ $req->requested_by }}</td><td>{{ $req->quantity }}</td><td>{{ $req->reason }}</td><td>{{ ucfirst($req->status) }}</td>
@if($isAdmin)<td>@if($req->status==='pending' && $req->requested_by!==auth()->id())<form method="POST" action="{{ route('teacher.operations.inventory.review',$req->id) }}">@csrf<button class="btn btn-sm btn-success" name="decision" value="approved">Approve</button> <button class="btn btn-sm btn-danger" name="decision" value="rejected">Reject</button></form>@endif</td>@endif
</tr>
@empty
<tr><td colspan="6" class="text-center">No requests.</td></tr>
@endforelse
</tbody></table></div>{{ $requests->links() }}</div>
@endsection
