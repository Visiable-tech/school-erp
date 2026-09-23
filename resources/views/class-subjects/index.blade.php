@extends('layouts.admin')

@section('title', 'Class Subject Mapping')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Class Subject Mapping
        </h4>

        <div class="text-muted">
            Subjects assigned to classes by academic year
        </div>

    </div>


    @can('class-subject.create')

        <a
            href="{{ route('class-subjects.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Assign Subjects
        </a>

    @endcan

</div>


{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('class-subjects.index') }}"
        >

            <div class="row align-items-end">

                <div class="col-md-4">

                    <label class="form-label">
                        Academic Year
                    </label>

                    <select
                        name="academic_year_id"
                        class="form-select"
                    >

                        <option value="">
                            All Academic Years
                        </option>

                        @foreach($academicYears as $year)

                            <option
                                value="{{ $year->id }}"
                                @selected(
                                    request('academic_year_id') == $year->id
                                )
                            >
                                {{ $year->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Class
                    </label>

                    <select
                        name="school_class_id"
                        class="form-select"
                    >

                        <option value="">
                            All Classes
                        </option>

                        @foreach($classes as $class)

                            <option
                                value="{{ $class->id }}"
                                @selected(
                                    request('school_class_id') == $class->id
                                )
                            >

                                {{ $class->name }}

                                @if($class->wing)
                                    - {{ $class->wing->name }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-4">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-search"></i>
                        Filter
                    </button>

                    <a
                        href="{{ route('class-subjects.index') }}"
                        class="btn btn-light"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between">

            <strong>
                Subject Mapping List
            </strong>

            <span class="text-muted">
                Total: {{ $classSubjects->total() }}
            </span>

        </div>

    </div>


    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th>#</th>
                        <th>Academic Year</th>
                        <th>Wing</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Type</th>
                        <th>Optional</th>
                        <th>Status</th>
                        <th width="100">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($classSubjects as $mapping)

                        <tr>

                            <td>
                                {{
                                    $classSubjects->firstItem()
                                    + $loop->index
                                }}
                            </td>


                            <td>

                                {{ $mapping->academicYear->name ?? '-' }}

                            </td>


                            <td>

                                {{
                                    $mapping
                                        ->schoolClass
                                        ?->wing
                                        ?->name ?? '-'
                                }}

                            </td>


                            <td>

                                <strong>
                                    {{
                                        $mapping
                                            ->schoolClass
                                            ?->name ?? '-'
                                    }}
                                </strong>

                            </td>


                            <td>

                                {{
                                    $mapping
                                        ->subject
                                        ?->name ?? '-'
                                }}

                            </td>


                            <td>

                                {{
                                    ucfirst(
                                        $mapping
                                            ->subject
                                            ?->type ?? '-'
                                    )
                                }}

                            </td>


                            <td>

                                @if($mapping->is_optional)

                                    <span class="badge bg-warning text-dark">
                                        Optional
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Compulsory
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if($mapping->status)

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

                                @can('class-subject.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'class-subjects.destroy',
                                            $mapping
                                        ) }}"
                                        onsubmit="return confirm('Remove this subject from the class?')"
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
                                colspan="9"
                                class="text-center py-5 text-muted"
                            >

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                No subject mappings found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($classSubjects->hasPages())

            <div class="mt-3">
                {{ $classSubjects->links() }}
            </div>

        @endif

    </div>

</div>

@endsection