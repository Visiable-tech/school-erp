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
                                class="form-select"
                                required>

                            <option value="">
                                Select Class
                            </option>

                            @foreach(
                                $classes as $class
                            )

                                <option
                                    value="{{ $class->id }}">

                                    {{ $class->name }}

                                </option>

                            @endforeach

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

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const year =
            document.getElementById(
                'academic_year_id'
            );

        const schoolClass =
            document.getElementById(
                'school_class_id'
            );

        const section =
            document.getElementById(
                'section_id'
            );

        const template =
            document.getElementById(
                'fee_structure_id'
            );


        async function loadData()
        {
            section.innerHTML =
                '<option value="">All Sections</option>';

            template.innerHTML =
                '<option value="">Select Fee Template</option>';


            if (
                !year.value ||
                !schoolClass.value
            ) {
                return;
            }


            const params =
                new URLSearchParams({
                    academic_year_id:
                        year.value,

                    school_class_id:
                        schoolClass.value
                });


            try {

                const [
                    sectionResponse,
                    templateResponse
                ] = await Promise.all([

                    fetch(
                        '{{ route("fee-compile.sections") }}'
                        + '?' + params
                    ),

                    fetch(
                        '{{ route("fee-compile.templates") }}'
                        + '?' + params
                    )
                ]);


                if (
                    !sectionResponse.ok ||
                    !templateResponse.ok
                ) {
                    throw new Error(
                        'Unable to load data.'
                    );
                }


                const sections =
                    await sectionResponse.json();

                const templates =
                    await templateResponse.json();


                sections.forEach(
                    function (item) {

                        const option =
                            document.createElement(
                                'option'
                            );

                        option.value =
                            item.id;

                        option.textContent =
                            item.name;

                        section.appendChild(
                            option
                        );
                    }
                );


                templates.forEach(
                    function (item) {

                        const option =
                            document.createElement(
                                'option'
                            );

                        option.value =
                            item.id;

                        option.textContent =
                            item.name;

                        template.appendChild(
                            option
                        );
                    }
                );

            } catch (error) {

                console.error(error);

                alert(
                    'Unable to load Sections or Fee Templates.'
                );
            }
        }


        year.addEventListener(
            'change',
            loadData
        );

        schoolClass.addEventListener(
            'change',
            loadData
        );

    }
);

</script>

@endsection