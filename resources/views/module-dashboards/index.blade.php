@extends('layouts.admin')
@section('title', $module . ' Dashboard')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>{{ $module }} Dashboard</h3>
        <span class="text-muted">School overview</span>
    </div>
    <div class="row g-3 mb-4">
        @foreach($stats as $stat)
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted mb-2">{{ $stat['title'] }}</div>
                    <h2 class="mb-0">{{ $stat['value'] === null ? '—' : number_format($stat['value']) }}</h2>
                    @if($stat['value'] === null)
                    <small class="text-muted">Table or school-scoped data unavailable</small>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="card">
        <div class="card-header">Quick Access</div>
        <div class="card-body d-flex flex-wrap gap-2">
            @foreach($links as $link)
                @if(\Illuminate\Support\Facades\Route::has($link['route']))
                <a href="{{ route($link['route']) }}" class="btn btn-outline-primary">{{ $link['label'] }}</a>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
