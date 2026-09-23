@extends('layouts.admin')

@section('title', 'Admission Applications')

@section('content')

<div class="mb-4">

    <h4 class="mb-1">
        Admission Applications
    </h4>

    <div class="text-muted">
        Manage submitted admission applications
    </div>

</div>


<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form method="GET">

            <div class="row g-3">

                <div class="col-md-6">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Application no, student, father, mobile"
                    >

                </div>


                <div class="col-md-3">

                    <select
                        name="application_status"
                        class="form-select"
                    >

                        <option value="">
                            All Status
                        </option>

                        @foreach([
                            'draft' => 'Draft',
                            'submitted' => 'Submitted',
                            'under_review' => 'Under Review',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            'admitted' => 'Admitted'
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    request('application_status')
                                    == $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-3">

                    <button class="btn btn-primary">
                        Filter
                    </button>

                    <a
                        href="{{ route('admission-applications.index') }}"
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

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th>Application</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Parent</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                @forelse($applications as $application)

                    <tr>

                        <td>
                            <strong>
                                {{ $application->application_no }}
                            </strong>

                            <div class="small text-muted">
                                {{ $application->enquiry?->enquiry_no }}
                            </div>
                        </td>


                        <td>
                            {{ $application->student_name }}
                        </td>


                        <td>
                            {{ $application->schoolClass?->name ?? '-' }}
                        </td>


                        <td>

                            {{ $application->father_name ?: '-' }}

                            @if($application->father_mobile)

                                <div class="small text-muted">
                                    {{ $application->father_mobile }}
                                </div>

                            @endif

                        </td>


                        <td>
                            {{
                                $application
                                    ->application_date
                                    ->format('d M Y')
                            }}
                        </td>


                        <td>

                            <span class="badge bg-secondary">

                                {{
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $application->application_status
                                        )
                                    )
                                }}

                            </span>

                        </td>


                        <td>

                            @can('admission-application.edit')

                                <a
                                    href="{{ route(
                                        'admission-applications.edit',
                                        $application
                                    ) }}"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                            @endcan

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="text-center py-5 text-muted"
                        >
                            No admission applications found.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($applications->hasPages())

            <div class="mt-3">
                {{ $applications->links() }}
            </div>

        @endif

    </div>

</div>

@endsection