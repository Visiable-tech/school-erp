@extends('layouts.admin')
@section('title','Manage Library Catalog')
@section('content')
<div class="container-fluid"><h4>Manage Library Catalog</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('teacher.library.catalog.store') }}">@csrf
<div class="card mb-3"><div class="card-body"><div class="row g-2">
<div class="col-md-4"><input name="title" class="form-control" placeholder="Book title *" required value="{{ old('title') }}"></div>
<div class="col-md-3"><input name="author" class="form-control" placeholder="Author" value="{{ old('author') }}"></div>
<div class="col-md-2"><input name="isbn" class="form-control" placeholder="ISBN" value="{{ old('isbn') }}"></div>
<div class="col-md-3"><input name="category" class="form-control" placeholder="Category" value="{{ old('category') }}"></div>
<div class="col-md-3"><input name="shelf" class="form-control" placeholder="Shelf" value="{{ old('shelf') }}"></div>
<div class="col-md-2"><input name="total_copies" class="form-control" type="number" min="1" value="{{ old('total_copies',1) }}" required></div>
<div class="col-auto"><button class="btn btn-primary">Add Resource</button></div>
</div></div></div></form>
<div class="table-responsive"><table class="table table-bordered table-striped">
<thead><tr><th>Title</th><th>Author</th><th>Category</th><th>Available</th><th>Total</th></tr></thead><tbody>
@forelse($books as $book)
<tr><td>{{ $book->title }}</td><td>{{ $book->author }}</td><td>{{ $book->category }}</td><td>{{ $book->available_copies }}</td><td>{{ $book->total_copies }}</td></tr>
@empty
<tr><td colspan="5" class="text-center">Catalog empty.</td></tr>
@endforelse
</tbody></table></div>{{ $books->links() }}
</div>
@endsection
