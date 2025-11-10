@extends('layouts.dashboard')

@php
    $page = 'regulasi';
    $action = 'index';
@endphp

@section('dashboard-content')
    <x-breadcrumbs>
        @if (Auth::user()->hasMenuPermission($activeMenu->id, 'create'))
            <button class="btn btn-sm rounded-4 btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#regulasiModal">
                <i class="bi bi-plus-circle-fill"></i> Tambah Regulasi
            </button>
        @endif
    </x-breadcrumbs>

    <div class="row">
        <div class="col-12 d-flex">
            <div class="card rounded-4 w-100 m-0 shadow-sm">
                <div class="card-body">
                    <table id="datatables" class="table table-sm align-middle table-striped table-bordered">
                        <thead class="bg-gd">
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah / Edit Regulasi -->
    <div class="modal fade" id="regulasiModal" tabindex="-1" aria-labelledby="regulasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header bg-gd text-white rounded-top-4">
                    <h5 class="modal-title" id="regulasiModalLabel">Tambah Regulasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="regulasiForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="regulasi_id" id="regulasi_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Regulasi</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">File Regulasi</label>
                            <input type="file" class="form-control" id="file" name="file">
                            <small id="currentFile" class="form-text text-muted"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-4" id="btnSave">
                            <i class="bi bi-save-fill"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
