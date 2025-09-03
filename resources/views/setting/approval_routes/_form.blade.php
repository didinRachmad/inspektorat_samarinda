<div class="row">
    {{-- Module Select --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.select label="Module" name="module" :options="$menus->pluck('route', 'route')->toArray()" :value="$approval_route->module ?? ''" class="select2-module"
                required />
        </div>
    </div>

    {{-- Role Select --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.select label="Role" name="role_id" :options="$roles->pluck('name', 'id')->toArray()" :value="$approval_route->role_id ?? ''" class="select2-role"
                required />
        </div>
    </div>

    {{-- Assigned User Select (Opsional) --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="form-label" for="assigned_user_id" class="form-label">Assigned User (Opsional)</label>
            <select name="assigned_user_id" id="assigned_user_id"
                class="form-select form-select-sm select2-user @error('assigned_user_id') is-invalid @enderror">
                <option></option>
                @if (isset($approval_route) && $approval_route->assigned_user)
                    <option value="{{ $approval_route->assigned_user_id }}" selected>
                        {{ $approval_route->assigned_user->name }}
                    </option>
                @endif
            </select>
            @error('assigned_user_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Sequence Input --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Urutan Approval (Sequence)" name="sequence" type="number" :value="$approval_route->sequence ?? ''"
                min="1" required />
        </div>
    </div>
</div>
