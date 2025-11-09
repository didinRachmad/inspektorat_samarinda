@extends('layouts.dashboard')

@php
    $page = 'pelaksanaan/tindak_lanjut_temuan';
    $action = 'create';
@endphp

@section('dashboard-content')
    <x-breadcrumbs></x-breadcrumbs>
    <form action="{{ route('tindak_lanjut_temuan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card rounded-4 w-100 mb-3">
            <div class="card-header">
                <h5 class="card-title">Tambah LHP</h5>
            </div>
            <div class="card-body">
                @include('pelaksanaan.tindak_lanjut_temuan._form')
            </div>
        </div>
        <div class="card rounded-4 w-100 mb-3">
            <div class="card-header">
                <h5 class="card-title">Tambah Temuan</h5>
            </div>
            <div class="card-body">
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ route('tindak_lanjut_temuan.index') }}" class="btn btn-sm rounded-4 btn-secondary">Batal <i
                        class="bi bi-x-square-fill"></i></a>
                <button type="submit" class="btn btn-sm btn-submit rounded-4 btn-primary">Simpan <i
                        class="bi bi-save-fill"></i></button>
            </div>
        </div>
    </form>
@endsection
