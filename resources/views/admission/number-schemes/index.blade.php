@extends('layouts.admin')

@section('title', 'Admission Number Generation Schemes')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Admission Management / Number Generation Schemes
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Scheme For</th>
                            <th>Auto</th>
                            <th>Academic Year</th>
                            <th>Class</th>
                            <th>Separator</th>
                            <th>Starting No.</th>
                            <th>Last Generated No.</th>
                            <th>Fixed String</th>
                            <th>Allow Manual Entry</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($schemes as $scheme)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    <strong>{{ $scheme->scheme_name }}</strong>
                                </td>

                                <td>
                                    {{ $scheme->auto_generate ? 'YES' : 'NO' }}
                                </td>

                                <td>
                                    {{ $scheme->include_academic_year ? 'YES' : 'NO' }}
                                </td>

                                <td>
                                    {{ $scheme->include_class ? 'YES' : 'NO' }}
                                </td>

                                <td>
                                    {{ $scheme->separator ?: '-' }}
                                </td>

                                <td>
                                    {{ $scheme->starting_number }}
                                </td>

                                <td>
                                    {{ $scheme->last_generated_number ?: '-' }}
                                </td>

                                <td>
                                    {{ $scheme->fixed_string ?: '-' }}
                                </td>

                                <td>
                                    {{ $scheme->allow_manual_entry ? 'YES' : 'NO' }}
                                </td>

                                <td>
                                    @if($scheme->status)
                                        <span class="badge bg-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @can('admission-number-scheme.edit')
                                        <a href="{{ route('admission-number-schemes.edit', $scheme->id) }}"
                                           class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                            Edit
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12"
                                    class="text-center text-muted py-4">
                                    No numbering schemes found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="alert alert-info mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Last Generated No. is maintained automatically when a
                number is issued. Editing a scheme does not issue a number.
            </div>

        </div>
    </div>

</div>
@endsection