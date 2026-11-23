@extends('layouts.admin')

@section('title', 'Registrations')

@section('content')

<div class="mb-4">

    <h4 class="mb-1">
        Registrations
    </h4>

    <div class="text-muted">
        Manage registrations, approvals and confirmed admissions
    </div>

</div>


<div class="row g-3 mb-4">
    @foreach([
      ['label'=>'Total Registrations','value'=>\App\Models\AdmissionApplication::where('school_id',auth()->user()->school_id)->count(),'icon'=>'bi-journal-check','color'=>'primary'],
      ['label'=>'Awaiting Review','value'=>\App\Models\AdmissionApplication::where('school_id',auth()->user()->school_id)->whereIn('application_status',['submitted','under_review'])->count(),'icon'=>'bi-hourglass-split','color'=>'warning'],
      ['label'=>'Approved','value'=>\App\Models\AdmissionApplication::where('school_id',auth()->user()->school_id)->where('application_status','approved')->count(),'icon'=>'bi-patch-check','color'=>'success'],
      ['label'=>'Admitted','value'=>\App\Models\AdmissionApplication::where('school_id',auth()->user()->school_id)->where('application_status','admitted')->count(),'icon'=>'bi-mortarboard','color'=>'info'],
    ] as $metric)
    <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center"><div><div class="small text-muted">{{ $metric['label'] }}</div><div class="fs-2 fw-bold">{{ number_format($metric['value']) }}</div></div><div class="rounded-3 bg-{{ $metric['color'] }} bg-opacity-10 text-{{ $metric['color'] }} p-3"><i class="bi {{ $metric['icon'] }} fs-4"></i></div></div></div></div>
    @endforeach
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

                            <span class="badge bg-{{ ['approved'=>'success','admitted'=>'info','rejected'=>'danger','under_review'=>'warning','submitted'=>'primary'][$application->application_status] ?? 'secondary' }}">

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

                            @if($application->application_status === 'approved' && !$application->student)
                                @can('student-admission.create')
                                    <a href="{{ route('student-admissions.create', $application) }}" class="btn btn-sm btn-success ms-1" title="Complete Admission"><i class="bi bi-person-check"></i> Admit</a>
                                @endcan
                            @elseif($application->student)
                                @can('student.view')
                                    <a href="{{ route('students.show', $application->student) }}" class="btn btn-sm btn-outline-success ms-1" title="View Student"><i class="bi bi-person-vcard"></i></a>
                                @endcan
                            @endif
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