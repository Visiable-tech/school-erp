@extends('layouts.admin')

@section('title', 'Import Students')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Bulk Student Import</h4>

        <div class="text-muted">
            Import students and academic enrollments using Excel.
        </div>
    </div>

    <a
        href="{{ route('students.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Student List
    </a>

</div>


<div class="row">

    <div class="col-lg-8">

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <strong>Upload Excel</strong>
            </div>

            <div class="card-body">

                <div class="alert alert-info">

                    <strong>Recommended process:</strong>

                    Download the template, enter student data,
                    then upload the completed file.

                </div>


                <div class="mb-4">

                    <a
                        href="{{ route('students.import.template') }}"
                        class="btn btn-success"
                    >
                        <i class="bi bi-download"></i>
                        Download Excel Template
                    </a>

                </div>


                <form
                    action="{{ route('students.import.preview') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    @csrf


                    <div class="mb-3">

                        <label class="form-label">
                            Excel File
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="file"
                            name="file"
                            accept=".xlsx,.xls,.csv"
                            class="form-control @error('file') is-invalid @enderror"
                            required
                        >

                        @error('file')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror


                        <div class="form-text">
                            Supported: XLSX, XLS and CSV. Maximum 10 MB.
                        </div>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-search"></i>
                        Upload & Preview
                    </button>

                </form>

            </div>

        </div>

    </div>


    <div class="col-lg-4">

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <strong>Important</strong>
            </div>

            <div class="card-body">

                <ul class="mb-0">

                    <li class="mb-2">
                        Do not change Excel column headings.
                    </li>

                    <li class="mb-2">
                        Academic Year must already exist.
                    </li>

                    <li class="mb-2">
                        Class must already exist.
                    </li>

                    <li class="mb-2">
                        Section must exist for that year and class.
                    </li>

                    <li class="mb-2">
                        Roll numbers must be unique inside the section.
                    </li>

                    <li>
                        Admission numbers are generated automatically.
                    </li>

                </ul>

            </div>

        </div>

    </div>

</div>

@endsection