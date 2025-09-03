@extends('layouts.dashboard')

@php
    $page = 'transaksi/delivery_orders';
    $action = 'edit';
@endphp

@section('dashboard-content')
    <x-breadcrumbs></x-breadcrumbs>
    <form action="{{ route('transaksi_delivery_orders.update', $deliveryOrder->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card rounded-4 w-100 m-0">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Data</h5>
            </div>
            <div class="card-body">
                @include('transaksi.delivery_orders._form')
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ route('transaksi_delivery_orders.index') }}" class="btn btn-sm rounded-4 btn-secondary">Batal <i
                        class="bi bi-x-square-fill"></i></a>
                <button type="submit" class="btn btn-sm btn-submit rounded-4 btn-primary">Simpan <i
                        class="bi bi-save-fill"></i></button>
            </div>
        </div>
    </form>
@endsection
