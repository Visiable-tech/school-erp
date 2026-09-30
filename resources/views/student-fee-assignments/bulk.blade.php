@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Bulk Student Fee Assignment
            </h4>

            <small class="text-muted">
                Generate fees for all students of a class or section
            </small>
        </div>

        <a href="{{ route('student-fee-assignments.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>

    </div>


    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    <form method="POST"
          action="{{ route('student-fee-assignments.bulk.store') }}"
          id="bulkFeeForm">

        @csrf


        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white">

                <strong>
                    <i class="bi bi-people"></i>
                    Bulk Fee Generation
                </strong>

            </div>


            <div class="card-body">

                <div class="alert alert-info">

                    <strong>How it works:</strong>

                    Select an Academic Year, Class and Fee Structure.

                    Leave Section as
                    <strong>All Sections</strong>
                    to generate fees for every eligible student in the class.

                    Students who already have fees assigned for this
                    academic year will automatically be skipped.

                </div>


                <div class="row g-3">


                    {{-- Academic Year --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Academic Year
                            <span class="text-danger">*</span>
                        </label>

                        <select name="academic_year_id"
                                id="academic_year_id"
                                class="form-select"
                                required>

                            <option value="">
                                Select Academic Year
                            </option>

                            @foreach($academicYears as $year)

                                <option
                                    value="{{ $year->id }}"

                                    {{ old(
                                        'academic_year_id',
                                        optional($currentAcademicYear)->id
                                    ) == $year->id
                                        ? 'selected'
                                        : '' }}
                                >
                                    {{ $year->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Class --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Class
                            <span class="text-danger">*</span>
                        </label>

                        <select name="school_class_id"
                                id="school_class_id"
                                class="form-select"
                                required>

                            <option value="">
                                Select Class
                            </option>

                            @foreach($classes as $class)

                                <option
                                    value="{{ $class->id }}"
                                    {{ old('school_class_id') == $class->id
                                        ? 'selected'
                                        : '' }}
                                >
                                    {{ $class->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Section --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Section
                        </label>

                        <select name="section_id"
                                id="section_id"
                                class="form-select"
                                disabled>

                            <option value="">
                                All Sections
                            </option>

                        </select>

                        <div class="form-text">
                            Leave blank for entire class.
                        </div>

                    </div>


                    {{-- Fee Structure --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Fee Structure
                            <span class="text-danger">*</span>
                        </label>

                        <select name="fee_structure_id"
                                id="fee_structure_id"
                                class="form-select"
                                required
                                disabled>

                            <option value="">
                                Select Fee Structure
                            </option>

                        </select>

                    </div>


                    {{-- Date --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Assignment Date
                            <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               name="assigned_date"
                               class="form-control"
                               value="{{ old(
                                   'assigned_date',
                                   date('Y-m-d')
                               ) }}"
                               required>

                    </div>

                </div>


                <hr>


                <div class="alert alert-warning mb-3">

                    <i class="bi bi-exclamation-triangle me-1"></i>

                    Bulk generation creates the
                    <strong>standard fee without student-specific discounts</strong>.

                    Existing student fee assignments are not overwritten.

                </div>


                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('student-fee-assignments.index') }}"
                       class="btn btn-light">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-success"
                            id="generateBtn">

                        <i class="bi bi-lightning-charge"></i>

                        Generate Fees for All Students

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const year =
        document.getElementById('academic_year_id');

    const classSelect =
        document.getElementById('school_class_id');

    const section =
        document.getElementById('section_id');

    const structure =
        document.getElementById('fee_structure_id');

    const form =
        document.getElementById('bulkFeeForm');

    const button =
        document.getElementById('generateBtn');


    async function loadData() {

        section.innerHTML =
            '<option value="">All Sections</option>';

        structure.innerHTML =
            '<option value="">Select Fee Structure</option>';

        section.disabled = true;
        structure.disabled = true;


        if (!year.value || !classSelect.value) {
            return;
        }


        try {

            /*
             * Load sections and fee structures
             * at the same time.
             */
            const sectionUrl =
                '{{ route("student-fee-assignments.sections") }}'
                + '?academic_year_id='
                + encodeURIComponent(year.value)
                + '&school_class_id='
                + encodeURIComponent(classSelect.value);


            const structureUrl =
                '{{ route("student-fee-assignments.fee-structures") }}'
                + '?academic_year_id='
                + encodeURIComponent(year.value)
                + '&school_class_id='
                + encodeURIComponent(classSelect.value);


            const responses =
                await Promise.all([
                    fetch(sectionUrl, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }),

                    fetch(structureUrl, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                ]);


            if (
                responses[0].status === 401 ||
                responses[1].status === 401
            ) {
                window.location.reload();
                return;
            }


            if (
                !responses[0].ok ||
                !responses[1].ok
            ) {
                throw new Error(
                    'Unable to load fee assignment data.'
                );
            }


            const sections =
                await responses[0].json();

            const structures =
                await responses[1].json();


            sections.sections.forEach(item => {

                const option =
                    document.createElement('option');

                option.value = item.id;
                option.textContent = item.name;

                section.appendChild(option);
            });


            structures.structures.forEach(item => {

                const option =
                    document.createElement('option');

                option.value = item.id;

                option.textContent =
                    item.name +
                    ' (' +
                    item.installment_count +
                    ' installments)';

                if (item.installment_count === 0) {
                    option.disabled = true;
                }

                structure.appendChild(option);
            });


            section.disabled = false;
            structure.disabled = false;


        } catch (error) {

            alert(error.message);
        }
    }


    year.addEventListener(
        'change',
        loadData
    );


    classSelect.addEventListener(
        'change',
        loadData
    );


    /*
     * Prevent accidental double click.
     */
    form.addEventListener(
        'submit',
        function (event) {

            if (!structure.value) {

                event.preventDefault();

                alert(
                    'Please select a Fee Structure.'
                );

                return;
            }


            const confirmed =
                confirm(
                    'Generate fees for all eligible students? Existing fee assignments will be skipped.'
                );


            if (!confirmed) {

                event.preventDefault();
                return;
            }


            button.disabled = true;

            button.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span> Generating Fees...';
        }
    );


    if (
        year.value &&
        classSelect.value
    ) {
        loadData();
    }

});
</script>

@endsection