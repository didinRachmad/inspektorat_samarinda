@extends('layouts.dashboard')

@php
    $page = 'perencanaan/non_pkpt';
    $action = 'index';
@endphp

@section('dashboard-content')
    <x-breadcrumbs>
        @if (Auth::user()->hasMenuPermission($activeMenu->id, 'create'))
            <a class="btn btn-sm rounded-4 btn-primary shadow-sm" href="{{ route('non_pkpt.create') }}">
                <i class="bi bi-plus-circle-fill"></i> Tambah Data
            </a>
        @endif
    </x-breadcrumbs>
    <div class="row">
        <div class="col-12 d-flex">
            <div class="card rounded-4 w-100 m-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatables" class="table table-sm align-middle table-striped table-bordered">
                            <thead class="bg-gd">
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Tahun</th>
                                    <th>Bulan</th>
                                    <th>No PKPT</th>
                                    <th>Auditi</th>
                                    <th>Ruang Lingkup</th>
                                    <th>Sasaran</th>
                                    <th>Jenis Pengawasan</th>
                                    <th>RMP</th>
                                    <th>RSP</th>
                                    <th>RPL</th>
                                    <th>HP</th>
                                    <th>PJ</th>
                                    <th>WPJ</th>
                                    <th>PT</th>
                                    <th>KT</th>
                                    <th>AT</th>
                                    <th>Anggaran Total</th>
                                    <th>Irbanwil</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
