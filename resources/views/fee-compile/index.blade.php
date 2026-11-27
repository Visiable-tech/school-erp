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