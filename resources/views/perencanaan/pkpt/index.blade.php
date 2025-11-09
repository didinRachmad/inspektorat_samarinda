@extends('layouts.dashboard')

@php
    $page = 'perencanaan/pkpt';
    $action = 'index';
@endphp

@section('dashboard-content')
    <x-breadcrumbs>
        @if (Auth::user()->hasMenuPermission($activeMenu->id, 'create'))
            <a class="btn btn-sm rounded-4 btn-primary shadow-sm" href="{{ route('pkpt.create') }}">
                <i class="bi bi-plus-circle-fill"></i> Tambah Data
            </a>
        @endif
    </x-breadcrumbs>

    <div class="card rounded-4 w-100">
        <div class="card-body">
            <div class="row justify-content-center align-items-center mb-3">
                <div class="col-md-2">
                    <input type="text" id="bulan" class="form-control form-control-sm filterBulan"
                        placeholder="Pilih Bulan" autocomplete="off">
                    <input type="hidden" name="filterBulan" id="filterBulan">
                </div>
                <div class="col-md-2">
                    <input type="text" id="filterTahun" class="form-control form-control-sm filterTahun"
                        placeholder="Pilih Tahun" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <select id="filterMandatory" class="form-select form-select-sm select2"
                        placeholder="-- Semua Mandatory --">
                        <option value=""></option>
                        @foreach ($mandatories as $mandatori)
                            <option value="{{ $mandatori->id }}">{{ $mandatori->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select id="filterAuditi" class="form-select form-select-sm select2" placeholder="-- Semua Auditi --">
                        <option value=""></option>
                        @foreach ($auditis as $auditi)
                            <option value="{{ $auditi->id }}">{{ $auditi->nama_auditi }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select id="filterIrbanwil" class="form-select form-select-sm select2"
                        placeholder="-- Semua Irbanwil --">
                        <option value=""></option>
                        @foreach ($irbanwils as $irban)
                            <option value="{{ $irban->id }}">{{ $irban->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select id="filterJenisPengawasan" class="form-select form-select-sm select2"
                        placeholder="-- Semua Jenis Pengawasan --">
                        <option value=""></option>
                        @foreach ($jenisPengawasans as $parent)
                            @foreach ($parent->children as $child)
                                <option value="{{ $child->id }}">
                                    {{ $parent->nama }} : {{ $child->nama }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>

            <hr>
            <div class="table-responsive">
                <table id="datatables" class="table table-sm align-middle table-striped table-bordered">
                    <thead class="bg-gd">
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Tahun</th>
                            <th>Bulan</th>
                            <th>No PKPT</th>
                            <th>Mandatory</th>
                            <th>Auditi</th>
                            <th>Sasaran</th>
                            <th>Ruang Lingkup</th>
                            <th>Jenis Pengawasan</th>
                            <th>Sub Jenis Pengawasan</th>
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
@endsection
