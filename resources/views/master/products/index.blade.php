@extends('layouts.dashboard')

@php
    $page = 'master/products';
    $action = 'index';
@endphp

@section('dashboard-content')
    <x-breadcrumbs>
        @if (Auth::user()->hasMenuPermission($activeMenu->id, 'create'))
            <a class="btn btn-sm rounded-4 btn-primary shadow-sm" href="{{ route('master_products.create') }}">
                <i class="bi bi-plus-circle-fill"></i> Tambah Data
            </a>
        @endif
    </x-breadcrumbs>
    <div class="row">
        <div class="col-12 d-flex">
            <div class="card rounded-4 w-100 m-0">
                <div class="card-body">
                    <table id="datatables"
                        class="table table-sm align-middle table-striped table-bordered">
                        <thead class="bg-gd">
                            <tr>
                                <th>No</th>
                                <th>ID</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Kemasan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
