@extends('layouts.admin')

@section('title', 'Sections')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Sections
        </h4>

        <div class="text-muted">
            Manage class sections by academic year
        </div>

    </div>

    @can('section.create')

        <a
            href="{{ route('sections.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Add Section
        </a>

    @endcan

</div>


<div class="card border-0 shadow-sm">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between">

            <strong>Section List</strong>

            <span class="text-muted">
                Total: {{ $sections->count() }}
            </span>

        </div>

    </div>


    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        @can('section.edit')<th width="55">Move</th>@endcan
                        <th>#</th>
                        <th>Academic Year</th>
                        <th>Wing</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>

                </thead>

                <tbody id="sortableSections">

                    @forelse($sections as $section)

                        <tr data-id="{{ $section->id }}"
                            data-year="{{ $section->academic_year_id }}"
                            data-class="{{ $section->school_class_id }}">
                            @can('section.edit')
                                <td><span class="section-drag-handle" title="Drag within same class and academic year"><i class="bi bi-grip-vertical"></i></span></td>
                            @endcan
                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>

                                {{ $section->academicYear->name ?? '-' }}

                                @if($section->academicYear?->is_current)

                                    <span class="badge bg-primary ms-1">
                                        Current
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $section->schoolClass?->wing?->name ?? '-' }}
                            </td>

                            <td>
                                <strong>
                                    {{ $section->schoolClass->name ?? '-' }}
                                </strong>
                            </td>

                            <td>

                                <span class="badge bg-light text-dark border">
                                    {{ $section->name }}
                                </span>

                            </td>

                            <td>
                                {{ $section->capacity ?: '-' }}
                            </td>

                            <td>

                                @if($section->status)

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            <td>

                                @can('section.edit')

                                    <a
                                        href="{{ route(
                                            'sections.edit',
                                            $section
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('section.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'sections.destroy',
                                            $section
                                        ) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this section?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                @endcan

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="{{ auth()->user()->can('section.edit') ? 9 : 8 }}"
                                class="text-center py-5 text-muted"
                            >

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                No sections found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


    </div>

</div>

@can('section.edit')
<style>
.section-drag-handle { cursor: grab; font-size: 21px; color: #64748b; user-select: none; }
.section-drag-handle:active { cursor: grabbing; }
.section-sort-ghost { background: #dbeafe !important; opacity: .6; }
</style>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('sortableSections');
    if (!tbody || typeof Sortable === 'undefined') return;
    const groupOf = row => row.dataset.year + ':' + row.dataset.class;
    let saving = false;
    new Sortable(tbody, {
        animation: 150,
        handle: '.section-drag-handle',
        ghostClass: 'section-sort-ghost',
        onMove: function (evt) {
            return groupOf(evt.dragged) === groupOf(evt.related);
        },
        onEnd: async function (evt) {
            if (evt.oldIndex === evt.newIndex || saving) return;
            const row = evt.item;
            const year = row.dataset.year, cls = row.dataset.class;
            const ids = [...tbody.querySelectorAll('tr[data-id]')]
                .filter(r => r.dataset.year === year && r.dataset.class === cls)
                .map(r => Number(r.dataset.id));
            saving = true;
            try {
                const response = await fetch(@json(route('sections.reorder')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token())
                    },
                    body: JSON.stringify({ academic_year_id: Number(year), school_class_id: Number(cls), ids })
                });
                if (!response.ok) throw new Error((await response.json().catch(() => ({}))).message || 'Unable to save section order');
                window.location.reload();
            } catch (error) {
                alert(error.message);
                window.location.reload();
            } finally { saving = false; }
        }
    });
});
</script>
@endcan
@endsection