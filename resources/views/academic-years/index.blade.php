@extends('layouts.admin')

@section('title', 'Academic Years')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Academic Years</h4>

        <div class="text-muted">
            Manage school academic sessions
        </div>
    </div>

    @can('academic-year.create')

        <a
            href="{{ route('academic-years.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Add Academic Year
        </a>

    @endcan

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                <tr>
                    <th>#</th>
                    <th>Academic Year</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Current</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                </thead>

                <tbody>

                @forelse($academicYears as $academicYear)

                    <tr>

                        <td>
                            {{ $academicYears->firstItem() + $loop->index }}
                        </td>

                        <td>
                            <strong>
                                {{ $academicYear->name }}
                            </strong>
                        </td>

                        <td>
                            {{ $academicYear->start_date->format('d M Y') }}
                        </td>

                        <td>
                            {{ $academicYear->end_date->format('d M Y') }}
                        </td>

                        <td>

                            @if($academicYear->is_current)

                                <span class="badge bg-primary">
                                    Current
                                </span>

                            @else

                                -

                            @endif

                        </td>

                        <td>

                            @if($academicYear->status)

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

                            @can('academic-year.edit')

                                <a
                                    href="{{ route(
                                        'academic-years.edit',
                                        $academicYear
                                    ) }}"
                                    class="btn btn-sm btn-outline-primary"
                                >

                                    <i class="bi bi-pencil"></i>

                                </a>

                            @endcan


                            @can('academic-year.delete')

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'academic-years.destroy',
                                        $academicYear
                                    ) }}"
                                    class="d-inline"
                                    onsubmit="return confirm(
                                        'Delete this academic year?'
                                    )"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
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
                            colspan="7"
                            class="text-center py-4 text-muted"
                        >
                            No academic years found.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        {{ $academicYears->links() }}

    </div>

</div>

@endsection