@extends('layouts.dashboard')

@php
    $page = 'dashboard';
    $action = 'index';
@endphp

@section('dashboard-content')
    <div class="row">
        <div class="col-12 d-flex">
            <div class="card rounded-4 w-100 m-0">
                <div class="card-body">
                    <div class="p-6 fw-bold">
                        {{ __('Selamat datang') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
    <!--end breadcrumb-->
    {{-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-4">
            <div class="p-6 text-gray-900 fw-bold">
                {{ __('Selamat datang') }}
            </div>
        </div>
    </div> --}}
@endsection
