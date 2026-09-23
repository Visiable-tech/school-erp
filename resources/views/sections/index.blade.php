@extends('layouts.admin')

@section('title', 'Sections')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Sections
        </h4>

        <div class="text-muted">
            Manage class sections by academic year
        </div>

    </div>

    @can('section.create')

        <a
            href="{{ route('sections.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Add Section
        </a>

    @endcan

</div>


<div class="card border-0 shadow-sm">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between">

            <strong>Section List</strong>

            <span class="text-muted">
                Total: {{ $sections->total() }}
            </span>

        </div>

    </div>


    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>#</th>
                        <th>Academic Year</th>
                        <th>Wing</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($sections as $section)

                        <tr>

                            <td>
                                {{ $sections->firstItem() + $loop->index }}
                            </td>

                            <td>

                                {{ $section->academicYear->name ?? '-' }}

                                @if($section->academicYear?->is_current)

                                    <span class="badge bg-primary ms-1">
                                        Current
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $section->schoolClass?->wing?->name ?? '-' }}
                            </td>

                            <td>
                                <strong>
                                    {{ $section->schoolClass->name ?? '-' }}
                                </strong>
                            </td>

                            <td>

                                <span class="badge bg-light text-dark border">
                                    {{ $section->name }}
                                </span>

                            </td>

                            <td>
                                {{ $section->capacity ?: '-' }}
                            </td>

                            <td>

                                @if($section->status)

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

                                @can('section.edit')

                                    <a
                                        href="{{ route(
                                            'sections.edit',
                                            $section
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('section.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'sections.destroy',
                                            $section
                                        ) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this section?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
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
                                colspan="8"
                                class="text-center py-5 text-muted"
                            >

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                No sections found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($sections->hasPages())

            <div class="mt-4">
                {{ $sections->links() }}
            </div>

        @endif

    </div>

</div>

@endsection