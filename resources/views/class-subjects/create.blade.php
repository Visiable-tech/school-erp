@extends('layouts.admin')

@section('title', 'Class Subject Mapping')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Class Subject Mapping</h4>
        <div class="text-muted">
            Assign subjects to a class for an academic year
        </div>
    </div>

    <a href="{{ route('class-subjects.index') }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
        Back
    </a>

</div>


<form method="POST"
      action="{{ route('class-subjects.store') }}"
      id="classSubjectForm">

    @csrf

    {{-- Academic Year / Class --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <strong>Class Information</strong>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Academic Year
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="academic_year_id"
                        id="academic_year_id"
                        class="form-select @error('academic_year_id') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Academic Year
                        </option>

                        @foreach($academicYears as $year)

                            <option
                                value="{{ $year->id }}"
                                @selected(
                                    old('academic_year_id') == $year->id
                                )
                            >
                                {{ $year->name }}

                                @if($year->is_current)
                                    (Current)
                                @endif
                            </option>

                        @endforeach

                    </select>

                    @error('academic_year_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Class
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="school_class_id"
                        id="school_class_id"
                        class="form-select @error('school_class_id') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Class
                        </option>

                        @foreach($classes as $class)

                            <option
                                value="{{ $class->id }}"
                                @selected(
                                    old('school_class_id') == $class->id
                                )
                            >

                                {{ $class->name }}

                                @if($class->wing)
                                    - {{ $class->wing->name }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                    @error('school_class_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- Subjects --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <strong>Select Subjects</strong>

                <div>

                    <button type="button"
                            class="btn btn-sm btn-outline-primary"
                            id="selectAll">
                        Select All
                    </button>

                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            id="clearAll">
                        Clear
                    </button>

                </div>

            </div>

        </div>


        <div class="card-body p-0">

            @error('subjects')
                <div class="alert alert-danger m-3">
                    {{ $message }}
                </div>
            @enderror

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th width="80">Select</th>
                            <th>Subject</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th width="120">Optional</th>
                            <th width="130">Sort Order</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($subjects as $subject)

                            <tr>

                                <td>

                                    <input
                                        type="checkbox"
                                        class="form-check-input subject-checkbox"
                                        name="subjects[]"
                                        value="{{ $subject->id }}"
                                        id="subject_{{ $subject->id }}"
                                        @checked(
                                            in_array(
                                                $subject->id,
                                                old('subjects', [])
                                            )
                                        )
                                    >

                                </td>

                                <td>
                                    <strong>
                                        {{ $subject->name }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $subject->code ?: '-' }}
                                </td>

                                <td>
                                    {{ ucfirst($subject->type) }}
                                </td>

                                <td>

                                    <input
                                        type="checkbox"
                                        class="form-check-input optional-checkbox"
                                        name="optional[{{ $subject->id }}]"
                                        value="1"
                                        id="optional_{{ $subject->id }}"
                                        @checked(
                                            old(
                                                'optional.' . $subject->id,
                                                $subject->is_optional
                                            )
                                        )
                                    >

                                </td>

                                <td>

                                    <input
                                        type="number"
                                        name="sort_order[{{ $subject->id }}]"
                                        id="sort_order_{{ $subject->id }}"
                                        class="form-control form-control-sm"
                                        min="0"
                                        value="{{ old(
                                            'sort_order.' . $subject->id,
                                            $subject->sort_order ?? 0
                                        ) }}"
                                    >

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6"
                                    class="text-center py-5 text-muted">

                                    No active subjects found.

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <div class="card-footer bg-white py-3">

            <button type="submit"
                    class="btn btn-primary">

                <i class="bi bi-check-lg"></i>
                Save Subject Mapping

            </button>

            <a href="{{ route('class-subjects.index') }}"
               class="btn btn-light ms-2">
                Cancel
            </a>

        </div>

    </div>

</form>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const academicYear =
        document.getElementById('academic_year_id');

    const schoolClass =
        document.getElementById('school_class_id');

    const selectAll =
        document.getElementById('selectAll');

    const clearAll =
        document.getElementById('clearAll');


    function clearMappings()
    {
        document.querySelectorAll('.subject-checkbox')
            .forEach(function (checkbox) {

                checkbox.checked = false;

            });

        document.querySelectorAll('.optional-checkbox')
            .forEach(function (checkbox) {

                checkbox.checked = false;

            });
    }


    function loadMappedSubjects()
    {
        const academicYearId = academicYear.value;
        const schoolClassId = schoolClass.value;

        /*
         * Do not clear anything until both
         * selections have been made.
         */
        if (!academicYearId || !schoolClassId) {
            return;
        }

        clearMappings();


        const url =
            "{{ route('class-subjects.mapped-subjects') }}"
            + "?academic_year_id="
            + encodeURIComponent(academicYearId)
            + "&school_class_id="
            + encodeURIComponent(schoolClassId);


        fetch(url)
            .then(function (response) {

                if (!response.ok) {
                    throw new Error(
                        'Unable to load mappings'
                    );
                }

                return response.json();

            })
            .then(function (data) {

                data.subjects.forEach(function (mapping) {

                    const subject =
                        document.getElementById(
                            'subject_' + mapping.subject_id
                        );

                    const optional =
                        document.getElementById(
                            'optional_' + mapping.subject_id
                        );

                    const sortOrder =
                        document.getElementById(
                            'sort_order_' + mapping.subject_id
                        );


                    if (subject) {
                        subject.checked = true;
                    }

                    if (optional) {
                        optional.checked =
                            Number(mapping.is_optional) === 1;
                    }

                    if (sortOrder) {
                        sortOrder.value =
                            mapping.sort_order ?? 0;
                    }

                });

            })
            .catch(function (error) {

                console.error(error);

            });
    }


    academicYear.addEventListener(
        'change',
        loadMappedSubjects
    );


    schoolClass.addEventListener(
        'change',
        loadMappedSubjects
    );


    selectAll.addEventListener(
        'click',
        function () {

            document
                .querySelectorAll('.subject-checkbox')
                .forEach(function (checkbox) {

                    checkbox.checked = true;

                });

        }
    );


    clearAll.addEventListener(
        'click',
        function () {

            document
                .querySelectorAll('.subject-checkbox')
                .forEach(function (checkbox) {

                    checkbox.checked = false;

                });

        }
    );


    /*
     * Useful after Laravel validation redirects back.
     */
    if (
        academicYear.value &&
        schoolClass.value &&
        !@json(old('subjects'))
    ) {
        loadMappedSubjects();
    }

});

</script>

@endsection