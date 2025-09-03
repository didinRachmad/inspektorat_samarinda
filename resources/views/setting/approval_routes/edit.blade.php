@extends('layouts.dashboard')

@php
    $page = 'setting/approval_routes';
    $action = 'edit';
@endphp

@section('dashboard-content')
    <x-breadcrumbs></x-breadcrumbs>
    <div class="card rounded-4 w-100 m-0">

        <form action="{{ route('approval_routes.update', $approval_route->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Konfigurasi Approval</h5>
            </div>
            <div class="card-body">
                @include('setting.approval_routes._form')
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ route('approval_routes.index') }}" class="btn btn-sm rounded-4 btn-secondary">Batal <i
                        class="bi bi-x-square-fill"></i></a>
                <button type="submit" class="btn btn-sm btn-submit rounded-4 btn-primary">Simpan <i
                        class="bi bi-save-fill"></i></button>
            </div>
        </form>
    </div>
@endsection
