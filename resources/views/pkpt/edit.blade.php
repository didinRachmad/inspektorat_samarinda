@extends('layouts.dashboard')

@php
    $page = 'pkpt';
    $action = 'edit';
@endphp

@section('dashboard-content')
    <x-breadcrumbs></x-breadcrumbs>
    <div class="card rounded-4 w-100 m-0">
        <form action="{{ route('pkpt.update', $pkpt->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="card-header">
                <h5 class="card-title">Edit PKPT</h5>
            </div>
            <div class="card-body">
                @include('pkpt._form', ['pkpt' => $pkpt])
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ route('pkpt.index') }}" class="btn btn-sm rounded-4 btn-secondary">Batal <i
                        class="bi bi-x-square-fill"></i></a>
                <button type="submit" class="btn btn-sm btn-submit rounded-4 btn-primary">Update <i
                        class="bi bi-save-fill"></i></button>
            </div>
        </form>
    </div>
@endsection
