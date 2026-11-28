@extends('layouts.admin')
@section('content')
@php
    $transportStops = [];
    $enrollmentYears = [];
    foreach ($routes as $route) {
        $transportStops[$route->id] = [];
        foreach ($route->stops as $stop) {
            $rates = [];
            foreach ($stop->rates as $rate) {
                $rates[] = ['academic_year_id' => $rate->academic_year_id, 'monthly_amount' => $rate->monthly_amount];
            }
            $transportStops[$route->id][] = ['id' => $stop->id, 'name' => $stop->name, 'rates' => $rates];
        }
    }
    foreach ($enrollments as $enrollment) {
        $enrollmentYears[$enrollment->id] = $enrollment->academic_year_id;
    }
    $yearNames = $years->pluck('name', 'id');
@endphp
<div class="container-fluid py-3">
    <h3 class="mb-4">Transport Fee Management</h3>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="card mb-4"><div class="card-header"><h5 class="mb-0" id="transport-routes">Transport Route Master</h5></div><div class="card-body">
        <form method="POST" action="{{ route('transport.routes.save') }}" id="route-form" class="row g-3">@csrf
            <input type="hidden" name="route_id" value=""><input type="hidden" name="status" value="1">
            <div class="col-md-2"><label class="form-label">Route Code</label><input class="form-control" name="code" required></div>
            <div class="col-md-3"><label class="form-label">Route Name</label><input class="form-control" name="name" required></div>
            <div class="col-md-2"><label class="form-label">Academic Year</label><select class="form-select" name="academic_year_id" required><option value="">Select Year</option>@foreach($years as $year)<option value="{{ $year->id }}">{{ $year->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Default Monthly Fee</label><input class="form-control" name="monthly_amount" type="number" min="0" step="0.01" required></div>
            <div class="col-md-3 d-flex align-items-end gap-2"><button id="route-submit" class="btn btn-primary">Save Route</button><button type="button" class="btn btn-secondary" onclick="resetRoute()">Reset</button></div>
        </form>
        <div class="table-responsive mt-4"><table class="table table-bordered table-striped align-middle"><thead><tr><th>Code</th><th>Route</th><th>Academic Year</th><th>Default Monthly Fee</th><th>Action</th></tr></thead><tbody>
            @forelse($routes as $route)
                @forelse($route->rates as $rate)
                    <tr><td>{{ $route->code }}</td><td>{{ $route->name }}</td><td>{{ $yearNames[$rate->academic_year_id] ?? $rate->academic_year_id }}</td><td>₹{{ number_format($rate->monthly_amount,2) }}</td><td><button type="button" class="btn btn-sm btn-outline-primary" data-id="{{ $route->id }}" data-code="{{ $route->code }}" data-name="{{ $route->name }}" data-year="{{ $rate->academic_year_id }}" data-amount="{{ $rate->monthly_amount }}" onclick="editRoute(this)">Edit</button></td></tr>
                @empty<tr><td>{{ $route->code }}</td><td>{{ $route->name }}</td><td colspan="3">No rate configured</td></tr>@endforelse
            @empty<tr><td colspan="5" class="text-center">No routes</td></tr>@endforelse
        </tbody></table></div>
    </div></div>

    <div class="card mb-4"><div class="card-header"><h5 class="mb-0" id="transport-stops">Route Stops &amp; Monthly Fees</h5></div><div class="card-body">
        <form method="POST" action="{{ route('transport.stops.save') }}" id="stop-form" class="row g-3">@csrf
            <input type="hidden" name="stop_id" value=""><input type="hidden" name="status" value="1">
            <div class="col-md-3"><label class="form-label">Transport Route</label><select class="form-select" name="transport_route_id" required><option value="">Select Route</option>@foreach($routes as $route)<option value="{{ $route->id }}">{{ $route->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Stop Name</label><input class="form-control" name="name" required></div>
            <div class="col-md-1"><label class="form-label">Order</label><input class="form-control" name="sort_order" type="number" min="0" value="1" required></div>
            <div class="col-md-2"><label class="form-label">Academic Year</label><select class="form-select" name="academic_year_id" required><option value="">Select Year</option>@foreach($years as $year)<option value="{{ $year->id }}">{{ $year->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Monthly Fee</label><input class="form-control" name="monthly_amount" type="number" min="0" step="0.01" required></div>
            <div class="col-md-2 d-flex align-items-end gap-2"><button id="stop-submit" class="btn btn-primary">Save Stop</button><button type="button" class="btn btn-secondary" onclick="resetStop()">Reset</button></div>
        </form>
        <div class="table-responsive mt-4"><table class="table table-bordered table-striped align-middle"><thead><tr><th>Route</th><th>Stop</th><th>Order</th><th>Academic Year</th><th>Monthly Fee</th><th>Action</th></tr></thead><tbody>
            @forelse($routes as $route)
                @foreach($route->stops as $stop)
                    @forelse($stop->rates as $rate)
                        <tr><td>{{ $route->name }}</td><td>{{ $stop->name }}</td><td>{{ $stop->sort_order }}</td><td>{{ $yearNames[$rate->academic_year_id] ?? $rate->academic_year_id }}</td><td>₹{{ number_format($rate->monthly_amount,2) }}</td><td><button type="button" class="btn btn-sm btn-outline-primary" data-id="{{ $stop->id }}" data-route="{{ $route->id }}" data-name="{{ $stop->name }}" data-order="{{ $stop->sort_order }}" data-year="{{ $rate->academic_year_id }}" data-amount="{{ $rate->monthly_amount }}" onclick="editStop(this)">Edit</button></td></tr>
                    @empty<tr><td>{{ $route->name }}</td><td>{{ $stop->name }}</td><td>{{ $stop->sort_order }}</td><td colspan="3">No rate configured</td></tr>@endforelse
                @endforeach
            @empty<tr><td colspan="6" class="text-center">No routes</td></tr>@endforelse
        </tbody></table></div>
    </div></div>

    <div class="card mb-4"><div class="card-header"><h5 class="mb-0" id="transport-assignments">Student Transport Assignment</h5></div><div class="card-body">
        <form method="POST" action="{{ route('transport.assign') }}" id="assignment-form" class="row g-3">@csrf
            <input type="hidden" name="assignment_id" value="">
            <div class="col-md-2"><label class="form-label">Academic Session *</label><select class="form-select" id="transport_year" required><option value="">Select Session</option>@foreach($years as $year)<option value="{{ $year->id }}">{{ $year->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Class *</label><select class="form-select" id="transport_class" required disabled><option value="">Select Class</option></select></div>
            <div class="col-md-2"><label class="form-label">Section *</label><select class="form-select" id="transport_section" required disabled><option value="">Select Section</option></select></div>
            <div class="col-md-3"><label class="form-label">Student *</label><select class="form-select" name="student_enrollment_id" id="student_enrollment_id" required disabled><option value="">Select Student</option></select></div>
            <div class="col-md-2"><label class="form-label">Route</label><select class="form-select" name="transport_route_id" id="assign_route" required><option value="">Select Route</option>@foreach($routes as $route)@if($route->status)<option value="{{ $route->id }}">{{ $route->name }}</option>@endif @endforeach</select></div>
            <div class="col-md-2"><label class="form-label">Stop</label><select class="form-select" name="transport_route_stop_id" id="assign_stop" required><option value="">Select Stop</option></select><small id="stop_fee_preview" class="text-success fw-bold"></small></div>
            <div class="col-md-2"><label class="form-label">Start Date</label><input class="form-control" name="starts_on" type="date" required></div>
            <div class="col-md-2"><label class="form-label">End Date</label><input class="form-control" name="ends_on" type="date"></div>
            <div class="col-md-3"><button id="assignment-submit" class="btn btn-success">Assign Transport</button><button type="button" class="btn btn-secondary" onclick="resetAssignment()">Reset</button></div>
        </form>
        <div class="alert alert-info mt-3 mb-0">Student must have an active Fee Assignment. Editing is blocked if transport dues have payment or other financial adjustments.</div>
    </div></div>

    <div class="card"><div class="card-header"><h5 class="mb-0">Student Transport Assignments</h5></div><div class="table-responsive"><table class="table table-bordered table-striped align-middle mb-0"><thead><tr><th>Student</th><th>Route</th><th>Stop</th><th>From</th><th>To</th><th>Monthly Fee</th><th>Action</th></tr></thead><tbody>
        @forelse($assignments as $assignment)
            <tr><td>{{ $assignment->student->student_name ?? $assignment->student_id }}</td><td>{{ $assignment->route->name ?? '-' }}</td><td>{{ $assignment->stop->name ?? 'Legacy route rate' }}</td><td>{{ $assignment->starts_on?->format('d-m-Y') ?? '-' }}</td><td>{{ $assignment->ends_on?->format('d-m-Y') ?? 'Academic year end' }}</td><td>₹{{ number_format($assignment->monthly_amount,2) }}</td><td><button type="button" class="btn btn-sm btn-outline-primary" data-id="{{ $assignment->id }}" data-enrollment="{{ $assignment->student_enrollment_id }}" data-route="{{ $assignment->transport_route_id }}" data-stop="{{ $assignment->transport_route_stop_id }}" data-start="{{ $assignment->starts_on?->format('Y-m-d') }}" data-end="{{ $assignment->ends_on?->format('Y-m-d') }}" onclick="editAssignment(this)">Edit</button></td></tr>
        @empty<tr><td colspan="7" class="text-center">No assignments</td></tr>@endforelse
    </tbody></table></div><div class="card-footer">{{ $assignments->links() }}</div></div>
</div>
<script>
const transportStops = {{ Illuminate\Support\Js::from($transportStops) }};
const enrollmentYears = {{ Illuminate\Support\Js::from($enrollmentYears) }};
const enrollmentOptions = {{ Illuminate\Support\Js::from($enrollmentOptions) }};
const yearFilter = document.getElementById('transport_year');
const classFilter = document.getElementById('transport_class');
const sectionFilter = document.getElementById('transport_section');
const studentFilter = document.getElementById('student_enrollment_id');
function clearFilter(select, label) {
    select.replaceChildren(new Option(label, ''));
    select.disabled = true;
}
function fillFilter(select, values, label) {
    clearFilter(select, label);
    [...values.entries()].sort((a,b) => String(a[1]).localeCompare(String(b[1]), undefined, {numeric:true}))
        .forEach(([id, name]) => select.add(new Option(name, id)));
    select.disabled = values.size === 0;
}
function filterClasses() {
    clearFilter(classFilter, 'Select Class');
    clearFilter(sectionFilter, 'Select Section');
    clearFilter(studentFilter, 'Select Student');
    const rows = enrollmentOptions.filter(e => String(e.academic_year_id) === yearFilter.value);
    fillFilter(classFilter, new Map(rows.filter(e => e.school_class_id != null)
        .map(e => [String(e.school_class_id), e.class_name])), 'Select Class');
    refreshStops();
}
function filterSections() {
    clearFilter(sectionFilter, 'Select Section');
    clearFilter(studentFilter, 'Select Student');
    const rows = enrollmentOptions.filter(e => String(e.academic_year_id) === yearFilter.value &&
        String(e.school_class_id) === classFilter.value);
    fillFilter(sectionFilter, new Map(rows.filter(e => e.section_id != null)
        .map(e => [String(e.section_id), e.section_name])), 'Select Section');
    refreshStops();
}
function filterStudents() {
    clearFilter(studentFilter, 'Select Student');
    const rows = enrollmentOptions.filter(e => String(e.academic_year_id) === yearFilter.value &&
        String(e.school_class_id) === classFilter.value && String(e.section_id) === sectionFilter.value);
    rows.sort((a,b) => String(a.student_name).localeCompare(String(b.student_name)));
    rows.forEach(e => studentFilter.add(new Option(e.student_name + ' (' + e.admission_no + ')', e.id)));
    studentFilter.disabled = rows.length === 0;
    refreshStops();
}
function selectEnrollment(enrollmentId) {
    const e = enrollmentOptions.find(row => String(row.id) === String(enrollmentId));
    if (!e) return false;
    yearFilter.value = String(e.academic_year_id);
    filterClasses();
    classFilter.value = String(e.school_class_id);
    filterSections();
    sectionFilter.value = String(e.section_id);
    filterStudents();
    studentFilter.value = String(e.id);
    refreshStops();
    return true;
}
function refreshStops(selectedId = '') {
    const route = document.getElementById('assign_route');
    const stop = document.getElementById('assign_stop');
    const enrollment = document.getElementById('student_enrollment_id');
    const preview = document.getElementById('stop_fee_preview');
    stop.innerHTML = '<option value="">Select Stop</option>';
    preview.textContent = '';
    const year = Number(enrollmentYears[enrollment.value]);
    (transportStops[route.value] || []).forEach(function(s) {
        const rate = s.rates.find(r => Number(r.academic_year_id) === year);
        if (!rate) return;
        const option = new Option(s.name + ' — ₹' + Number(rate.monthly_amount).toFixed(2) + '/month', s.id);
        option.dataset.fee = rate.monthly_amount;
        stop.add(option);
    });
    if (selectedId) stop.value = String(selectedId);
    const selected = stop.selectedOptions[0];
    if (selected && selected.dataset.fee !== undefined) preview.textContent = 'Monthly: ₹' + Number(selected.dataset.fee).toFixed(2);
}
document.addEventListener('DOMContentLoaded', function() {
    yearFilter.addEventListener('change', filterClasses);
    classFilter.addEventListener('change', filterSections);
    sectionFilter.addEventListener('change', filterStudents);
    document.getElementById('assign_route').addEventListener('change', () => refreshStops());
    document.getElementById('student_enrollment_id').addEventListener('change', () => refreshStops());
    document.getElementById('assign_stop').addEventListener('change', function() {
        const selected = this.selectedOptions[0];
        document.getElementById('stop_fee_preview').textContent = selected && selected.dataset.fee !== undefined ? 'Monthly: ₹' + Number(selected.dataset.fee).toFixed(2) : '';
    });
    refreshStops();
});
function editRoute(b) {
    const f = document.getElementById('route-form');
    for (const [field, value] of Object.entries({route_id:b.dataset.id,code:b.dataset.code,name:b.dataset.name,academic_year_id:b.dataset.year,monthly_amount:b.dataset.amount})) f.elements[field].value=value;
    document.getElementById('route-submit').textContent='Update Route';f.scrollIntoView({behavior:'smooth'});
}
function resetRoute() {const f=document.getElementById('route-form');f.reset();f.elements.route_id.value='';document.getElementById('route-submit').textContent='Save Route';}
function editStop(b) {
    const f=document.getElementById('stop-form');
    for (const [field,value] of Object.entries({stop_id:b.dataset.id,transport_route_id:b.dataset.route,name:b.dataset.name,sort_order:b.dataset.order,academic_year_id:b.dataset.year,monthly_amount:b.dataset.amount})) f.elements[field].value=value;
    document.getElementById('stop-submit').textContent='Update Stop';f.scrollIntoView({behavior:'smooth'});
}
function resetStop() {const f=document.getElementById('stop-form');f.reset();f.elements.stop_id.value='';document.getElementById('stop-submit').textContent='Save Stop';}
function editAssignment(b) {
    const f=document.getElementById('assignment-form');
    f.elements.assignment_id.value=b.dataset.id;
    if (!selectEnrollment(b.dataset.enrollment)) {
        alert('This enrollment is not in the current student list. Please check its academic session or enrollment status.');
        return;
    }
    f.elements.transport_route_id.value=b.dataset.route;
    refreshStops(b.dataset.stop);
    f.elements.starts_on.value=b.dataset.start;
    f.elements.ends_on.value=b.dataset.end;
    document.getElementById('assignment-submit').textContent='Update Transport';
    f.scrollIntoView({behavior:'smooth'});
}
function resetAssignment() {const f=document.getElementById('assignment-form');f.reset();f.elements.assignment_id.value='';filterClasses();refreshStops();document.getElementById('assignment-submit').textContent='Assign Transport';}
</script>
@endsection
