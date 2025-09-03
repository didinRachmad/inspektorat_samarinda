@extends('layouts.dashboard')

@php
    $page = 'transaksi/sales_orders';
    $action = 'index';
@endphp

@section('dashboard-content')
    <x-breadcrumbs>
        @if (Auth::user()->hasMenuPermission($activeMenu->id, 'create'))
            <a class="btn btn-sm rounded-4 btn-primary shadow-sm" href="{{ route('transaksi_sales_orders.create') }}">
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
                                    <th>NO SO</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>Pembayaran</th>
                                    <th>Total QTY</th>
                                    <th>Total Diskon</th>
                                    <th>Grand Total</th>
                                    <th>Approval Level</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
