@extends('layouts.dashboard')

@php
    $page = 'pelaksanaan/lha';
    $action = 'create';
@endphp

@section('dashboard-content')
    <x-breadcrumbs></x-breadcrumbs>
    <div class="card rounded-4 w-100 m-0">
        <form action="{{ route('lha.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
                <h5 class="card-title">Tambah LHA</h5>
            </div>
            <div class="card-body">
                @include('pelaksanaan.lha._form')
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ route('lha.index') }}" class="btn btn-sm rounded-4 btn-secondary">Batal <i
                        class="bi bi-x-square-fill"></i></a>
                <button type="submit" class="btn btn-sm btn-submit rounded-4 btn-primary">Simpan <i
                        class="bi bi-save-fill"></i></button>
            </div>
        </form>
    </div>
@endsection
