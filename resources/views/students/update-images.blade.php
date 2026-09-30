@extends('layouts.admin')

@section('title', 'Update Student Images')

@section('content')

<style>
.student-image-table-wrapper {
    height: 200px;
    max-height: 700px;
    overflow: auto;
    position: relative;
}
</style>    

<div class="container-fluid">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Update Student Images
            </h4>

            <div class="text-muted">
                Manage student, parent and portfolio images
            </div>
        </div>

        <a
            href="{{ route('students.index') }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Student List
        </a>

    </div>


    {{-- =========================================================
        SUCCESS MESSAGE
    ========================================================== --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle me-1"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- =========================================================
        VALIDATION ERRORS
    ========================================================== --}}

    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                Please correct the following:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================================
        FILTER SECTION
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                <i class="bi bi-funnel me-1"></i>
                Search Students
            </strong>

        </div>

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('students.images') }}"
            >

                <div class="row g-3">

                    {{-- STATUS --}}

                    <div class="col-xl-2 col-lg-3 col-md-4">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option
                                value="1"
                                @selected(request('status') === '1')
                            >
                                Active
                            </option>

                            <option
                                value="0"
                                @selected(request('status') === '0')
                            >
                                Inactive
                            </option>

                        </select>

                    </div>


                    {{-- ACADEMIC YEAR --}}

                    <div class="col-xl-2 col-lg-3 col-md-4">

                        <label class="form-label">
                            Academic Year
                        </label>

                        <select
                            name="academic_year_id"
                            class="form-select"
                        >

                            <option value="">
                                All
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


                    {{-- CLASS --}}

                    <div class="col-xl-2 col-lg-3 col-md-4">

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


                    {{-- SECTION --}}

                    <div class="col-xl-2 col-lg-3 col-md-4">

                        <label class="form-label">
                            Class-Sec
                        </label>

                        <select
                            name="section_id"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            @foreach($sections as $section)

                                <option
                                    value="{{ $section->id }}"
                                    @selected(
                                        request('section_id')
                                        == $section->id
                                    )
                                >
                                    {{ $section->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- SEARCH --}}

                    <div class="col-xl-4 col-lg-6 col-md-8">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Admission No, Student, Parent, Mobile, Roll No..."
                        >

                    </div>


                    {{-- BUTTONS --}}

                    <div class="col-12">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>

                        <a
                            href="{{ route('students.images') }}"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        STUDENT IMAGE TABLE
    ========================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <strong>
                        <i class="bi bi-images me-1"></i>
                        Student & Parent Images
                    </strong>

                    <div class="small text-muted mt-1">
                        Select one or more images for a student and click
                        Save Images.
                    </div>

                </div>

                <span class="badge bg-primary">

                    {{ $students->total() }}

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            {{-- =================================================
                FIXED 700PX SCROLL AREA
            ================================================== --}}

            <div
                class="table-responsive student-image-table-wrapper"
            >

                <table
                    class="table table-bordered table-striped table-hover align-middle mb-0 student-image-table"
                >

                    {{-- =========================================
                        STICKY HEADER
                    ========================================== --}}

                    <thead class="table-dark">

                        <tr>

                            <th style="width:55px;">
                                #
                            </th>

                            <th style="min-width:140px;">
                                Enrollment No.
                            </th>

                            <th style="min-width:190px;">
                                Student
                            </th>

                            <th style="min-width:220px;">
                                Student Image
                            </th>

                            <th style="min-width:220px;">
                                Father Image
                            </th>

                            <th style="min-width:220px;">
                                Mother Image
                            </th>

                            <th style="min-width:220px;">
                                Guardian Image
                            </th>

                            <th style="min-width:220px;">
                                Glimpse of Myself
                            </th>

                            <th style="min-width:220px;">
                                Glimpse of My Family
                            </th>

                            <th style="min-width:220px;">
                                Learner's Portfolio
                            </th>

                            <th style="min-width:140px;">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($students as $student)

                            @php

                                /*
                                |--------------------------------------------------------------------------
                                | Existing Student Images
                                |--------------------------------------------------------------------------
                                */

                                $images = $student
                                    ->images
                                    ->keyBy('image_type');


                                /*
                                |--------------------------------------------------------------------------
                                | Image Categories
                                |--------------------------------------------------------------------------
                                */

                                $imageTypes = [

                                    'student' => [
                                        'label' => 'Student',
                                        'icon'  => 'bi-person',
                                    ],

                                    'father' => [
                                        'label' => 'Father',
                                        'icon'  => 'bi-person',
                                    ],

                                    'mother' => [
                                        'label' => 'Mother',
                                        'icon'  => 'bi-person',
                                    ],

                                    'guardian' => [
                                        'label' => 'Guardian',
                                        'icon'  => 'bi-person',
                                    ],

                                    'glimpse_myself' => [
                                        'label' => 'Glimpse of Myself',
                                        'icon'  => 'bi-image',
                                    ],

                                    'glimpse_family' => [
                                        'label' => 'Glimpse of My Family',
                                        'icon'  => 'bi-people',
                                    ],

                                    'learner_portfolio' => [
                                        'label' => "Learner's Portfolio",
                                        'icon'  => 'bi-folder2-open',
                                    ],

                                ];


                                /*
                                |--------------------------------------------------------------------------
                                | One Form ID Per Student
                                |--------------------------------------------------------------------------
                                */

                                $studentFormId =
                                    'student-images-form-'
                                    . $student->id;

                            @endphp


                            <tr>

                                {{-- =====================================
                                    SERIAL NUMBER
                                ====================================== --}}

                                <td class="text-center">

                                    {{
                                        $students->firstItem()
                                        + $loop->index
                                    }}

                                </td>


                                {{-- =====================================
                                    ENROLLMENT / ADMISSION NUMBER
                                ====================================== --}}

                                <td>

                                    <strong class="text-primary">

                                        {{ $student->admission_no }}

                                    </strong>


                                    @if(
                                        $student
                                            ->currentEnrollment
                                            ?->roll_no
                                    )

                                        <div class="small text-muted mt-1">

                                            Roll:

                                            <strong>

                                                {{
                                                    $student
                                                        ->currentEnrollment
                                                        ->roll_no
                                                }}

                                            </strong>

                                        </div>

                                    @endif

                                </td>


                                {{-- =====================================
                                    STUDENT INFORMATION
                                ====================================== --}}

                                <td>

                                    <div>

                                        <strong>

                                            {{ $student->student_name }}

                                        </strong>

                                    </div>


                                    <div class="small text-muted mt-1">

                                        {{
                                            $student
                                                ->currentEnrollment
                                                ?->schoolClass
                                                ?->name
                                            ?? '-'
                                        }}

                                        /

                                        {{
                                            $student
                                                ->currentEnrollment
                                                ?->section
                                                ?->name
                                            ?? '-'
                                        }}

                                    </div>


                                    @if($student->father_name)

                                        <div class="small text-muted mt-1">

                                            Father:
                                            {{ $student->father_name }}

                                        </div>

                                    @endif

                                </td>


                                {{-- =====================================
                                    IMAGE COLUMNS
                                ====================================== --}}

                                @foreach(
                                    $imageTypes
                                    as $type => $imageConfig
                                )

                                    @php

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Check student_images Table
                                        |--------------------------------------------------------------------------
                                        */

                                        $storedImage =
                                            $images->get($type);


                                        /*
                                        |--------------------------------------------------------------------------
                                        | Student Photo Backward Compatibility
                                        |--------------------------------------------------------------------------
                                        */

                                        if ($storedImage) {

                                            $existingPath =
                                                $storedImage
                                                    ->image_path;

                                        } elseif (
                                            $type === 'student'
                                            &&
                                            $student->student_photo
                                        ) {

                                            $existingPath =
                                                $student
                                                    ->student_photo;

                                        } else {

                                            $existingPath =
                                                null;

                                        }


                                        $previewId =
                                            'preview-'
                                            . $student->id
                                            . '-'
                                            . $type;


                                        $placeholderId =
                                            'placeholder-'
                                            . $student->id
                                            . '-'
                                            . $type;

                                    @endphp


                                    <td>

                                        <div
                                            class="d-flex align-items-start gap-2"
                                        >

                                            {{-- =============================
                                                IMAGE PREVIEW
                                            ============================== --}}

                                            <div
                                                class="student-image-preview-box"
                                            >

                                                @if($existingPath)

                                                    <img
                                                        src="{{
                                                            asset(
                                                                'storage/'
                                                                . $existingPath
                                                            )
                                                        }}"
                                                        id="{{ $previewId }}"
                                                        class="student-image-preview"
                                                        alt="{{
                                                            $imageConfig[
                                                                'label'
                                                            ]
                                                        }}"
                                                    >

                                                @else

                                                    <div
                                                        id="{{ $placeholderId }}"
                                                        class="student-image-placeholder"
                                                    >

                                                        <i
                                                            class="bi {{
                                                                $imageConfig[
                                                                    'icon'
                                                                ]
                                                            }}"
                                                        ></i>

                                                    </div>


                                                    <img
                                                        src=""
                                                        id="{{ $previewId }}"
                                                        class="student-image-preview d-none"
                                                        alt="{{
                                                            $imageConfig[
                                                                'label'
                                                            ]
                                                        }}"
                                                    >

                                                @endif


                                                {{-- =========================
                                                    DELETE EXISTING IMAGE
                                                ========================== --}}

                                                @if($storedImage)

                                                    <button
                                                        type="button"
                                                        class="btn btn-danger btn-sm image-delete-button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteImageModal"
                                                        data-delete-url="{{
                                                            route(
                                                                'students.images.delete',
                                                                [
                                                                    $student,
                                                                    $type
                                                                ]
                                                            )
                                                        }}"
                                                        data-image-name="{{
                                                            $imageConfig[
                                                                'label'
                                                            ]
                                                        }}"
                                                        title="Delete Image"
                                                    >

                                                        <i
                                                            class="bi bi-x-lg"
                                                        ></i>

                                                    </button>

                                                @endif

                                            </div>


                                            {{-- =============================
                                                FILE INPUT
                                            ============================== --}}

                                            <div class="flex-grow-1">

                                                <label
                                                    class="small fw-semibold mb-1"
                                                >
                                                    {{
                                                        $existingPath
                                                        ? 'Replace'
                                                        : 'Upload'
                                                    }}
                                                </label>


                                                <input
                                                    form="{{ $studentFormId }}"
                                                    type="file"
                                                    name="images[{{ $type }}]"
                                                    class="form-control form-control-sm image-input"
                                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                                    data-student="{{
                                                        $student->id
                                                    }}"
                                                    data-type="{{ $type }}"
                                                >


                                                <div
                                                    class="small text-muted mt-1 selected-file-name"
                                                    id="filename-{{
                                                        $student->id
                                                    }}-{{ $type }}"
                                                >

                                                    No new image selected

                                                </div>

                                            </div>

                                        </div>

                                    </td>

                                @endforeach


                                {{-- =====================================
                                    SAVE BUTTON
                                ====================================== --}}

                                <td class="text-center">

                                    {{-- ONE FORM PER STUDENT --}}

                                    <form
                                        id="{{ $studentFormId }}"
                                        method="POST"
                                        action="{{
                                            route(
                                                'students.images.upload',
                                                $student
                                            )
                                        }}"
                                        enctype="multipart/form-data"
                                    >

                                        @csrf

                                    </form>


                                    <button
                                        form="{{ $studentFormId }}"
                                        type="submit"
                                        class="btn btn-success btn-sm w-100"
                                    >

                                        <i class="bi bi-floppy me-1"></i>

                                        Save Images

                                    </button>


                                    <div class="small text-muted mt-2">

                                        Selected images only

                                    </div>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="11"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-images"
                                        style="font-size:2rem;"
                                    ></i>

                                    <div class="mt-2">

                                        No students found.

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =====================================================
            PAGINATION
            OUTSIDE 700PX SCROLL AREA
        ====================================================== --}}

        <div class="card-footer bg-white">

            <div
                class="d-flex justify-content-between align-items-center flex-wrap gap-2"
            >

                <div class="small text-muted">

                    Showing

                    <strong>
                        {{ $students->firstItem() ?? 0 }}
                    </strong>

                    to

                    <strong>
                        {{ $students->lastItem() ?? 0 }}
                    </strong>

                    of

                    <strong>
                        {{ $students->total() }}
                    </strong>

                    students

                </div>


                @if($students->hasPages())

                    <div>

                        {{
                            $students->links(
                                'pagination::bootstrap-5'
                            )
                        }}

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    DELETE IMAGE MODAL
========================================================== --}}

<div
    class="modal fade"
    id="deleteImageModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Delete Image
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                Are you sure you want to delete the

                <strong id="deleteImageName"></strong>

                image?

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>


                <form
                    id="deleteImageForm"
                    method="POST"
                    action=""
                >

                    @csrf
                    @method('DELETE')


                    <button
                        type="submit"
                        class="btn btn-danger"
                    >

                        <i class="bi bi-trash me-1"></i>

                        Delete

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    PAGE CSS
========================================================== --}}

<style>

    /*
    |--------------------------------------------------------------------------
    | Fixed Table Height
    |--------------------------------------------------------------------------
    */

    .student-image-table-wrapper {
        height: 700px;
        max-height: 700px;
        overflow: auto;
        position: relative;
    }


    /*
    |--------------------------------------------------------------------------
    | Table Width
    |--------------------------------------------------------------------------
    */

    .student-image-table {
        min-width: 1900px;
    }


    /*
    |--------------------------------------------------------------------------
    | Sticky Header
    |--------------------------------------------------------------------------
    */

    .student-image-table thead th {
        position: sticky;
        top: 0;
        z-index: 20;
        vertical-align: middle;
        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Image Preview
    |--------------------------------------------------------------------------
    */

    .student-image-preview-box {
        width: 70px;
        min-width: 70px;
        position: relative;
    }


    .student-image-preview {
        width: 65px;
        height: 75px;
        object-fit: cover;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background: #ffffff;
    }


    .student-image-placeholder {
        width: 65px;
        height: 75px;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background: #f8f9fa;

        display: flex;
        align-items: center;
        justify-content: center;

        color: #6c757d;
        font-size: 1.5rem;
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Button
    |--------------------------------------------------------------------------
    */

    .image-delete-button {
        position: absolute;

        top: -7px;
        right: -1px;

        width: 23px;
        height: 23px;

        padding: 0;

        border-radius: 50%;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 10px;
    }


    /*
    |--------------------------------------------------------------------------
    | File Input
    |--------------------------------------------------------------------------
    */

    .student-image-table input[type="file"] {
        min-width: 125px;
    }


    /*
    |--------------------------------------------------------------------------
    | Table Cells
    |--------------------------------------------------------------------------
    */

    .student-image-table td {
        vertical-align: middle;
    }


    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    .pagination {
        margin-bottom: 0;
    }

</style>


{{-- =========================================================
    JAVASCRIPT
========================================================== --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | Image Preview
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll('.image-input')
            .forEach(function (input) {

                input.addEventListener(
                    'change',
                    function () {

                        const studentId =
                            this.dataset.student;

                        const type =
                            this.dataset.type;

                        const file =
                            this.files[0];


                        const preview =
                            document.getElementById(
                                'preview-'
                                + studentId
                                + '-'
                                + type
                            );


                        const placeholder =
                            document.getElementById(
                                'placeholder-'
                                + studentId
                                + '-'
                                + type
                            );


                        const fileName =
                            document.getElementById(
                                'filename-'
                                + studentId
                                + '-'
                                + type
                            );


                        if (!file) {

                            fileName.textContent =
                                'No new image selected';

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Basic Client Validation
                        |--------------------------------------------------------------------------
                        */

                        const allowedTypes = [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ];


                        if (
                            !allowedTypes.includes(
                                file.type
                            )
                        ) {

                            alert(
                                'Please select JPG, PNG or WEBP image.'
                            );

                            this.value = '';

                            fileName.textContent =
                                'No new image selected';

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | 2 MB Limit
                        |--------------------------------------------------------------------------
                        */

                        const maxSize =
                            2 * 1024 * 1024;


                        if (file.size > maxSize) {

                            alert(
                                'Image size cannot exceed 2 MB.'
                            );

                            this.value = '';

                            fileName.textContent =
                                'No new image selected';

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Preview
                        |--------------------------------------------------------------------------
                        */

                        preview.src =
                            URL.createObjectURL(
                                file
                            );


                        preview.classList.remove(
                            'd-none'
                        );


                        if (placeholder) {

                            placeholder
                                .classList
                                .add('d-none');
                        }


                        fileName.textContent =
                            file.name;

                    }
                );

            });


        /*
        |--------------------------------------------------------------------------
        | Delete Modal
        |--------------------------------------------------------------------------
        */

        const deleteModal =
            document.getElementById(
                'deleteImageModal'
            );


        if (deleteModal) {

            deleteModal.addEventListener(
                'show.bs.modal',
                function (event) {

                    const button =
                        event.relatedTarget;


                    const deleteUrl =
                        button.getAttribute(
                            'data-delete-url'
                        );


                    const imageName =
                        button.getAttribute(
                            'data-image-name'
                        );


                    document
                        .getElementById(
                            'deleteImageForm'
                        )
                        .setAttribute(
                            'action',
                            deleteUrl
                        );


                    document
                        .getElementById(
                            'deleteImageName'
                        )
                        .textContent =
                            imageName;

                }
            );

        }

    }
);

</script>

@endsection