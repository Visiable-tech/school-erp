@extends('layouts.admin')

@section('title', 'Admission Follow-ups')

@section('content')

<div class="mb-4">

    <h4 class="mb-1">
        Admission Follow-ups
    </h4>

    <div class="text-muted">
        Track communication with admission enquiries
    </div>

</div>


<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form method="GET">

            <div class="row g-3">

                <div class="col-md-5">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Enquiry no, student or mobile"
                    >

                </div>


                <div class="col-md-3">

                    <select
                        name="response_status"
                        class="form-select"
                    >

                        <option value="">
                            All Responses
                        </option>

                        @foreach([
                            'follow_up' => 'Follow Up',
                            'interested' => 'Interested',
                            'not_interested' => 'Not Interested',
                            'applied' => 'Applied',
                            'closed' => 'Closed'
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    request('response_status')
                                    == $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-2">

                    <input
                        type="date"
                        name="followup_date"
                        value="{{ request('followup_date') }}"
                        class="form-control"
                    >

                </div>


                <div class="col-md-2">

                    <button class="btn btn-primary">
                        Filter
                    </button>

                    <a
                        href="{{ route('admission-followups.index') }}"
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
                        <th>Date</th>
                        <th>Enquiry</th>
                        <th>Student</th>
                        <th>Type</th>
                        <th>Response</th>
                        <th>Next Follow-up</th>
                        <th>Followed By</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                @forelse($followups as $followup)

                    <tr>

                        <td>
                            {{ $followup->followup_date->format('d M Y') }}
                        </td>

                        <td>
                            {{ $followup->enquiry->enquiry_no }}
                        </td>

                        <td>

                            <strong>
                                {{ $followup->enquiry->student_name }}
                            </strong>

                            <div class="small text-muted">
                                {{ $followup->enquiry->mobile }}
                            </div>

                        </td>

                        <td>
                            {{
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $followup->followup_type
                                    )
                                )
                            }}
                        </td>

                        <td>

                            <span class="badge bg-secondary">

                                {{
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $followup->response_status
                                        )
                                    )
                                }}

                            </span>

                        </td>

                        <td>

                            @if($followup->next_followup_date)

                                {{
                                    $followup
                                        ->next_followup_date
                                        ->format('d M Y')
                                }}

                            @else
                                -
                            @endif

                        </td>

                        <td>
                            {{ $followup->followedBy?->name ?? '-' }}
                        </td>

                        <td>

                            @can('admission-followup.edit')

                                <a
                                    href="{{ route(
                                        'admission-followups.edit',
                                        $followup
                                    ) }}"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>

                            @endcan


                            @can('admission-followup.delete')

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admission-followups.destroy',
                                        $followup
                                    ) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete this follow-up?')"
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
                            colspan="8"
                            class="text-center py-5 text-muted"
                        >
                            No follow-up records found.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        <div class="mt-3">
            {{ $followups->links() }}
        </div>

    </div>

</div>

@endsection