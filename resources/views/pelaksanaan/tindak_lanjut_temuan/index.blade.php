@extends('layouts.dashboard')

@php
    $page = 'pelaksanaan/tindak_lanjut_temuan';
    $action = 'index';
@endphp

@section('dashboard-content')
    <x-breadcrumbs>
        {{-- @if (Auth::user()->hasMenuPermission($activeMenu->id, 'create'))
            <a class="btn btn-sm rounded-4 btn-primary shadow-sm" href="{{ route('tindak_lanjut_temuan.create') }}">
                <i class="bi bi-plus-circle-fill"></i> Tambah Data
            </a>
        @endif --}}
    </x-breadcrumbs>
    <div class="row">
        <div class="col-12 d-flex">
            <div class="card rounded-4 w-100 m-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatables" class="table table-sm align-middle table-striped table-bordered">
                            <thead></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
