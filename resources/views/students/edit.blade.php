@extends('layouts.admin')

@section('title', 'Edit Student')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Edit Student
        </h4>

        <div class="text-muted">

            {{ $student->student_name }}

            |

            {{ $student->admission_no }}

        </div>

    </div>


    <a
        href="{{ route('students.show', $student) }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Back
    </a>

</div>


<form
    method="POST"
    action="{{ route('students.update', $student) }}"
>

    @csrf
    @method('PUT')


    {{-- =====================================================
         ADMISSION INFORMATION
    ====================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                Admission Information
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">

                    <label class="form-label">
                        Admission Number
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $student->admission_no }}"
                        disabled
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Admission Date
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $student->admission_date?->format('d M Y') }}"
                        disabled
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Current Class
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{
                            $student
                                ->currentEnrollment
                                ?->schoolClass
                                ?->name
                            ?? '-'
                        }}"
                        disabled
                    >

                </div>

            </div>

        </div>

    </div>



    {{-- =====================================================
         STUDENT INFORMATION
    ====================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                Student Information
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-3">


                <div class="col-md-6">

                    <label class="form-label">

                        Student Name

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="text"
                        name="student_name"
                        class="form-control @error('student_name') is-invalid @enderror"
                        value="{{ old('student_name', $student->student_name) }}"
                        required
                    >

                    @error('student_name')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Date of Birth
                    </label>

                    <input
                        type="date"
                        name="date_of_birth"
                        class="form-control"
                        value="{{ old(
                            'date_of_birth',
                            $student->date_of_birth?->format('Y-m-d')
                        ) }}"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Gender
                    </label>

                    <select
                        name="gender"
                        class="form-select"
                    >

                        <option value="">
                            Select
                        </option>

                        <option
                            value="male"
                            @selected(
                                old(
                                    'gender',
                                    $student->gender
                                ) === 'male'
                            )
                        >
                            Male
                        </option>

                        <option
                            value="female"
                            @selected(
                                old(
                                    'gender',
                                    $student->gender
                                ) === 'female'
                            )
                        >
                            Female
                        </option>

                        <option
                            value="other"
                            @selected(
                                old(
                                    'gender',
                                    $student->gender
                                ) === 'other'
                            )
                        >
                            Other
                        </option>

                    </select>

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Blood Group
                    </label>

                    <select
                        name="blood_group"
                        class="form-select"
                    >

                        <option value="">
                            Select
                        </option>

                        @foreach([
                            'A+',
                            'A-',
                            'B+',
                            'B-',
                            'AB+',
                            'AB-',
                            'O+',
                            'O-'
                        ] as $bloodGroup)

                            <option
                                value="{{ $bloodGroup }}"
                                @selected(
                                    old(
                                        'blood_group',
                                        $student->blood_group
                                    ) === $bloodGroup
                                )
                            >
                                {{ $bloodGroup }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Nationality
                    </label>

                    <input
                        type="text"
                        name="nationality"
                        class="form-control"
                        value="{{ old(
                            'nationality',
                            $student->nationality
                        ) }}"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Religion
                    </label>

                    <input
                        type="text"
                        name="religion"
                        class="form-control"
                        value="{{ old(
                            'religion',
                            $student->religion
                        ) }}"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Category
                    </label>

                    <input
                        type="text"
                        name="category"
                        class="form-control"
                        value="{{ old(
                            'category',
                            $student->category
                        ) }}"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Mother Tongue
                    </label>

                    <input
                        type="text"
                        name="mother_tongue"
                        class="form-control"
                        value="{{ old(
                            'mother_tongue',
                            $student->mother_tongue
                        ) }}"
                    >

                </div>

            </div>

        </div>

    </div>



    {{-- =====================================================
         FATHER INFORMATION
    ====================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                Father Information
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-3">

                    <label class="form-label">
                        Father Name
                    </label>

                    <input
                        type="text"
                        name="father_name"
                        class="form-control"
                        value="{{ old(
                            'father_name',
                            $student->father_name
                        ) }}"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Mobile
                    </label>

                    <input
                        type="text"
                        name="father_mobile"
                        class="form-control"
                        value="{{ old(
                            'father_mobile',
                            $student->father_mobile
                        ) }}"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Email
                    </label>

                    <input
                        type="email"
                        name="father_email"
                        class="form-control"
                        value="{{ old(
                            'father_email',
                            $student->father_email
                        ) }}"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Occupation
                    </label>

                    <input
                        type="text"
                        name="father_occupation"
                        class="form-control"
                        value="{{ old(
                            'father_occupation',
                            $student->father_occupation
                        ) }}"
                    >

                </div>

            </div>

        </div>

    </div>



    {{-- =====================================================
         MOTHER INFORMATION
    ====================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                Mother Information
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-3">

                    <label class="form-label">
                        Mother Name
                    </label>

                    <input
                        type="text"
                        name="mother_name"
                        class="form-control"
                        value="{{ old(
                            'mother_name',
                            $student->mother_name
                        ) }}"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Mobile
                    </label>

                    <input
                        type="text"
                        name="mother_mobile"
                        class="form-control"
                        value="{{ old(
                            'mother_mobile',
                            $student->mother_mobile
                        ) }}"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Email
                    </label>

                    <input
                        type="email"
                        name="mother_email"
                        class="form-control"
                        value="{{ old(
                            'mother_email',
                            $student->mother_email
                        ) }}"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Occupation
                    </label>

                    <input
                        type="text"
                        name="mother_occupation"
                        class="form-control"
                        value="{{ old(
                            'mother_occupation',
                            $student->mother_occupation
                        ) }}"
                    >

                </div>

            </div>

        </div>

    </div>



    {{-- =====================================================
         GUARDIAN INFORMATION
    ====================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                Guardian Information
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">

                    <label class="form-label">
                        Guardian Name
                    </label>

                    <input
                        type="text"
                        name="guardian_name"
                        class="form-control"
                        value="{{ old(
                            'guardian_name',
                            $student->guardian_name
                        ) }}"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Relation
                    </label>

                    <input
                        type="text"
                        name="guardian_relation"
                        class="form-control"
                        value="{{ old(
                            'guardian_relation',
                            $student->guardian_relation
                        ) }}"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Mobile
                    </label>

                    <input
                        type="text"
                        name="guardian_mobile"
                        class="form-control"
                        value="{{ old(
                            'guardian_mobile',
                            $student->guardian_mobile
                        ) }}"
                    >

                </div>

            </div>

        </div>

    </div>



    {{-- =====================================================
         PRESENT ADDRESS
    ====================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                Present Address
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-12">

                    <label class="form-label">
                        Address
                    </label>

                    <textarea
                        name="present_address"
                        class="form-control"
                        rows="2"
                    >{{ old(
                        'present_address',
                        $student->present_address
                    ) }}</textarea>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        City
                    </label>

                    <input
                        type="text"
                        name="present_city"
                        class="form-control"
                        value="{{ old(
                            'present_city',
                            $student->present_city
                        ) }}"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        State
                    </label>

                    <input
                        type="text"
                        name="present_state"
                        class="form-control"
                        value="{{ old(
                            'present_state',
                            $student->present_state
                        ) }}"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        PIN Code
                    </label>

                    <input
                        type="text"
                        name="present_pin_code"
                        class="form-control"
                        value="{{ old(
                            'present_pin_code',
                            $student->present_pin_code
                        ) }}"
                    >

                </div>

            </div>

        </div>

    </div>



    {{-- =====================================================
         PERMANENT ADDRESS
    ====================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                Permanent Address
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-12">

                    <label class="form-label">
                        Address
                    </label>

                    <textarea
                        name="permanent_address"
                        class="form-control"
                        rows="2"
                    >{{ old(
                        'permanent_address',
                        $student->permanent_address
                    ) }}</textarea>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        City
                    </label>

                    <input
                        type="text"
                        name="permanent_city"
                        class="form-control"
                        value="{{ old(
                            'permanent_city',
                            $student->permanent_city
                        ) }}"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        State
                    </label>

                    <input
                        type="text"
                        name="permanent_state"
                        class="form-control"
                        value="{{ old(
                            'permanent_state',
                            $student->permanent_state
                        ) }}"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        PIN Code
                    </label>

                    <input
                        type="text"
                        name="permanent_pin_code"
                        class="form-control"
                        value="{{ old(
                            'permanent_pin_code',
                            $student->permanent_pin_code
                        ) }}"
                    >

                </div>

            </div>

        </div>

    </div>



    {{-- =====================================================
         PREVIOUS SCHOOL
    ====================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                Previous School
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">

                    <label class="form-label">
                        School Name
                    </label>

                    <input
                        type="text"
                        name="previous_school"
                        class="form-control"
                        value="{{ old(
                            'previous_school',
                            $student->previous_school
                        ) }}"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Previous Class
                    </label>

                    <input
                        type="text"
                        name="previous_class"
                        class="form-control"
                        value="{{ old(
                            'previous_class',
                            $student->previous_class
                        ) }}"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Board
                    </label>

                    <input
                        type="text"
                        name="previous_board"
                        class="form-control"
                        value="{{ old(
                            'previous_board',
                            $student->previous_board
                        ) }}"
                    >

                </div>

            </div>

        </div>

    </div>



    {{-- =====================================================
         STATUS
    ====================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="form-check form-switch">

                <input
                    type="checkbox"
                    name="status"
                    value="1"
                    class="form-check-input"
                    id="studentStatus"
                    @checked(
                        old(
                            'status',
                            $student->status
                        )
                    )
                >

                <label
                    class="form-check-label"
                    for="studentStatus"
                >
                    Active Student
                </label>

            </div>

        </div>

    </div>



    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <button
                type="submit"
                class="btn btn-primary"
            >
                <i class="bi bi-check-lg"></i>
                Update Student
            </button>


            <a
                href="{{ route('students.show', $student) }}"
                class="btn btn-outline-secondary"
            >
                Cancel
            </a>

        </div>

    </div>

</form>

@endsection