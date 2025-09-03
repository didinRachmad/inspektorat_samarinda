@php
    $user = $user ?? null;
@endphp

<div class="row">
    {{-- Nama --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Nama" name="name" :value="$user->name ?? ''" required />
        </div>
    </div>
    {{-- Email --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input type="email" label="Email" name="email" :value="$user->email ?? ''" required />
        </div>
    </div>
    {{-- Password --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input type="password" label="Password" name="password" required />
        </div>
    </div>
    {{-- Konfirmasi Password --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input type="password" label="Konfirmasi Password" name="password_confirmation" required />
        </div>
    </div>
    {{-- Role --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.select label="Role" name="role_id" :options="$roles->pluck('name', 'id')->toArray()" :value="$user?->roles->first()?->id ?? ''" required />
        </div>
    </div>
</div>
