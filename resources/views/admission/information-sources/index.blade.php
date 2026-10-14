@extends('layouts.admin')

@section('title', 'Sources Of Information')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Sources Of Information</h5>

            @can('admission-information-source.create')
                <a href="{{ route('admission-information-sources.create') }}"
                   class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Add Source
                </a>
            @endcan
        </div>

        <div class="card-body">

            <form method="GET"
                  action="{{ route('admission-information-sources.index') }}"
                  class="row g-2 mb-3">

                <div class="col-md-6">
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           class="form-control"
                           placeholder="Search source name or code">
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="1" @selected(request('status') === '1')>Active</option>
                        <option value="0" @selected(request('status') === '0')>Inactive</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('admission-information-sources.index') }}"
                       class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="65">#</th>
                            <th>Source Name</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th width="100">Order</th>
                            <th width="110">Status</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($sources as $source)
                            <tr>
                                <td>{{ $sources->firstItem() + $loop->index }}</td>
                                <td><strong>{{ $source->name }}</strong></td>
                                <td>{{ $source->code ?: '-' }}</td>
                                <td>{{ $source->description ?: '-' }}</td>
                                <td>{{ $source->sort_order }}</td>

                                <td>
                                    @if($source->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex gap-1">
                                        @can('admission-information-source.edit')
                                            <a href="{{ route('admission-information-sources.edit', $source) }}"
                                               class="btn btn-warning btn-sm">Edit</a>
                                        @endcan

                                        @can('admission-information-source.delete')
                                            <form method="POST"
                                                  action="{{ route('admission-information-sources.destroy', $source) }}"
                                                  onsubmit="return confirm('Delete this source of information?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No sources of information found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $sources->links() }}
            </div>

        </div>
    </div>

</div>
@endsection