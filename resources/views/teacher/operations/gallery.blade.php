@extends('layouts.admin')
@section('title','Image Gallery')
@section('content')
<div class="container-fluid"><h4>Image Gallery</h4>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('teacher.operations.gallery.upload') }}" enctype="multipart/form-data" class="card mb-4"><div class="card-body row g-2">@csrf
<div class="col-md-4"><input class="form-control" name="title" placeholder="Image title" required></div><div class="col-md-4"><input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control" required></div><div class="col-md-3"><input name="caption" class="form-control" placeholder="Caption (optional)"></div><div class="col-auto"><button class="btn btn-primary">Upload</button></div></div></form>
<div class="row g-3">
@forelse($images as $item)
<div class="col-md-4 col-lg-3"><div class="card h-100"><img src="{{ asset('storage/'.$item->image_path) }}" class="card-img-top" style="height:180px;object-fit:cover" alt="{{ $item->title }}"><div class="card-body"><strong>{{ $item->title }}</strong><p>{{ $item->caption }}</p>
@if(auth()->id()==$item->uploaded_by || auth()->user()->hasAnyRole(['Admin','Super Admin']))
<form method="POST" action="{{ route('teacher.operations.gallery.delete',$item->id) }}" onsubmit="return confirm('Delete this image?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
@endif
</div></div></div>
@empty
<div class="col-12"><p>No images uploaded.</p></div>
@endforelse
</div>{{ $images->links() }}</div>
@endsection
