<script>

document.addEventListener('DOMContentLoaded', function () {

    const academicYear =
        document.getElementById('academic_year_id');

    const container =
        document.getElementById('sectionContainer');

    const selectedSections =
        @json(
            old(
                'sections',
                isset($sectionGroup)
                    ? $sectionGroup->sections->pluck('id')->toArray()
                    : []
            )
        );


    function loadSections()
    {
        const academicYearId =
            academicYear.value;


        if (!academicYearId) {

            container.innerHTML = `
                <div class="text-muted text-center py-4">
                    Select Academic Year to load sections.
                </div>
            `;

            return;
        }


        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm"></div>
                Loading sections...
            </div>
        `;


        const url =
            "{{ route('section-groups.sections') }}"
            + "?academic_year_id="
            + encodeURIComponent(academicYearId);


        fetch(url)
            .then(function (response) {

                if (!response.ok) {
                    throw new Error(
                        'Unable to load sections.'
                    );
                }

                return response.json();

            })
            .then(function (data) {

                if (!data.sections.length) {

                    container.innerHTML = `
                        <div class="text-muted text-center py-4">
                            No sections found for this academic year.
                        </div>
                    `;

                    return;
                }


                let html = `
                    <div class="row">
                `;


                data.sections.forEach(
                    function (section) {

                        const checked =
                            selectedSections
                                .map(Number)
                                .includes(
                                    Number(section.id)
                                )
                                ? 'checked'
                                : '';


                        let title =
                            section.class_name
                            + ' - '
                            + section.section_name;


                        if (section.wing_name) {

                            title +=
                                ' (' +
                                section.wing_name +
                                ')';

                        }


                        html += `

                            <div class="col-md-4 mb-3">

                                <div class="border rounded p-3">

                                    <div class="form-check">

                                        <input
                                            type="checkbox"
                                            name="sections[]"
                                            value="${section.id}"
                                            class="form-check-input section-checkbox"
                                            id="section_${section.id}"
                                            ${checked}
                                        >

                                        <label
                                            class="form-check-label"
                                            for="section_${section.id}"
                                        >
                                            ${title}
                                        </label>

                                    </div>

                                </div>

                            </div>

                        `;

                    }
                );


                html += `
                    </div>
                `;


                container.innerHTML = html;

            })
            .catch(function (error) {

                console.error(error);

                container.innerHTML = `
                    <div class="alert alert-danger mb-0">
                        Unable to load sections.
                    </div>
                `;

            });
    }


    academicYear.addEventListener(
        'change',
        function () {

            /*
             * Academic year changed manually.
             * Old selections should not carry over.
             */
            selectedSections.length = 0;

            loadSections();

        }
    );


    document
        .getElementById('selectAllSections')
        .addEventListener(
            'click',
            function () {

                document
                    .querySelectorAll(
                        '.section-checkbox'
                    )
                    .forEach(
                        function (checkbox) {

                            checkbox.checked = true;

                        }
                    );

            }
        );


    document
        .getElementById('clearSections')
        .addEventListener(
            'click',
            function () {

                document
                    .querySelectorAll(
                        '.section-checkbox'
                    )
                    .forEach(
                        function (checkbox) {

                            checkbox.checked = false;

                        }
                    );

            }
        );


    /*
     * Edit page or validation redirect.
     */
    if (academicYear.value) {
        loadSections();
    }

});

</script>