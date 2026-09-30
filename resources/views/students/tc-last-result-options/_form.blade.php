<div class="row">

    <div class="col-md-6 mb-3">
        <label class="form-label">
            Last Result Option <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $tcLastResultOption->name ?? '') }}"
            placeholder="Example: Passed"
            required
        >

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Code</label>

        <input
            type="text"
            name="code"
            class="form-control"
            value="{{ old('code', $tcLastResultOption->code ?? '') }}"
            placeholder="Example: PASS"
        >
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Sort Order</label>

        <input
            type="number"
            name="sort_order"
            min="0"
            class="form-control"
            value="{{ old('sort_order', $tcLastResultOption->sort_order ?? 0) }}"
        >
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Description</label>

        <textarea
            name="description"
            class="form-control"
            rows="3"
        >{{ old('description', $tcLastResultOption->description ?? '') }}</textarea>
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Status</label>

        <select name="status" class="form-select" required>

            <option
                value="1"
                {{ old('status', $tcLastResultOption->status ?? 1) == 1 ? 'selected' : '' }}
            >
                Active
            </option>

            <option
                value="0"
                {{ old('status', $tcLastResultOption->status ?? 1) == 0 ? 'selected' : '' }}
            >
                Inactive
            </option>

        </select>
    </div>

</div>