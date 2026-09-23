@extends('layouts.admin')

@section('title', 'Subjects')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            Subjects
        </h4>

        <div class="text-muted">
            Manage school subject master
        </div>
    </div>


    @can('subject.create')

        <a
            href="{{ route('subjects.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Add Subject
        </a>

    @endcan

</div>


<div class="card border-0 shadow-sm">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between align-items-center">

            <strong>
                Subject List
            </strong>

            <span class="text-muted">
                Total: {{ $subjects->total() }}
            </span>

        </div>

    </div>


    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th width="70">#</th>
                        <th>Subject</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Optional</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($subjects as $subject)

                        <tr>

                            <td>
                                {{ $subjects->firstItem() + $loop->index }}
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

                                @if($subject->type === 'theory')

                                    <span class="badge bg-primary">
                                        Theory
                                    </span>

                                @elseif($subject->type === 'practical')

                                    <span class="badge bg-info text-dark">
                                        Practical
                                    </span>

                                @elseif($subject->type === 'activity')

                                    <span class="badge bg-warning text-dark">
                                        Activity
                                    </span>

                                @endif

                            </td>

                            <td>

                                @if($subject->is_optional)

                                    <span class="badge bg-warning text-dark">
                                        Optional
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Compulsory
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $subject->sort_order }}
                            </td>

                            <td>

                                @if($subject->status)

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

                                @can('subject.edit')

                                    <a
                                        href="{{ route(
                                            'subjects.edit',
                                            $subject
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('subject.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'subjects.destroy',
                                            $subject
                                        ) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this subject?')"
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
                                colspan="8"
                                class="text-center py-5 text-muted"
                            >

                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>

                                No subjects found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($subjects->hasPages())

            <div class="mt-4">
                {{ $subjects->links() }}
            </div>

        @endif

    </div>

</div>

@endsection