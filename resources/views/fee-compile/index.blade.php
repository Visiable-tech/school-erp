@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Compile Fee
            </h4>

            <small class="text-muted">
                Fee Management → Setup → Compile Fee
            </small>

        </div>

    </div>


    <div class="alert alert-info">

        <i class="bi bi-info-circle"></i>

        Compile Fee will generate student-wise
        fee dues from the selected Fee Template.

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white">

            <strong>
                Select Students & Fee Template
            </strong>

        </div>


        <div class="card-body">

            <form method="POST"
                  action="{{ route(
                      'fee-compile.preview'
                  ) }}">

                @csrf


                <div class="row g-3">


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

                            @foreach(
                                $academicYears as $year
                            )

                                <option
                                    value="{{ $year->id }}"
                                    {{ old(
                                        'academic_year_id'
                                    ) == $year->id
                                        ? 'selected'
                                        : '' }}>

                                    {{ $year->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Class
                            <span class="text-danger">*</span>
                        </label>

                        <select name="school_class_id"
                                id="school_class_id"
                                disabled
                                class="form-select"
                                required>

                            <option value="">
                                Select Class
                            </option>



                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Section
                        </label>

                        <select name="section_id"
                                id="section_id"
                                class="form-select">

                            <option value="">
                                All Sections
                            </option>

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Fee Template
                            <span class="text-danger">*</span>
                        </label>

                        <select name="fee_structure_id"
                                id="fee_structure_id"
                                class="form-select"
                                required>

                            <option value="">
                                Select Fee Template
                            </option>

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Compile Date
                            <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               name="compile_date"
                               class="form-control"
                               value="{{ old(
                                   'compile_date',
                                   date('Y-m-d')
                               ) }}"
                               required>

                    </div>


                    <div class="col-md-9
                                d-flex
                                align-items-end">

                        <button type="submit"
                                class="btn btn-primary">

                            <i class="bi bi-eye"></i>

                            Preview Students

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <div class="card border-0 shadow-sm mt-4" id="compiled-history">
        <div class="card-header bg-white"><strong>Already Compiled Fee List</strong></div>
        <div class="card-body">
            <form method="GET" action="{{ url()->current() }}#compiled-history" class="row g-2 mb-3">
                <div class="col-md-3"><label class="form-label">Academic Year</label><select name="history_year" class="form-select"><option value="">All Years</option>@foreach($academicYears as $year)<option value="{{ $year->id }}" @selected(request('history_year') == $year->id)>{{ $year->name }}</option>@endforeach</select></div>
                <div class="col-md-3"><label class="form-label">Class</label><select name="history_class" class="form-select"><option value="">All Classes</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('history_class') == $class->id)>{{ $class->name }}</option>@endforeach</select></div>
                <div class="col-md-3"><label class="form-label">Section</label><select name="history_section" class="form-select"><option value="">All Sections</option>@foreach($historySections as $section)<option value="{{ $section->id }}" @selected(request('history_section') == $section->id)>{{ $section->name }} ({{ $classes->firstWhere('id', $section->school_class_id)?->name ?? '-' }}, {{ $academicYears->firstWhere('id', $section->academic_year_id)?->name ?? '-' }})</option>@endforeach</select></div>
                <div class="col-md-3 d-flex align-items-end gap-2"><button class="btn btn-primary">Filter</button><a class="btn btn-light" href="{{ url()->current() }}#compiled-history">Reset</a></div>
            </form>
            <div class="row g-2 mb-3">
                @foreach(['Total Students' => $summaryRows->sum('total_students'), 'Compiled Students' => $summaryRows->sum('compiled_students'), 'Pending Students' => $summaryRows->sum('total_students') - $summaryRows->sum('compiled_students')] as $label => $value)
                    <div class="col-md-4"><div class="border rounded p-3"><small>{{ $label }}</small><h4 class="mb-0">{{ $value }}</h4></div></div>
                @endforeach
            </div>
            <h6>Class / Section Summary (active enrollments)</h6>
            <div class="table-responsive"><table class="table table-bordered table-striped align-middle">
                <thead class="table-light"><tr><th>Academic Year</th><th>Class</th><th>Section</th><th>Total</th><th>Compiled</th><th>Pending</th><th>Status</th></tr></thead>
                <tbody>@forelse($summaryRows as $r)<tr><td>{{ $r->year_name }}</td><td>{{ $r->class_name }}</td><td>{{ $r->section_name ?? '-' }}</td><td>{{ $r->total_students }}</td><td>{{ $r->compiled_students }}</td><td>{{ $r->total_students - $r->compiled_students }}</td><td><span class="badge bg-{{ $r->compiled_students == $r->total_students ? 'success' : ($r->compiled_students ? 'warning text-dark' : 'secondary') }}">{{ $r->compiled_students == $r->total_students ? 'Compiled' : ($r->compiled_students ? 'Partial' : 'Pending') }}</span></td></tr>@empty<tr><td colspan="7" class="text-center">No enrollments found.</td></tr>@endforelse</tbody>
            </table></div>
            <h6 class="mt-3">Student-wise Compilation History ({{ $compiledList->total() }})</h6>
            <div class="table-responsive"><table class="table table-bordered table-striped align-middle">
                <thead class="table-light"><tr><th>Admission No.</th><th>Student</th><th>Year</th><th>Class / Section</th><th>Fee Template</th><th>Compile Date</th><th>Dues</th><th>Gross Fee</th><th>Assignment Status</th></tr></thead>
                <tbody>@forelse($compiledList as $a)<tr><td>{{ $a->student?->admission_no ?? '-' }}</td><td>{{ $a->student?->student_name ?? '-' }}</td><td>{{ $a->academicYear?->name ?? '-' }}</td><td>{{ $a->enrollment?->schoolClass?->name ?? '-' }} / {{ $a->enrollment?->section?->name ?? '-' }}</td><td>{{ $a->feeStructure?->name ?? '-' }}</td><td>{{ $a->assigned_date?->format('d-m-Y') ?? '-' }}</td><td>{{ $a->dues_count }}</td><td>₹{{ number_format($a->dues_sum_base_amount ?? 0, 2) }}</td><td>{{ $a->status ? 'Active' : 'Inactive' }}</td></tr>@empty<tr><td colspan="9" class="text-center">No compiled fee assignments found.</td></tr>@endforelse</tbody>
            </table></div>
            {{ $compiledList->fragment('compiled-history')->links('pagination::bootstrap-5') }}
            <small class="text-muted">Compiled counts follow the existing student/year assignment check. Class and section reflect the linked enrollment's current values. Gross fee includes all linked dues before adjustments.</small>
        </div>
    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const year = document.getElementById('academic_year_id');
    const schoolClass = document.getElementById('school_class_id');
    const section = document.getElementById('section_id');
    const template = document.getElementById('fee_structure_id');
    const submit = template.closest('form').querySelector('button[type="submit"]');
    const classes = @json($classes->map(function ($class) { return $class->only(['id', 'name']); })->values());
    const templateClasses = @json($templateClasses);
    const oldClass = @json(old('school_class_id'));
    const oldSection = @json(old('section_id'));
    const oldTemplate = @json(old('fee_structure_id'));
    let requestId = 0;

    function reset(select, label) {
        select.replaceChildren(new Option(label, ''));
    }
    function append(select, items) {
        items.forEach(item => select.add(new Option(item.name, item.id)));
    }
    function loadClasses(restore = false) {
        const allowed = (templateClasses[year.value] || []).map(String);
        const available = classes.filter(item => allowed.includes(String(item.id)));
        reset(schoolClass, !year.value ? 'Select Academic Year First' :
            available.length ? 'Select Class' : 'No Classes with Fee Templates');
        append(schoolClass, available);
        schoolClass.disabled = !available.length;
        if (restore && oldClass) schoolClass.value = oldClass;
        loadData(restore);
    }
    async function loadData(restore = false) {
        const id = ++requestId;
        reset(section, 'All Sections');
        reset(template, 'Select Fee Template');
        section.disabled = template.disabled = submit.disabled = true;
        if (!year.value || !schoolClass.value) return;
        const params = new URLSearchParams({academic_year_id: year.value,
            school_class_id: schoolClass.value});
        try {
            const responses = await Promise.all([
                fetch('{{ route("fee-compile.sections") }}?' + params, {headers: {Accept: 'application/json'}}),
                fetch('{{ route("fee-compile.templates") }}?' + params, {headers: {Accept: 'application/json'}})
            ]);
            if (responses.some(response => !response.ok)) throw new Error('Unable to load data');
            const [sections, templates] = await Promise.all(responses.map(response => response.json()));
            if (id !== requestId) return;
            append(section, sections);
            append(template, templates);
            section.disabled = false;
            template.disabled = !templates.length;
            if (!templates.length) reset(template, 'No Fee Templates Available');
            if (restore && oldSection) { section.value = oldSection; section.dispatchEvent(new Event('change')); return; }
            if (restore && oldTemplate) template.value = oldTemplate;
            submit.disabled = !template.value;
        } catch (error) {
            if (id !== requestId) return;
            reset(template, 'Unable to load Fee Templates');
            console.error(error);
        }
    }
    section.addEventListener('change', async () => {
        const id = ++requestId;
        reset(template, 'Select Fee Template');
        template.disabled = submit.disabled = true;
        const params = new URLSearchParams({academic_year_id: year.value, school_class_id: schoolClass.value, section_id: section.value});
        try {
            const response = await fetch('{{ route("fee-compile.templates") }}?' + params, {headers: {Accept: 'application/json'}});
            if (!response.ok) throw new Error('Unable to load templates');
            const rows = await response.json();
            if (id !== requestId) return;
            append(template, rows);
            template.disabled = !rows.length;
        } catch (e) { if (id === requestId) reset(template, 'Unable to load Fee Templates'); }
    });
    year.addEventListener('change' , () => loadClasses());
    schoolClass.addEventListener('change', () => loadData());
    template.addEventListener('change', () => { submit.disabled = !template.value; });
    loadClasses(true);
});
</script>

@endsection