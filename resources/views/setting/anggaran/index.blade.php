@extends('layouts.dashboard')

@php
    $page = 'setting/anggaran';
    $action = 'index';
@endphp

@section('dashboard-content')
    <x-breadcrumbs>
    </x-breadcrumbs>
    <div class="row">
        <div class="col-12 d-flex">
            <div class="card rounded-4 w-100 m-0">
                <form action="{{ route('setting_anggaran.update', $anggaran->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="anggaran" class="form-label">Setting Anggaran</label>
                                <input type="text" name="anggaran" id="anggaran" class="form-control numeric"
                                    value="{{ old('anggaran', $anggaran->anggaran ?? 0) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end align-items-center">
                        <button type="submit" class="btn btn-sm btn-submit rounded-4 btn-primary">Update <i
                                class="bi bi-save-fill"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
