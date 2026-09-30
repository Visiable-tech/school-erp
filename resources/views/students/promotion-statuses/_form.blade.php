<div class="row">

    <div class="col-md-4 mb-3">
        <label class="form-label">
            Status Name <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $promotionStatus->name ?? '') }}"
            placeholder="Example: Promoted"
            required
        >

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    <div class="col-md-2 mb-3">
        <label class="form-label">Code</label>

        <input
            type="text"
            name="code"
            class="form-control"
            value="{{ old('code', $promotionStatus->code ?? '') }}"
            placeholder="PROM"
        >
    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Type <span class="text-danger">*</span>
        </label>

        <select
            name="type"
            class="form-select"
            required
        >
            <option value="">Select Type</option>

            <option
                value="promoted"
                {{ old('type', $promotionStatus->type ?? '') === 'promoted' ? 'selected' : '' }}
            >
                Promoted
            </option>

            <option
                value="repeated"
                {{ old('type', $promotionStatus->type ?? '') === 'repeated' ? 'selected' : '' }}
            >
                Repeated
            </option>

            <option
                value="detained"
                {{ old('type', $promotionStatus->type ?? '') === 'detained' ? 'selected' : '' }}
            >
                Detained
            </option>

            <option
                value="conditional"
                {{ old('type', $promotionStatus->type ?? '') === 'conditional' ? 'selected' : '' }}
            >
                Conditional
            </option>

            <option
                value="other"
                {{ old('type', $promotionStatus->type ?? '') === 'other' ? 'selected' : '' }}
            >
                Other
            </option>
        </select>

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">Sort Order</label>

        <input
            type="number"
            name="sort_order"
            min="0"
            class="form-control"
            value="{{ old('sort_order', $promotionStatus->sort_order ?? 0) }}"
        >

    </div>


    <div class="col-md-12 mb-3">

        <label class="form-label">Description</label>

        <textarea
            name="description"
            class="form-control"
            rows="3"
            placeholder="Optional description"
        >{{ old('description', $promotionStatus->description ?? '') }}</textarea>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">Status</label>

        <select
            name="status"
            class="form-select"
            required
        >
            <option
                value="1"
                {{ old('status', $promotionStatus->status ?? 1) == 1 ? 'selected' : '' }}
            >
                Active
            </option>

            <option
                value="0"
                {{ old('status', $promotionStatus->status ?? 1) == 0 ? 'selected' : '' }}
            >
                Inactive
            </option>
        </select>

    </div>

</div>