@extends('layouts.admin')

@section('title', 'Wings')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Wings</h4>

        <div class="text-muted">
            Manage academic wings of the school
        </div>
    </div>

    @can('wing.create')
        <a href="{{ route('wings.create') }}"
           class="btn btn-primary">

            <i class="bi bi-plus-lg"></i>
            Add Wing

        </a>
    @endcan

</div>


<div class="card border-0 shadow-sm">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between align-items-center">

            <strong>Wing List</strong>

            <span class="text-muted">
                Total: {{ $wings->total() }}
            </span>

        </div>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th width="70">#</th>
                        <th>Wing Name</th>
                        <th>Code</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($wings as $wing)

                        <tr>

                            <td>
                                {{ $wings->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <strong>
                                    {{ $wing->name }}
                                </strong>
                            </td>

                            <td>
                                {{ $wing->code ?: '-' }}
                            </td>

                            <td>
                                {{ $wing->sort_order }}
                            </td>

                            <td>

                                @if($wing->status)

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            <td>

                                @can('wing.edit')

                                    <a href="{{ route('wings.edit', $wing) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Edit">

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                @endcan


                                @can('wing.delete')

                                    <form
                                        method="POST"
                                        action="{{ route('wings.destroy', $wing) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this wing?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete"
                                        >

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                @endcan

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6"
                                class="text-center py-5 text-muted">

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                No wings found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($wings->hasPages())

            <div class="mt-4">

                {{ $wings->links() }}

            </div>

        @endif

    </div>

</div>

@endsection