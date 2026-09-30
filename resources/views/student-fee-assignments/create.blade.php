@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">Student Fee Assignment</h4>
            <small class="text-muted">
                Assign fee structure and generate student fee dues
            </small>
        </div>

        <a href="{{ route('student-fee-assignments.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-list"></i>
            Assignments
        </a>

    </div>


    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following:</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST"
          action="{{ route('student-fee-assignments.store') }}"
          id="feeAssignmentForm">

        @csrf

        {{-- STUDENT SELECTION --}}
        <div class="card border-0 shadow-sm mb-3">

            <div class="card-header bg-white">
                <strong>
                    <i class="bi bi-person"></i>
                    Student Selection
                </strong>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    {{-- Academic Year --}}
                    <div class="col-lg-3 col-md-6">

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
                                    ) == $year->id ? 'selected' : '' }}
                                >
                                    {{ $year->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Class --}}
                    <div class="col-lg-3 col-md-6">

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
                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Section
                            <span class="text-danger">*</span>
                        </label>

                        <select name="section_id"
                                id="section_id"
                                class="form-select"
                                required
                                disabled>

                            <option value="">
                                Select Section
                            </option>

                        </select>

                    </div>


                    {{-- Student --}}
                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Student
                            <span class="text-danger">*</span>
                        </label>

                        <select name="student_id"
                                id="student_id"
                                class="form-select"
                                required
                                disabled>

                            <option value="">
                                Select Student
                            </option>

                        </select>

                        <input type="hidden"
                               name="student_enrollment_id"
                               id="student_enrollment_id">

                    </div>

                </div>

            </div>

        </div>


        {{-- FEE STRUCTURE --}}
        <div class="card border-0 shadow-sm mb-3">

            <div class="card-header bg-white">
                <strong>
                    <i class="bi bi-cash-stack"></i>
                    Fee Structure
                </strong>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-lg-4">

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

                        <div class="form-text"
                             id="structureHelp">
                        </div>

                    </div>


                    <div class="col-lg-2 col-md-4">

                        <label class="form-label">
                            Assigned Date
                            <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               name="assigned_date"
                               id="assigned_date"
                               class="form-control"
                               value="{{ old(
                                   'assigned_date',
                                   date('Y-m-d')
                               ) }}"
                               required>

                    </div>


                    <div class="col-lg-2 col-md-4">

                        <label class="form-label">
                            Discount Type
                        </label>

                        <select name="discount_type"
                                id="discount_type"
                                class="form-select">

                            <option value="none">
                                No Discount
                            </option>

                            <option value="fixed">
                                Fixed Amount
                            </option>

                            <option value="percentage">
                                Percentage
                            </option>

                        </select>

                    </div>


                    <div class="col-lg-2 col-md-4">

                        <label class="form-label">
                            Discount Value
                        </label>

                        <input type="number"
                               name="discount_value"
                               id="discount_value"
                               class="form-control"
                               min="0"
                               step="0.01"
                               value="0"
                               disabled>

                    </div>


                    <div class="col-lg-2 d-flex align-items-end">

                        <button type="button"
                                class="btn btn-primary w-100"
                                id="previewBtn"
                                disabled>

                            <i class="bi bi-eye"></i>
                            Preview

                        </button>

                    </div>

                </div>


                <div class="row mt-3">

                    <div class="col-lg-8">

                        <label class="form-label">
                            Remarks
                        </label>

                        <textarea name="remarks"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Optional remarks">{{ old('remarks') }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        {{-- PREVIEW --}}
        <div class="card border-0 shadow-sm mb-3"
             id="previewCard"
             style="display:none;">

            <div class="card-header bg-white d-flex justify-content-between">

                <strong>
                    <i class="bi bi-receipt"></i>
                    Fee Dues Preview
                </strong>

                <span id="studentInfo"
                      class="text-muted small">
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th>#</th>
                                <th>Fee Head</th>
                                <th>Installment</th>
                                <th>Due Date</th>

                                <th class="text-end">
                                    Amount
                                </th>

                                <th class="text-end">
                                    Discount
                                </th>

                                <th class="text-end">
                                    Payable
                                </th>
                            </tr>

                        </thead>

                        <tbody id="previewBody"></tbody>

                        <tfoot class="table-light fw-bold">

                            <tr>

                                <td colspan="4"
                                    class="text-end">
                                    Total
                                </td>

                                <td class="text-end"
                                    id="baseTotal">
                                    ₹0.00
                                </td>

                                <td class="text-end text-success"
                                    id="discountTotal">
                                    ₹0.00
                                </td>

                                <td class="text-end"
                                    id="payableTotal">
                                    ₹0.00
                                </td>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>

        </div>


        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('student-fee-assignments.index') }}"
               class="btn btn-light">
                Cancel
            </a>

            <button type="submit"
                    id="saveBtn"
                    class="btn btn-success"
                    disabled>

                <i class="bi bi-check-circle"></i>
                Assign Fee Structure

            </button>

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

    const student =
        document.getElementById('student_id');

    const enrollment =
        document.getElementById('student_enrollment_id');

    const structure =
        document.getElementById('fee_structure_id');

    const discountType =
        document.getElementById('discount_type');

    const discountValue =
        document.getElementById('discount_value');

    const previewBtn =
        document.getElementById('previewBtn');

    const saveBtn =
        document.getElementById('saveBtn');

    const previewCard =
        document.getElementById('previewCard');

    const previewBody =
        document.getElementById('previewBody');

    const structureHelp =
        document.getElementById('structureHelp');


    let studentsData = [];


    function invalidatePreview() {

        previewCard.style.display = 'none';

        previewBody.innerHTML = '';

        saveBtn.disabled = true;
    }


    function resetSections() {

        section.innerHTML =
            '<option value="">Select Section</option>';

        section.disabled = true;
    }


    function resetStudents() {

        studentsData = [];

        student.innerHTML =
            '<option value="">Select Student</option>';

        student.disabled = true;

        enrollment.value = '';
    }


    function resetStructures() {

        structure.innerHTML =
            '<option value="">Select Fee Structure</option>';

        structure.disabled = true;

        structureHelp.textContent = '';
    }


    function updatePreviewButton() {

        previewBtn.disabled = !(
            year.value &&
            classSelect.value &&
            section.value &&
            student.value &&
            enrollment.value &&
            structure.value
        );
    }


    async function loadSections() {

        invalidatePreview();
        resetSections();
        resetStudents();

        if (!year.value || !classSelect.value) {
            updatePreviewButton();
            return;
        }

        try {

            const url =
                '{{ route("student-fee-assignments.sections") }}'
                + '?academic_year_id='
                + encodeURIComponent(year.value)
                + '&school_class_id='
                + encodeURIComponent(classSelect.value);

            const response =
                await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

            if (response.status === 401) {
                window.location.reload();
                return;
            }

            if (!response.ok) {
                throw new Error(
                    'Unable to load sections.'
                );
            }

            const data =
                await response.json();

            data.sections.forEach(item => {

                const option =
                    document.createElement('option');

                option.value = item.id;
                option.textContent = item.name;

                section.appendChild(option);
            });

            section.disabled = false;

        } catch (error) {

            alert(error.message);
        }

        updatePreviewButton();
    }


    async function loadStudents() {

        invalidatePreview();
        resetStudents();

        if (
            !year.value ||
            !classSelect.value ||
            !section.value
        ) {
            updatePreviewButton();
            return;
        }

        try {

            const url =
                '{{ route("student-fee-assignments.students") }}'
                + '?academic_year_id='
                + encodeURIComponent(year.value)
                + '&school_class_id='
                + encodeURIComponent(classSelect.value)
                + '&section_id='
                + encodeURIComponent(section.value);

            const response =
                await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

            if (response.status === 401) {
                window.location.reload();
                return;
            }

            if (!response.ok) {
                throw new Error(
                    'Unable to load students.'
                );
            }

            const data =
                await response.json();

            studentsData =
                data.students || [];

            studentsData.forEach(item => {

                const option =
                    document.createElement('option');

                option.value =
                    item.student_id;

                let label = '';

                if (item.roll_no) {
                    label +=
                        'Roll ' +
                        item.roll_no +
                        ' - ';
                }

                label += item.student_name;

                label +=
                    ' (' +
                    item.admission_no +
                    ')';

                if (item.fee_assigned) {
                    label +=
                        ' - Fee Assigned';

                    option.disabled = true;
                }

                option.textContent =
                    label;

                student.appendChild(option);
            });

            student.disabled = false;

        } catch (error) {

            alert(error.message);
        }

        updatePreviewButton();
    }


    async function loadStructures() {

        invalidatePreview();
        resetStructures();

        if (!year.value || !classSelect.value) {
            updatePreviewButton();
            return;
        }

        try {

            const url =
                '{{ route("student-fee-assignments.fee-structures") }}'
                + '?academic_year_id='
                + encodeURIComponent(year.value)
                + '&school_class_id='
                + encodeURIComponent(classSelect.value);

            const response =
                await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

            if (response.status === 401) {
                window.location.reload();
                return;
            }

            if (!response.ok) {
                throw new Error(
                    'Unable to load fee structures.'
                );
            }

            const data =
                await response.json();

            data.structures.forEach(item => {

                const option =
                    document.createElement('option');

                option.value =
                    item.id;

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

            structure.disabled = false;

            if (!data.structures.length) {

                structureHelp.textContent =
                    'No active fee structure found for this class and academic year.';
            }

        } catch (error) {

            alert(error.message);
        }

        updatePreviewButton();
    }


    year.addEventListener(
        'change',
        function () {

            invalidatePreview();

            loadSections();
            loadStructures();
        }
    );


    classSelect.addEventListener(
        'change',
        function () {

            invalidatePreview();

            loadSections();
            loadStructures();
        }
    );


    section.addEventListener(
        'change',
        function () {

            invalidatePreview();
            loadStudents();
        }
    );


    student.addEventListener(
        'change',
        function () {

            invalidatePreview();

            const selected =
                studentsData.find(
                    item =>
                        String(item.student_id) ===
                        String(student.value)
                );

            enrollment.value =
                selected
                    ? selected.student_enrollment_id
                    : '';

            updatePreviewButton();
        }
    );


    structure.addEventListener(
        'change',
        function () {

            invalidatePreview();
            updatePreviewButton();
        }
    );


    discountType.addEventListener(
        'change',
        function () {

            invalidatePreview();

            if (this.value === 'none') {

                discountValue.value = 0;
                discountValue.disabled = true;

            } else {

                discountValue.disabled = false;

                if (
                    Number(discountValue.value) === 0
                ) {
                    discountValue.value = '';
                }
            }

            updatePreviewButton();
        }
    );


    discountValue.addEventListener(
        'input',
        function () {

            invalidatePreview();
            updatePreviewButton();
        }
    );


    previewBtn.addEventListener(
        'click',
        async function () {

            if (previewBtn.disabled) {
                return;
            }

            previewBtn.disabled = true;

            const originalText =
                previewBtn.innerHTML;

            previewBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm"></span> Loading...';

            try {

                const formData =
                    new FormData();

                formData.append(
                    '_token',
                    '{{ csrf_token() }}'
                );

                formData.append(
                    'academic_year_id',
                    year.value
                );

                formData.append(
                    'school_class_id',
                    classSelect.value
                );

                formData.append(
                    'section_id',
                    section.value
                );

                formData.append(
                    'student_id',
                    student.value
                );

                formData.append(
                    'student_enrollment_id',
                    enrollment.value
                );

                formData.append(
                    'fee_structure_id',
                    structure.value
                );

                formData.append(
                    'discount_type',
                    discountType.value
                );

                formData.append(
                    'discount_value',
                    discountValue.value || 0
                );


                const response =
                    await fetch(
                        '{{ route("student-fee-assignments.preview") }}',
                        {
                            method: 'POST',

                            headers: {
                                'Accept':
                                    'application/json'
                            },

                            body: formData
                        }
                    );


                if (response.status === 401) {
                    window.location.reload();
                    return;
                }


                const data =
                    await response.json();


                if (!response.ok) {

                    if (data.errors) {

                        const messages =
                            Object.values(
                                data.errors
                            ).flat();

                        throw new Error(
                            messages.join('\n')
                        );
                    }

                    throw new Error(
                        data.message ||
                        'Unable to preview fees.'
                    );
                }


                previewBody.innerHTML = '';


                data.rows.forEach(
                    (row, index) => {

                        const tr =
                            document.createElement(
                                'tr'
                            );

                        tr.innerHTML = `
                            <td>${index + 1}</td>

                            <td>
                                ${escapeHtml(row.fee_head)}
                            </td>

                            <td>
                                ${escapeHtml(row.installment_name)}
                            </td>

                            <td>
                                ${escapeHtml(row.due_date)}
                            </td>

                            <td class="text-end">
                                ₹${row.base_amount}
                            </td>

                            <td class="text-end text-success">
                                ₹${row.discount_amount}
                            </td>

                            <td class="text-end fw-semibold">
                                ₹${row.payable_amount}
                            </td>
                        `;

                        previewBody.appendChild(tr);
                    }
                );


                document.getElementById(
                    'baseTotal'
                ).textContent =
                    '₹' +
                    data.summary.base_total;


                document.getElementById(
                    'discountTotal'
                ).textContent =
                    '₹' +
                    data.summary.discount_total;


                document.getElementById(
                    'payableTotal'
                ).textContent =
                    '₹' +
                    data.summary.payable_total;


                document.getElementById(
                    'studentInfo'
                ).textContent =
                    data.student.name +
                    ' | Admission: ' +
                    data.student.admission_no +
                    (
                        data.student.roll_no
                            ? ' | Roll: ' +
                              data.student.roll_no
                            : ''
                    );


                previewCard.style.display =
                    'block';

                saveBtn.disabled = false;


            } catch (error) {

                alert(error.message);

                invalidatePreview();

            } finally {

                previewBtn.innerHTML =
                    originalText;

                updatePreviewButton();
            }
        }
    );


    document.getElementById(
        'feeAssignmentForm'
    ).addEventListener(
        'submit',
        function (event) {

            if (saveBtn.disabled) {

                event.preventDefault();

                alert(
                    'Please preview the fee dues before assigning.'
                );

                return;
            }

            saveBtn.disabled = true;

            saveBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm"></span> Assigning...';
        }
    );


    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value == null ? '' : value;

        return div.innerHTML;
    }


    /*
     * Current academic year may already be selected.
     * Once user selects class, required data will load.
     */
    if (
        year.value &&
        classSelect.value
    ) {
        loadSections();
        loadStructures();
    }

});
</script>

@endsection