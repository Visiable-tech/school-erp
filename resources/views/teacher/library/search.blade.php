@extends('layouts.admin')
@section('title','Search In Library')
@section('content')
<div class="container-fluid">
<h4 class="mb-3">Search In Library</h4>
<form method="GET" action="{{ route('teacher.library.search') }}" class="row g-2 mb-3">
<div class="col-md-8"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Title, author, ISBN or category"></div>
<div class="col-auto"><button class="btn btn-primary">Search</button></div>
</form>
<div class="table-responsive"><table class="table table-bordered table-striped align-middle">
<thead><tr><th>Title</th><th>Author</th><th>ISBN</th><th>Category</th><th>Shelf</th><th>Available / Total</th></tr></thead><tbody>
@forelse($books as $book)
<tr><td>{{ $book->title }}</td><td>{{ $book->author ?? '—' }}</td><td>{{ $book->isbn ?? '—' }}</td><td>{{ $book->category ?? '—' }}</td><td>{{ $book->shelf ?? '—' }}</td><td>{{ $book->available_copies }} / {{ $book->total_copies }}</td></tr>
@empty
<tr><td colspan="6" class="text-center">No resources found.</td></tr>
@endforelse
</tbody></table></div>
{{ $books->links() }}
</div>
@endsection
