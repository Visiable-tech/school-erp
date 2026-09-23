@extends('layouts.admin')

@section('title', 'Classes')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Classes
        </h4>

        <div class="text-muted">
            Manage school classes and their wings
        </div>

    </div>


    @can('class.create')

        <a
            href="{{ route('school-classes.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Add Class
        </a>

    @endcan

</div>


<div class="card border-0 shadow-sm">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between">

            <strong>Class List</strong>

            <span class="text-muted">
                Total: {{ $classes->total() }}
            </span>

        </div>

    </div>


    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th width="70">#</th>
                        <th>Class</th>
                        <th>Code</th>
                        <th>Wing</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($classes as $schoolClass)

                        <tr>

                            <td>
                                {{ $classes->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <strong>
                                    {{ $schoolClass->name }}
                                </strong>
                            </td>

                            <td>
                                {{ $schoolClass->code ?: '-' }}
                            </td>

                            <td>

                                @if($schoolClass->wing)

                                    <span class="badge bg-light text-dark border">
                                        {{ $schoolClass->wing->name }}
                                    </span>

                                @else

                                    -

                                @endif

                            </td>

                            <td>
                                {{ $schoolClass->sort_order }}
                            </td>

                            <td>

                                @if($schoolClass->status)

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

                                @can('class.edit')

                                    <a
                                        href="{{ route(
                                            'school-classes.edit',
                                            $schoolClass
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('class.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'school-classes.destroy',
                                            $schoolClass
                                        ) }}"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                            'Are you sure you want to delete this class?'
                                        )"
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

                            <td
                                colspan="7"
                                class="text-center py-5 text-muted"
                            >

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                No classes found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($classes->hasPages())

            <div class="mt-4">
                {{ $classes->links() }}
            </div>

        @endif

    </div>

</div>

@endsection