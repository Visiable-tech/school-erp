@extends('layouts.admin')

@section('title', 'Classes')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Classes
        </h4>

        <div class="text-muted">
            Manage school classes and their wings
        </div>

    </div>


    @can('class.create')

        <a
            href="{{ route('school-classes.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Add Class
        </a>

    @endcan

</div>


<div class="card border-0 shadow-sm">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between">

            <strong>Class List</strong>

            <span class="text-muted">
                Total: {{ $classes->count() }}
            </span>

        </div>

    </div>


    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        @can('class.edit')<th width="55">Move</th>@endcan
                        <th width="70">#</th>
                        <th>Class</th>
                        <th>Code</th>
                        <th>Wing</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>

                </thead>

                <tbody id="sortableClasses">

                    @forelse($classes as $schoolClass)

                        <tr data-id="{{ $schoolClass->id }}">
                            @can('class.edit')
                            <td><span class="class-drag-handle" title="Drag to reorder" aria-label="Drag to reorder" style="cursor:grab;touch-action:none;font-size:20px">&#9776;</span></td>
                            @endcan
                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                <strong>
                                    {{ $schoolClass->name }}
                                </strong>
                            </td>

                            <td>
                                {{ $schoolClass->code ?: '-' }}
                            </td>

                            <td>

                                @if($schoolClass->wing)

                                    <span class="badge bg-light text-dark border">
                                        {{ $schoolClass->wing->name }}
                                    </span>

                                @else

                                    -

                                @endif

                            </td>

                            <td class="class-sort-order">
                                {{ $schoolClass->sort_order }}
                            </td>

                            <td>

                                @if($schoolClass->status)

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

                                @can('class.edit')

                                    <a
                                        href="{{ route(
                                            'school-classes.edit',
                                            $schoolClass
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('class.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'school-classes.destroy',
                                            $schoolClass
                                        ) }}"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                            'Are you sure you want to delete this class?'
                                        )"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete"
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
                                @can('class.edit') colspan="8" @else colspan="7" @endcan
                                class="text-center py-5 text-muted"
                            >

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                No classes found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



    </div>

</div>

@endsection
@can('class.edit')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('sortableClasses');
    if (!tbody || typeof Sortable === 'undefined') return;
    let saving = false;
    new Sortable(tbody, {
        animation: 150,
        handle: '.class-drag-handle',
        ghostClass: 'table-active',
        onEnd: async function (evt) {
            if (evt.oldIndex === evt.newIndex || saving) return;
            saving = true;
            const ids = [...tbody.querySelectorAll('tr[data-id]')].map(r => Number(r.dataset.id));
            try {
                const response = await fetch(@json(route('school-classes.reorder')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token())
                    },
                    body: JSON.stringify({ ids })
                });
                if (!response.ok) throw new Error('Could not save class order.');
                tbody.querySelectorAll('tr[data-id]').forEach((row, index) => {
                    const cells = row.querySelectorAll('td');
                    const numberCell = cells[1]; // Move column precedes #
                    const sortCell = row.querySelector('.class-sort-order');
                    if (numberCell) numberCell.textContent = index + 1;
                    if (sortCell) sortCell.textContent = index + 1;
                });
            } catch (error) {
                alert(error.message + ' Reloading original order.');
                window.location.reload();
            } finally { saving = false; }
        }
    });
});
</script>
@endpush
@endcan
