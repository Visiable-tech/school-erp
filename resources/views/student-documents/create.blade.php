@extends('layouts.admin')

@section('title', 'Add Student Document')

@section('content')

@php

$documentTypes = [
    'birth_certificate' => 'Birth Certificate',
    'aadhaar_card' => 'Aadhaar Card',
    'transfer_certificate' => 'Transfer Certificate',
    'previous_marksheet' => 'Previous School Marksheet',
    'address_proof' => 'Address Proof',
    'medical_certificate' => 'Medical Certificate',
    'student_photo' => 'Student Photo',
    'caste_certificate' => 'Caste Certificate',
    'income_certificate' => 'Income Certificate',
    'migration_certificate' => 'Migration Certificate',
    'other' => 'Other',
];

@endphp


<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Add Student Document
        </h4>

        <div class="text-muted">
            Select class, section and student to upload document.
        </div>

    </div>


    <a
        href="{{ route('student-documents.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Back
    </a>

</div>


{{-- ========================================================= --}}
{{-- STUDENT SELECTION --}}
{{-- ========================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <strong>
            <i class="bi bi-person-search me-1"></i>
            Select Student
        </strong>

    </div>


    <div class="card-body">

        <div class="row g-3">


            {{-- ACADEMIC YEAR --}}

            <div class="col-md-6">

                <label class="form-label">
                    Academic Year
                    <span class="text-danger">*</span>
                </label>

                <select
                    id="academic_year_id"
                    class="form-select"
                >

                    <option value="">
                        Select Academic Year
                    </option>

                    @foreach($academicYears as $year)

                        <option
                            value="{{ $year->id }}"
                            @selected(
                                $currentAcademicYear
                                &&
                                $currentAcademicYear->id == $year->id
                            )
                        >
                            {{ $year->name }}

                            @if($year->is_current)
                                (Current)
                            @endif

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- CLASS --}}

            <div class="col-md-6">

                <label class="form-label">
                    Class
                    <span class="text-danger">*</span>
                </label>

                <select
                    id="school_class_id"
                    class="form-select"
                >

                    <option value="">
                        Select Class
                    </option>

                    @foreach($classes as $class)

                        <option value="{{ $class->id }}">
                            {{ $class->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- SECTION --}}

            <div class="col-md-6">

                <label class="form-label">
                    Section
                    <span class="text-danger">*</span>
                </label>

                <select
                    id="section_id"
                    class="form-select"
                    disabled
                >

                    <option value="">
                        Select Section
                    </option>

                </select>

            </div>


            {{-- STUDENT --}}

            <div class="col-md-6">

                <label class="form-label">
                    Student
                    <span class="text-danger">*</span>
                </label>

                <select
                    id="student_id"
                    class="form-select"
                    disabled
                >

                    <option value="">
                        Select Student
                    </option>

                </select>

            </div>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- SELECTED STUDENT --}}
{{-- ========================================================= --}}

<div
    id="studentInfo"
    class="card border-0 shadow-sm mb-4 d-none"
>

    <div class="card-header bg-white py-3">

        <strong>
            Selected Student
        </strong>

    </div>


    <div class="card-body">

        <div class="row">


            <div class="col-md-3">

                <small class="text-muted">
                    Admission No.
                </small>

                <div
                    id="admissionNo"
                    class="fw-semibold"
                >
                    -
                </div>

            </div>


            <div class="col-md-4">

                <small class="text-muted">
                    Student Name
                </small>

                <div
                    id="studentName"
                    class="fw-semibold"
                >
                    -
                </div>

            </div>


            <div class="col-md-2">

                <small class="text-muted">
                    Roll No.
                </small>

                <div
                    id="rollNo"
                    class="fw-semibold"
                >
                    -
                </div>

            </div>


            <div class="col-md-3">

                <small class="text-muted">
                    Class / Section
                </small>

                <div
                    id="classSection"
                    class="fw-semibold"
                >
                    -
                </div>

            </div>


        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- DOCUMENT FORM --}}
{{-- ========================================================= --}}

<div
    id="documentForm"
    class="card border-0 shadow-sm d-none"
>

    <div class="card-header bg-white py-3">

        <strong>
            <i class="bi bi-file-earmark-arrow-up me-1"></i>
            Document Details
        </strong>

    </div>


    <div class="card-body">


        <form
            id="uploadForm"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf


            <div class="row g-3">


                {{-- DOCUMENT TYPE --}}

                <div class="col-md-4">

                    <label class="form-label">
                        Document Type
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="document_type"
                        id="document_type"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Document Type
                        </option>

                        @foreach($documentTypes as $key => $label)

                            <option
                                value="{{ $key }}"
                                @selected(
                                    old('document_type') == $key
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- DOCUMENT NAME --}}

                <div class="col-md-4">

                    <label class="form-label">
                        Document Name
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="document_name"
                        id="document_name"
                        value="{{ old('document_name') }}"
                        class="form-control"
                        placeholder="Example: Birth Certificate"
                        required
                    >

                </div>


                {{-- DOCUMENT NUMBER --}}

                <div class="col-md-4">

                    <label class="form-label">
                        Document Number
                    </label>

                    <input
                        type="text"
                        name="document_number"
                        value="{{ old('document_number') }}"
                        class="form-control"
                        placeholder="Optional"
                    >

                </div>


                {{-- FILE --}}

                <div class="col-md-6">

                    <label class="form-label">
                        Upload File
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="file"
                        name="file"
                        class="form-control"
                        accept=".pdf,.jpg,.jpeg,.png"
                        required
                    >

                    <small class="text-muted">
                        PDF, JPG, JPEG or PNG. Maximum 5 MB.
                    </small>

                </div>


                {{-- REMARKS --}}

                <div class="col-md-6">

                    <label class="form-label">
                        Remarks
                    </label>

                    <textarea
                        name="remarks"
                        class="form-control"
                        rows="2"
                        placeholder="Optional remarks"
                    >{{ old('remarks') }}</textarea>

                </div>


                <div class="col-12">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >

                        <i class="bi bi-cloud-upload me-1"></i>
                        Upload Document

                    </button>


                    <a
                        href="{{ route('student-documents.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                </div>


            </div>

        </form>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const academicYear =
        document.getElementById('academic_year_id');

    const schoolClass =
        document.getElementById('school_class_id');

    const section =
        document.getElementById('section_id');

    const student =
        document.getElementById('student_id');

    const studentInfo =
        document.getElementById('studentInfo');

    const documentForm =
        document.getElementById('documentForm');

    const uploadForm =
        document.getElementById('uploadForm');

    const documentType =
        document.getElementById('document_type');

    const documentName =
        document.getElementById('document_name');


    /*
    |--------------------------------------------------------------------------
    | Reset Sections
    |--------------------------------------------------------------------------
    */

    function resetSections() {

        section.innerHTML =
            '<option value="">Select Section</option>';

        section.disabled = true;

    }


    /*
    |--------------------------------------------------------------------------
    | Reset Students
    |--------------------------------------------------------------------------
    */

    function resetStudents() {

        student.innerHTML =
            '<option value="">Select Student</option>';

        student.disabled = true;

        studentInfo.classList.add('d-none');

        documentForm.classList.add('d-none');

        uploadForm.action = '';

    }


    /*
    |--------------------------------------------------------------------------
    | Load Sections
    |--------------------------------------------------------------------------
    */

    async function loadSections() {

        resetSections();

        resetStudents();

        const academicYearId =
            academicYear.value;

        const classId =
            schoolClass.value;


        if (!academicYearId || !classId) {
            return;
        }


        section.innerHTML =
            '<option value="">Loading...</option>';


        try {

            const url =
                "{{ route('student-documents.sections') }}"
                + "?academic_year_id="
                + encodeURIComponent(academicYearId)
                + "&school_class_id="
                + encodeURIComponent(classId);


            const response =
                await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });


            if (!response.ok) {
                throw new Error('Unable to load sections.');
            }


            const sections =
                await response.json();


            section.innerHTML =
                '<option value="">Select Section</option>';


            sections.forEach(function (item) {

                const option =
                    document.createElement('option');

                option.value =
                    item.id;

                option.textContent =
                    item.name;

                section.appendChild(option);

            });


            section.disabled = false;


            if (sections.length === 0) {

                section.innerHTML =
                    '<option value="">No Sections Found</option>';

                section.disabled = true;

            }

        }
        catch (error) {

            section.innerHTML =
                '<option value="">Unable to load sections</option>';

            section.disabled = true;

        }

    }


    academicYear.addEventListener(
        'change',
        loadSections
    );


    schoolClass.addEventListener(
        'change',
        loadSections
    );


    /*
    |--------------------------------------------------------------------------
    | Load Students
    |--------------------------------------------------------------------------
    */

    section.addEventListener(
        'change',
        async function () {

            resetStudents();

            if (!section.value) {
                return;
            }


            student.innerHTML =
                '<option value="">Loading...</option>';


            try {

                const url =
                    "{{ route('student-documents.students') }}"
                    + "?academic_year_id="
                    + encodeURIComponent(academicYear.value)
                    + "&school_class_id="
                    + encodeURIComponent(schoolClass.value)
                    + "&section_id="
                    + encodeURIComponent(section.value);


                const response =
                    await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });


                if (!response.ok) {
                    throw new Error('Unable to load students.');
                }


                const students =
                    await response.json();


                student.innerHTML =
                    '<option value="">Select Student</option>';


                students.forEach(function (item) {

                    const option =
                        document.createElement('option');


                    option.value =
                        item.id;


                    let label =
                        item.admission_no
                        + ' - '
                        + item.student_name;


                    if (item.roll_no) {

                        label +=
                            ' - Roll '
                            + item.roll_no;

                    }


                    option.textContent =
                        label;


                    option.dataset.admission =
                        item.admission_no || '';


                    option.dataset.name =
                        item.student_name || '';


                    option.dataset.roll =
                        item.roll_no || '';


                    student.appendChild(option);

                });


                student.disabled = false;


                if (students.length === 0) {

                    student.innerHTML =
                        '<option value="">No Students Found</option>';

                    student.disabled = true;

                }

            }
            catch (error) {

                student.innerHTML =
                    '<option value="">Unable to load students</option>';

                student.disabled = true;

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Select Student
    |--------------------------------------------------------------------------
    */

    student.addEventListener(
        'change',
        function () {

            if (!this.value) {

                studentInfo.classList.add('d-none');

                documentForm.classList.add('d-none');

                uploadForm.action = '';

                return;
            }


            const selectedStudent =
                this.options[this.selectedIndex];


            document.getElementById(
                'admissionNo'
            ).textContent =
                selectedStudent.dataset.admission || '-';


            document.getElementById(
                'studentName'
            ).textContent =
                selectedStudent.dataset.name || '-';


            document.getElementById(
                'rollNo'
            ).textContent =
                selectedStudent.dataset.roll || '-';


            const selectedClass =
                schoolClass.options[
                    schoolClass.selectedIndex
                ]?.text || '-';


            const selectedSection =
                section.options[
                    section.selectedIndex
                ]?.text || '-';


            document.getElementById(
                'classSection'
            ).textContent =
                selectedClass
                + ' / '
                + selectedSection;


            /*
            |--------------------------------------------------------------------------
            | Set Upload URL
            |--------------------------------------------------------------------------
            */

            uploadForm.action =
                "{{ url('/students') }}"
                + "/"
                + this.value
                + "/documents";


            studentInfo.classList.remove('d-none');

            documentForm.classList.remove('d-none');

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Auto Fill Document Name
    |--------------------------------------------------------------------------
    */

    documentType.addEventListener(
        'change',
        function () {

            if (!this.value) {
                return;
            }

            const text =
                this.options[
                    this.selectedIndex
                ].text;

            /*
             * Only auto-fill if empty.
             * User can still change it.
             */

            if (!documentName.value.trim()) {
                documentName.value = text;
            }

        }
    );


});

</script>

@endsection