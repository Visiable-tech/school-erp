@extends('layouts.admin')

@section('title', 'Edit Number Generation Scheme')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">
                Edit Number Generation Scheme
            </h5>
        </div>

        <div class="card-body">

            <div class="alert alert-light border">
                <strong>Scheme For:</strong>
                {{ $scheme->scheme_name }}
                <span class="ms-3">
                    <strong>Last Generated:</strong>
                    {{ $scheme->last_generated_number ?: 'None' }}
                </span>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('admission-number-schemes.update', $scheme->id) }}">

                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">
                            Auto Generation
                        </label>

                        <select name="auto_generate"
                                class="form-select" required>
                            <option value="1"
                                @selected((string) old('auto_generate', (int) $scheme->auto_generate) === '1')>
                                YES
                            </option>
                            <option value="0"
                                @selected((string) old('auto_generate', (int) $scheme->auto_generate) === '0')>
                                NO
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Include Academic Year
                        </label>

                        <select name="include_academic_year"
                                class="form-select" required>
                            <option value="1"
                                @selected((string) old('include_academic_year', (int) $scheme->include_academic_year) === '1')>
                                YES
                            </option>
                            <option value="0"
                                @selected((string) old('include_academic_year', (int) $scheme->include_academic_year) === '0')>
                                NO
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Include Class
                        </label>

                        <select name="include_class"
                                class="form-select" required>
                            <option value="1"
                                @selected((string) old('include_class', (int) $scheme->include_class) === '1')>
                                YES
                            </option>
                            <option value="0"
                                @selected((string) old('include_class', (int) $scheme->include_class) === '0')>
                                NO
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Separator
                        </label>

                        <select name="separator" class="form-select">
                            @foreach(['/' => 'Slash (/)', '-' => 'Hyphen (-)', '_' => 'Underscore (_)', '' => 'None'] as $value => $label)
                                <option value="{{ $value }}"
                                    @selected(old('separator', $scheme->separator) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Fixed String
                        </label>

                        <input type="text"
                               name="fixed_string"
                               class="form-control"
                               maxlength="50"
                               value="{{ old('fixed_string', $scheme->fixed_string) }}"
                               placeholder="e.g. ENQ or REG">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Number Length
                        </label>

                        <input type="number"
                               name="number_length"
                               class="form-control"
                               min="1"
                               max="12"
                               required
                               value="{{ old('number_length', $scheme->number_length) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Starting Number
                        </label>

                        <input type="number"
                               name="starting_number"
                               class="form-control"
                               min="1"
                               required
                               value="{{ old('starting_number', $scheme->starting_number) }}"
                               @if($scheme->last_generated_number > 0) readonly @endif>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Allow Manual Entry
                        </label>

                        <select name="allow_manual_entry"
                                class="form-select" required>
                            <option value="1"
                                @selected((string) old('allow_manual_entry', (int) $scheme->allow_manual_entry) === '1')>
                                YES
                            </option>
                            <option value="0"
                                @selected((string) old('allow_manual_entry', (int) $scheme->allow_manual_entry) === '0')>
                                NO
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Status
                        </label>

                        <select name="status" class="form-select" required>
                            <option value="1"
                                @selected((string) old('status', (int) $scheme->status) === '1')>
                                Active
                            </option>
                            <option value="0"
                                @selected((string) old('status', (int) $scheme->status) === '0')>
                                Inactive
                            </option>
                        </select>
                    </div>

                </div>

                <div class="d-flex gap-2 mt-4">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Update Scheme
                    </button>

                    <a href="{{ route('admission-number-schemes.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                </div>

            </form>

        </div>
    </div>

</div>
@endsection