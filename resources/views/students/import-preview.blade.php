@extends('layouts.admin')

@section('title', 'Student Import Preview')

@section('content')

@php

    $validCount =
        collect($previewRows)
            ->where('valid', true)
            ->count();

    $invalidCount =
        collect($previewRows)
            ->where('valid', false)
            ->count();

@endphp


<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Student Import Preview
        </h4>

        <div class="text-muted">
            Check all rows before confirming the import.
        </div>

    </div>


    <a
        href="{{ route('students.import') }}"
        class="btn btn-outline-secondary"
    >
        Upload Another File
    </a>

</div>


<div class="row g-3 mb-4">

    <div class="col-md-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="text-muted">
                    Total Rows
                </div>

                <h3 class="mb-0">
                    {{ count($previewRows) }}
                </h3>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="text-muted">
                    Valid
                </div>

                <h3 class="mb-0 text-success">
                    {{ $validCount }}
                </h3>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="text-muted">
                    Invalid
                </div>

                <h3 class="mb-0 text-danger">
                    {{ $invalidCount }}
                </h3>

            </div>

        </div>

    </div>

</div>


<div class="card border-0 shadow-sm mb-4">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>Excel Row</th>
                        <th>Status</th>
                        <th>Student</th>
                        <th>Academic Year</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Roll</th>
                        <th>Errors</th>

                    </tr>

                </thead>


                <tbody>

                @foreach($previewRows as $row)

                    <tr>

                        <td>
                            {{ $row['excel_row'] }}
                        </td>


                        <td>

                            @if($row['valid'])

                                <span class="badge bg-success">
                                    Valid
                                </span>

                            @else

                                <span class="badge bg-danger">
                                    Invalid
                                </span>

                            @endif

                        </td>


                        <td>
                            {{ $row['data']['student_name'] ?? '-' }}
                        </td>


                        <td>
                            {{ $row['data']['academic_year'] ?? '-' }}
                        </td>


                        <td>
                            {{ $row['data']['class'] ?? '-' }}
                        </td>


                        <td>
                            {{ $row['data']['section'] ?? '-' }}
                        </td>


                        <td>
                            {{ $row['data']['roll_no'] ?? '-' }}
                        </td>


                        <td>

                            @if(!$row['valid'])

                                <ul class="text-danger mb-0 ps-3">

                                    @foreach($row['errors'] as $error)

                                        <li>
                                            {{ $error }}
                                        </li>

                                    @endforeach

                                </ul>

                            @else

                                <span class="text-success">
                                    Ready
                                </span>

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>


@if($validCount > 0)

    <form
        action="{{ route('students.import.confirm') }}"
        method="POST"
    >

        @csrf

        <input
            type="hidden"
            name="token"
            value="{{ $token }}"
        >


        @if($invalidCount > 0)

            <div class="alert alert-warning">

                {{ $invalidCount }} invalid row(s) will NOT be imported.

                Only the {{ $validCount }} valid row(s) will be imported.

            </div>

        @endif


        <button
            type="submit"
            class="btn btn-success"
            onclick="return confirm(
                'Import {{ $validCount }} valid students?'
            )"
        >
            <i class="bi bi-database-add"></i>
            Import {{ $validCount }} Students
        </button>


        <a
            href="{{ route('students.import') }}"
            class="btn btn-outline-secondary"
        >
            Cancel
        </a>

    </form>

@endif

@endsection