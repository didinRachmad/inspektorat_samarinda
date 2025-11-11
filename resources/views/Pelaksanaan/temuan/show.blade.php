@extends('layouts.dashboard')
@php
    $page = 'pelaksanaan/temuan';
    $action = 'show';
@endphp

@section('dashboard-content')
    <x-breadcrumbs></x-breadcrumbs>

    {{-- Informasi Temuan --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-info bg-gradient ">
            <h5 class="mb-0">Informasi Temuan</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Nomor Temuan:</strong></p>
                    <p class="mb-0">{{ $temuan->nomor_temuan }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>No PKPT:</strong></p>
                    <p class="mb-0">{{ $temuan->pkpt->no_pkpt ?? '-' }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Tanggal:</strong></p>
                    <p class="mb-0">{{ optional($temuan->tanggal_temuan)->format('d-m-Y') }}</p>
                </div>
                <div class="col-md-6">
                    @if ($temuan->file_temuan)
                        <p class="mb-1"><strong>File Temuan:</strong></p>
                        <a href="{{ asset('storage/' . $temuan->file_temuan) }}" target="_blank"
                            class="btn btn-sm btn-outline-info rounded-4">
                            <i class="bi bi-download"></i> Download
                        </a>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <p class="mb-1"><strong>Uraian:</strong></p>
                    <p class="mb-0">{!! nl2br(e($temuan->uraian_temuan)) !!}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <p class="mb-1"><strong>Rekomendasi:</strong></p>
                    <p class="mb-0">{!! nl2br(e($temuan->rekomendasi)) !!}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar KKA --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Daftar KKA</h6>
            <button class="btn btn-sm btn-primary rounded-4" id="btnAddKka">
                <i class="bi bi-plus-lg"></i> Tambah KKA
            </button>
        </div>
        <div class="card-body p-3">
            <div id="kkaList">
                @include('pelaksanaan.temuan._kka_list', ['kkas' => $temuan->kkas])
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <a href="{{ route('temuan.index') }}" class="btn btn-sm btn-secondary rounded-4">
                <i class="bi bi-x-square-fill"></i> Kembali
            </a>
            {{-- <button type="submit" class="btn btn-sm btn-primary rounded-4 btn-submit">
                <i class="bi bi-save-fill"></i> Update
            </button> --}}
        </div>
    </div>

    {{-- Modal Tambah KKA --}}
    <div class="modal fade" id="modalKka" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4 shadow-sm">
                <div class="modal-header">
                    <h5 class="modal-title mb-0">Tambah KKA</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formKka" action="{{ route('kka.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="temuan_id" value="{{ $temuan->id }}">

                        <div class="mb-3">
                            <label class="form-label">Judul KKA</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Uraian Prosedur</label>
                            <input type="text" name="uraian_prosedur" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hasil</label>
                            <textarea name="hasil" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">File KKA (opsional)</label>
                            <input type="file" name="file_kka" class="form-control" accept=".pdf,.doc,.docx">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle-fill"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary rounded-4" id="btnSaveKka">
                        <i class="bi bi-save-fill"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
