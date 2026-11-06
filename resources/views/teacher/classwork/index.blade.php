@extends('layouts.admin')
@section('title', 'Classwork / Assignments')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Classwork / Assignments</h4>
        @hasanyrole('Teacher|Super Admin')
            <a href="{{ route('teacher.classwork.create') }}" class="btn btn-primary">+ Upload Classwork</a>
        @endhasanyrole
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3 d-flex flex-wrap gap-2">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'not-uploaded' => 'Not Uploaded'] as $key => $label)
            <a class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-outline-primary' }}"
               href="{{ route('teacher.classwork.index', ['status' => $key]) }}">{{ $label }}</a>
        @endforeach
    </div>

    @if($status === 'not-uploaded')
        <form method="GET" class="mb-3">
            <input type="hidden" name="status" value="not-uploaded">
            <label>Work Date
                <input class="form-control" type="date" name="date" value="{{ request('date', now()->toDateString()) }}">
            </label>
            <button class="btn btn-secondary">Check</button>
        </form>
        <div class="alert alert-info">Not Uploaded means no classwork/homework/assignment was submitted for that teaching assignment on the selected date. This does not imply a missed scheduled lesson.</div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead><tr><th>Class</th><th>Section</th><th>Subject</th></tr></thead>
                <tbody>
                    @forelse($missing as $a)
                        <tr>
                            <td>{{ $a->schoolClass?->name }}</td>
                            <td>{{ $a->section?->name }}</td>
                            <td>{{ $a->subject?->name ?? 'General' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">All assigned sections have an upload for this date.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead>
                    <tr><th>Date</th><th>Title</th><th>Type</th><th>Class / Section</th><th>Subject</th><th>Teacher</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->work_date?->format('d M Y') }}</td>
                            <td>
                                <strong>{{ $item->title }}</strong>
                                <div class="small text-muted">{{ $item->description }}</div>
                                @if($item->review_note)
                                    <div class="small text-danger">Review: {{ $item->review_note }}</div>
                                @endif
                            </td>
                            <td>{{ ucfirst($item->type) }}</td>
                            <td>{{ $item->assignment?->schoolClass?->name }} / {{ $item->assignment?->section?->name }}</td>
                            <td>{{ $item->assignment?->subject?->name ?? 'General' }}</td>
                            <td>{{ $item->teacher?->name }}</td>
                            <td>{{ ucfirst($item->approval_status) }}</td>
                            <td>
                                @if($item->approval_status === 'pending' && $item->teacher_user_id === auth()->id())
                                    <a class="btn btn-sm btn-outline-primary mb-1" href="{{ route('teacher.classwork.edit', $item) }}">Edit</a>
                                @endif
                                @can('teacher-classwork.review')
                                    @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin']) && $item->approval_status === 'pending')
                                        <form method="POST" action="{{ route('teacher.classwork.review', $item) }}">
                                            @csrf
                                            @method('PATCH')
                                            <textarea name="review_note" class="form-control form-control-sm mb-1" placeholder="Review note (optional)"></textarea>
                                            <button class="btn btn-sm btn-success" name="approval_status" value="approved">Approve</button>
                                            <button class="btn btn-sm btn-danger" name="approval_status" value="rejected">Reject</button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    @endif
</div>
@endsection
