{{-- Academic Setup --}}

        <div class="menu-title">
            Academic
        </div>


        <a
            data-bs-toggle="collapse"
            href="#academicSetup">

            <i class="bi bi-building"></i>

            <span class="flex-grow-1">
                Academic Setup
            </span>

            <i class="bi bi-chevron-down"></i>

        </a>


        <div
            class="collapse
            {{ request()->routeIs(
                    'academic-years.*',
                    'wings.*',
                    'school-classes.*',
                    'sections.*',
                    'section-groups.*',
                    'subjects.*',
                    'sections.*',
                    'subjects.*',
                    'class-subjects.*',
                    'calendar-event-types.*',
                    'school-calendar.*'
            ) ? 'true' : 'false' }}"
            id="academicSetup">

            <div class="sidebar-submenu">

                @can('academic-year.view')

                    <a
                        href="{{ route('academic-years.index') }}"
                        class="{{ request()->routeIs('academic-years.*') ? 'active' : '' }}">

                        Academic Years

                    </a>

                @endcan


                @can('wing.view')

                    <a
                        href="{{ route('wings.index') }}"
                        class="{{ request()->routeIs('wings.*') ? 'active' : '' }}">

                        Wings

                    </a>

                @endcan


                @can('class.view')

                    <a
                        href="{{ route('school-classes.index') }}"
                        class="{{ request()->routeIs('school-classes.*') ? 'active' : '' }}"
                    >
                        Classes
                    </a>

                @endcan

                @can('section.view')

                    <a
                        href="{{ route('sections.index') }}"
                        class="{{ request()->routeIs('sections.*') ? 'active' : '' }}"
                    >
                        Sections
                    </a>

                @endcan

                @can('section-group.view')

                    <a
                        href="{{ route('section-groups.index') }}"
                        class="{{ request()->routeIs('section-groups.*') ? 'active' : '' }}"
                    >
                        Section Groups
                    </a>

                @endcan

                @can('subject.view')

                    <a
                        href="{{ route('subjects.index') }}"
                        class="{{ request()->routeIs('subjects.*') ? 'active' : '' }}"
                    >
                        Subjects
                    </a>

                @endcan

                @can('class-subject.view')

                    <a
                        href="{{ route('class-subjects.index') }}"
                        class="{{ request()->routeIs('class-subjects.*') ? 'active' : '' }}"
                    >
                        Class Subject Mapping
                    </a>

                @endcan

                @can('calendar.view')

                    <a
                        href="{{ route('calendar-event-types.index') }}"
                        class="{{ request()->routeIs('calendar-event-types.*') ? 'active' : '' }}"
                    >
                        Calendar Event Types
                    </a>

                @endcan

                @can('calendar.view')

                    <a
                        href="{{ route('school-calendar.index') }}"
                        class="{{ request()->routeIs('school-calendar.*') ? 'active' : '' }}"
                    >
                        School Calendar
                    </a>

                @endcan

            </div>

        </div>


        