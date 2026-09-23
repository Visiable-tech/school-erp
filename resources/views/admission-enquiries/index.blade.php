@extends('layouts.admin')

@section('title', 'Admission Enquiries')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Admission Enquiries
        </h4>

        <div class="text-muted">
            Manage prospective student enquiries
        </div>

    </div>


    @can('admission-enquiry.create')

        <a
            href="{{ route('admission-enquiries.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Add Enquiry
        </a>

    @endcan

</div>


<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('admission-enquiries.index') }}"
        >

            <div class="row g-3">

                <div class="col-md-4">

                    <label class="form-label">
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Enquiry No, student, mobile..."
                    >

                </div>


                <div class="col-md-2">

                    <label class="form-label">
                        Session
                    </label>

                    <select
                        name="admission_session_id"
                        class="form-select"
                    >

                        <option value="">
                            All
                        </option>

                        @foreach($sessions as $session)

                            <option
                                value="{{ $session->id }}"
                                @selected(
                                    request('admission_session_id')
                                    == $session->id
                                )
                            >
                                {{ $session->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-2">

                    <label class="form-label">
                        Class
                    </label>

                    <select
                        name="school_class_id"
                        class="form-select"
                    >

                        <option value="">
                            All
                        </option>

                        @foreach($classes as $class)

                            <option
                                value="{{ $class->id }}"
                                @selected(
                                    request('school_class_id')
                                    == $class->id
                                )
                            >
                                {{ $class->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-2">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="enquiry_status"
                        class="form-select"
                    >

                        <option value="">
                            All
                        </option>

                        @foreach([
                            'open' => 'Open',
                            'follow_up' => 'Follow Up',
                            'interested' => 'Interested',
                            'not_interested' => 'Not Interested',
                            'applied' => 'Applied',
                            'admitted' => 'Admitted',
                            'closed' => 'Closed',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    request('enquiry_status')
                                    == $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-2 d-flex align-items-end">

                    <button
                        class="btn btn-primary me-2"
                    >
                        Filter
                    </button>

                    <a
                        href="{{ route('admission-enquiries.index') }}"
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
                        <th>Enquiry</th>
                        <th>Student</th>
                        <th>Contact</th>
                        <th>Class</th>
                        <th>Source</th>
                        <th>Follow-up</th>
                        <th>Status</th>
                        <th width="140">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($enquiries as $enquiry)

                        <tr>

                            <td>

                                <strong>
                                    {{ $enquiry->enquiry_no }}
                                </strong>

                                <div class="small text-muted">
                                    {{
                                        $enquiry
                                            ->enquiry_date
                                            ->format('d M Y')
                                    }}
                                </div>

                            </td>


                            <td>

                                <strong>
                                    {{ $enquiry->student_name }}
                                </strong>

                                @if($enquiry->father_name)

                                    <div class="small text-muted">
                                        Father:
                                        {{ $enquiry->father_name }}
                                    </div>

                                @endif

                            </td>


                            <td>

                                {{ $enquiry->mobile }}

                                @if($enquiry->email)

                                    <div class="small text-muted">
                                        {{ $enquiry->email }}
                                    </div>

                                @endif

                            </td>


                            <td>
                                {{
                                    $enquiry
                                        ->schoolClass
                                        ?->name ?? '-'
                                }}
                            </td>


                            <td>
                                {{ $enquiry->source ?: '-' }}
                            </td>


                            <td>

                                @if($enquiry->next_followup_date)

                                    {{
                                        $enquiry
                                            ->next_followup_date
                                            ->format('d M Y')
                                    }}

                                @else
                                    -
                                @endif

                            </td>


                            <td>

                                <span class="badge bg-secondary">

                                    {{
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $enquiry->enquiry_status
                                            )
                                        )
                                    }}

                                </span>

                            </td>


                            <td>


                                {{-- FOLLOW-UP --}}

                                @can('admission-followup.create')

                                    <a
                                        href="{{ route(
                                            'admission-followups.create',
                                            $enquiry
                                        ) }}"
                                        class="btn btn-sm btn-outline-success"
                                        title="Add Follow-up"
                                    >
                                        <i class="bi bi-telephone-plus"></i>
                                    </a>

                                @endcan



                                {{-- APPLICATION --}}

                                @can('admission-application.create')

                                    @if(!$enquiry->application)

                                        <a
                                            href="{{ route(
                                                'admission-applications.create',
                                                $enquiry
                                            ) }}"
                                            class="btn btn-sm btn-outline-dark"
                                            title="Create Application"
                                        >
                                            <i class="bi bi-file-earmark-plus"></i>
                                        </a>

                                    @else

                                        <a
                                            href="{{ route(
                                                'admission-applications.edit',
                                                $enquiry->application
                                            ) }}"
                                            class="btn btn-sm btn-outline-info"
                                            title="View Application"
                                        >
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>

                                    @endif

                                @endcan



                                {{-- EDIT ENQUIRY --}}

                                @can('admission-enquiry.edit')

                                    <a
                                        href="{{ route(
                                            'admission-enquiries.edit',
                                            $enquiry
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit Enquiry"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan



                                {{-- DELETE --}}

                                @can('admission-enquiry.delete')

                                    <form
                                        action="{{ route(
                                            'admission-enquiries.destroy',
                                            $enquiry
                                        ) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this enquiry?')"
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
                                colspan="8"
                                class="text-center py-5 text-muted"
                            >
                                No admission enquiries found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($enquiries->hasPages())

            <div class="mt-3">
                {{ $enquiries->links() }}
            </div>

        @endif

    </div>

</div>

@endsection