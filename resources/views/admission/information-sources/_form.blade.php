<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">Source Name <span class="text-danger">*</span></label>
        <input type="text" name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $source->name ?? '') }}"
               placeholder="e.g. Google Search" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Code</label>
        <input type="text" name="code"
               class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $source->code ?? '') }}"
               placeholder="e.g. GOOGLE">
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" min="0"
               class="form-control @error('sort_order') is-invalid @enderror"
               value="{{ old('sort_order', $source->sort_order ?? 0) }}">
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
            <option value="1"
                @selected((string) old('status', isset($source) ? (int) $source->status : 1) === '1')>
                Active
            </option>
            <option value="0"
                @selected((string) old('status', isset($source) ? (int) $source->status : 1) === '0')>
                Inactive
            </option>
        </select>
    </div>

    <div class="col-md-9">
        <label class="form-label">Description</label>
        <textarea name="description" rows="3"
                  class="form-control @error('description') is-invalid @enderror"
                  placeholder="Optional description">{{ old('description', $source->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>