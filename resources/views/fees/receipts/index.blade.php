@extends('layouts.admin')

@section('title', 'Fee Receipts')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">Fee Receipts</h4>
            <small class="text-muted">
                View collected and cancelled fee receipts.
            </small>
        </div>

        @can('fee-collection.create')

            <a href="{{ route('fee-receipts.create') }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Collect Fee

            </a>

        @endcan

    </div>


    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET">

                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year_id" id="receipt_year" class="form-select">
                            <option value="">All Academic Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(request('academic_year_id') == $year->id)>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Class</label>
                        <select name="school_class_id" id="receipt_class" class="form-select">
                            <option value="">All Classes</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Section</label>
                        <select name="section_id" id="receipt_section" class="form-select">
                            <option value="">All Sections</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Student / Admission No.</label>
                        <input type="text" name="student" class="form-control"
                               placeholder="Student / Admission No." value="{{ request('student') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Receipt No.</label>
                        <input type="text" name="receipt_no" class="form-control"
                               placeholder="Receipt No." value="{{ request('receipt_no') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="posted" @selected(request('status') == 'posted')>Posted</option>
                            <option value="cancelled" @selected(request('status') == 'cancelled')>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button class="btn btn-primary" type="submit">Filter</button>
                        <a href="{{ route('fee-receipts.index') }}" class="btn btn-light">Reset</a>
                    </div>
                </div>

            </form>

        </div>

    </div>


    <div class="card shadow-sm">

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0 align-middle">

                <thead class="table-light">

                    <tr>
                        <th>#</th>
                        <th>Receipt No.</th>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Academic Year</th>
                        <th>Mode</th>
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($receipts as $receipt)

                        <tr>

                            <td>
                                {{ $receipts->firstItem() + $loop->index }}
                            </td>


                            <td>
                                <strong>
                                    {{ $receipt->receipt_no }}
                                </strong>
                            </td>


                            <td>
                                {{ optional($receipt->payment_date)->format('d-m-Y') }}
                            </td>


                            <td>

                                {{ $receipt->student->student_name
                                    ?? $receipt->student->name
                                    ?? '-' }}

                                <br>

                                <small class="text-muted">
                                    {{ $receipt->student->admission_no ?? '' }}
                                </small>

                            </td>


                            <td>
                                {{ $receipt->academicYear->name ?? '-' }}
                            </td>


                            <td>
                                {{ $receipt->paymentMode->name
                                    ?? ucfirst($receipt->payment_mode ?? '-') }}
                            </td>


                            <td class="text-end fw-semibold">

                                ₹{{ number_format($receipt->total_amount, 2) }}

                            </td>


                            <td>

                                @if($receipt->status === 'posted')

                                    <span class="badge bg-success">
                                        Posted
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        Cancelled
                                    </span>

                                @endif

                            </td>


                            <td>

                                <a href="{{ route('fee-receipts.show', $receipt) }}"
                                   class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-eye"></i>

                                </a>


                                @can('fee-collection.receipt')

                                    <a href="{{ route('fee-receipts.print', $receipt) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-dark">

                                        <i class="bi bi-printer"></i>

                                    </a>

                                @endcan
                                @role('Super Admin')
                                    @if($receipt->status === 'posted')
                                        @if($receipt->isModificationLocked())
                                            <span class="badge bg-secondary" title="7-day modification period expired"><i class="bi bi-lock"></i> Locked</span>
                                        @else
                                            <a href="{{ route('fee-receipts.show', $receipt) }}#receipt-correction" class="btn btn-sm btn-outline-warning" title="Correct receipt"><i class="bi bi-pencil-square"></i></a>
                                            <a href="{{ route('fee-receipts.show', $receipt) }}#receipt-correction" class="btn btn-sm btn-outline-danger" title="Cancel receipt"><i class="bi bi-x-circle"></i></a>
                                        @endif
                                    @endif
                                @endrole

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9"
                                class="text-center text-muted py-5">

                                No fee receipts found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($receipts->hasPages())

            <div class="card-footer">
                {{ $receipts->links() }}
            </div>

        @endif

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const options = {{ Illuminate\Support\Js::from($enrollmentOptions) }};
    const year = document.getElementById('receipt_year');
    const classEl = document.getElementById('receipt_class');
    const section = document.getElementById('receipt_section');
    const initialClass = @json((string) request('school_class_id', ''));
    const initialSection = @json((string) request('section_id', ''));

    function fill(select, placeholder, records, idField, labelField, selected) {
        select.replaceChildren(new Option(placeholder, ''));
        const unique = new Map();
        records.forEach(row => {
            if (row[idField] !== null && row[idField] !== undefined) {
                unique.set(String(row[idField]), String(row[labelField]));
            }
        });
        [...unique].sort((a,b) => a[1].localeCompare(b[1], undefined, {numeric:true}))
            .forEach(([id,label]) => select.add(new Option(label, id)));
        select.value = selected || '';
    }
    function updateSections(selected = '') {
        const records = options.filter(row =>
            (!year.value || String(row.year_id) === year.value) &&
            (!classEl.value || String(row.class_id) === classEl.value));
        fill(section, 'All Sections', records, 'section_id', 'section_name', selected);
    }
    function updateClasses(selected = '', selectedSection = '') {
        const records = options.filter(row => !year.value || String(row.year_id) === year.value);
        fill(classEl, 'All Classes', records, 'class_id', 'class_name', selected);
        updateSections(selectedSection);
    }
    year.addEventListener('change', () => updateClasses());
    classEl.addEventListener('change', () => updateSections());
    updateClasses(initialClass, initialSection);
});
</script>

@endsection