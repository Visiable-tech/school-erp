@extends('layouts.admin')

@section('title', 'Section Groups')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Section Groups
        </h4>

        <div class="text-muted">
            Manage section groups by academic year
        </div>

    </div>


    @can('section-group.create')

        <a
            href="{{ route('section-groups.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Add Section Group
        </a>

    @endcan

</div>


{{-- Filter --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('section-groups.index') }}"
        >

            <div class="row align-items-end">

                <div class="col-md-5">

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
                                    request('academic_year_id')
                                    == $year->id
                                )
                            >
                                {{ $year->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-5">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-search"></i>
                        Filter
                    </button>

                    <a
                        href="{{ route('section-groups.index') }}"
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
                Section Group List
            </strong>

            <span class="text-muted">
                Total: {{ $sectionGroups->total() }}
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
                        <th>Group Name</th>
                        <th>Sections</th>
                        <th>Section Count</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($sectionGroups as $group)

                        <tr>

                            <td>
                                {{
                                    $sectionGroups->firstItem()
                                    + $loop->index
                                }}
                            </td>


                            <td>

                                {{ $group->academicYear?->name ?? '-' }}

                                @if(
                                    $group->academicYear?->is_current
                                )

                                    <span class="badge bg-primary">
                                        Current
                                    </span>

                                @endif

                            </td>


                            <td>

                                <strong>
                                    {{ $group->name }}
                                </strong>

                            </td>


                            <td>

                                @foreach(
                                    $group->sections->take(4)
                                    as $section
                                )

                                    <span class="badge bg-light text-dark border mb-1">

                                        {{
                                            $section
                                                ->schoolClass
                                                ?->name ?? ''
                                        }}

                                        -

                                        {{ $section->name }}

                                    </span>

                                @endforeach


                                @if(
                                    $group->sections->count() > 4
                                )

                                    <span class="badge bg-secondary">

                                        +{{
                                            $group
                                                ->sections
                                                ->count() - 4
                                        }}

                                    </span>

                                @endif

                            </td>


                            <td>

                                <span class="badge bg-info text-dark">

                                    {{ $group->sections_count }}

                                </span>

                            </td>


                            <td>

                                @if($group->status)

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

                                @can('section-group.edit')

                                    <a
                                        href="{{ route(
                                            'section-groups.edit',
                                            $group
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('section-group.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'section-groups.destroy',
                                            $group
                                        ) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this section group?')"
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
                                colspan="7"
                                class="text-center py-5 text-muted"
                            >

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                No section groups found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($sectionGroups->hasPages())

            <div class="mt-3">
                {{ $sectionGroups->links() }}
            </div>

        @endif

    </div>

</div>

@endsection