<div class="mb-3">
    <label class="form-label">Wing Name</label>

    <input
        type="text"
        name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $wing->name ?? '') }}"
        placeholder="Example: Primary"
        required
    >

    @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Code</label>

    <input
        type="text"
        name="code"
        class="form-control @error('code') is-invalid @enderror"
        value="{{ old('code', $wing->code ?? '') }}"
        placeholder="Example: PRI"
    >

    @error('code')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Sort Order</label>

    <input
        type="number"
        name="sort_order"
        min="0"
        class="form-control"
        value="{{ old('sort_order', $wing->sort_order ?? 0) }}"
    >
</div>

<div class="form-check mb-4">
    <input
        type="checkbox"
        name="status"
        value="1"
        class="form-check-input"
        id="status"
        @checked(old('status', $wing->status ?? true))
    >

    <label class="form-check-label" for="status">
        Active
    </label>
</div>