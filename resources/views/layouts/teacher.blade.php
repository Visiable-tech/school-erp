<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title','Teacher Portal') - School ERP</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>body{background:#f1f3f6}.teacher-sidebar{background:#343b4e;min-height:100vh;width:250px}.teacher-sidebar a{color:#dce0e8;text-decoration:none;display:block;padding:10px 16px}.teacher-sidebar a:hover,.teacher-sidebar a.active{background:#242b3e;color:white}.teacher-top{background:#e71e24;color:white}.teacher-main{min-width:0;flex:1}.teacher-sidebar .group{font-size:.72rem;color:#9da8ba;text-transform:uppercase;padding:18px 16px 5px}</style></head>
<body><div class="d-flex"><aside class="teacher-sidebar flex-shrink-0"><div class="p-3 text-white fw-bold border-bottom border-secondary">Teacher Portal</div>
<a href="{{ route('teacher.dashboard') }}" class="{{ request()->routeIs('teacher.dashboard')?'active':'' }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
<a href="{{ route('teacher.profile') }}" class="{{ request()->routeIs('teacher.profile')?'active':'' }}"><i class="bi bi-person me-2"></i>My Profile</a>
<div class="group">Student Management</div>
<a href="{{ route('teacher.students') }}" class="{{ request()->routeIs('teacher.students')?'active':'' }}"><i class="bi bi-people me-2"></i>Student List</a>
<div class="group">Upcoming modules</div>
@foreach(['Documents / Vault','Event Calendar','Announcements','Classwork / Assignments','Examination','Communication','Student Achievements','Library','Payroll / Attendance','Self / Team Assessment','Image Gallery','Inventory','Tickets / Tasks','Instructions','E-Learning'] as $item)
<div class="px-3 py-2 text-secondary small">{{ $item }}</div>@endforeach
</aside><main class="teacher-main"><header class="teacher-top px-4 py-3 d-flex justify-content-between"><strong>{{ auth()->user()->school?->name ?? 'School ERP' }}</strong><span>{{ auth()->user()->name }}</span></header>
<div class="container-fluid p-4">@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif @yield('content')</div></main></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html>
