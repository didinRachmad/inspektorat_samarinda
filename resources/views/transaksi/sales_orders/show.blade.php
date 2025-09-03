@extends('layouts.dashboard')

@php
    $page = 'transaksi/sales_orders';
    $action = 'show';
@endphp

@section('dashboard-content')
    <x-breadcrumbs></x-breadcrumbs>

    <div class="card rounded-4 w-100 m-0">
        <div class="card-header">
            <h5 class="card-title mb-0">Detail Sales Order</h5>
        </div>
        <div class="card-body">
            {{-- Informasi Sales Order --}}
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Nomor SO:</div>
                <div class="col-sm-9">{{ $salesOrder->no_so }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Tanggal:</div>
                <div class="col-sm-9">
                    {{ optional($salesOrder->tanggal)->format('d-m-Y') ?? '-' }}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Metode Pembayaran:</div>
                <div class="col-sm-9">{{ $salesOrder->metode_pembayaran }}</div>
            </div>

            <hr>

            {{-- Informasi Pelanggan --}}
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Kode Customer:</div>
                <div class="col-sm-9">{{ $salesOrder->customer->kode_customer ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Nama Toko:</div>
                <div class="col-sm-9">{{ $salesOrder->customer->nama_toko ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Pemilik:</div>
                <div class="col-sm-9">{{ $salesOrder->customer->pemilik ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Alamat:</div>
                <div class="col-sm-9">{{ $salesOrder->customer->alamat ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Pasar:</div>
                <div class="col-sm-9"><b>{{ $salesOrder->customer->id_pasar ?? '-' }}</b> -
                    {{ $salesOrder->customer->nama_pasar ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Tipe Outlet:</div>
                <div class="col-sm-9 text-capitalize">{{ $salesOrder->customer->tipe_outlet ?? '-' }}</div>
            </div>

            <hr>

            {{-- Status & Keterangan --}}
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Status:</div>
                <div class="col-sm-9">{{ $salesOrder->status }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Approval Level:</div>
                <div class="col-sm-9">{{ $salesOrder->approval_level }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Keterangan:</div>
                <div class="col-sm-9">{{ $salesOrder->keterangan ?? '-' }}</div>
            </div>

            {{-- Tabel Detail Produk --}}
            <h6 class="fw-bold mt-4 mb-3">Detail Produk</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                                <th>ID</th>
                            <th>Produk</th>
                            <th>Kemasan</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Diskon</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($salesOrder->details as $i => $detail)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>({{ $detail->product->kode_produk }})
                                    - {{ $detail->product->nama_produk }}</td>
                                <td>{{ $detail->product->kemasan }}</td>
                                <td class="text-end">{{ $detail->qty }}</td>
                                <td class="text-end">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($detail->diskon, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">Total</td>
                            <td class="text-end">{{ $salesOrder->total_qty }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($salesOrder->total_diskon, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($salesOrder->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <a href="{{ route('transaksi_sales_orders.index') }}" class="btn btn-sm btn-secondary rounded-4">
                Kembali <i class="bi bi-arrow-left-circle"></i>
            </a>

            @if ($approvalRoute && $salesOrder->approval_level == $approvalRoute->sequence - 1)
                @if (!($salesOrder->approval_level > 0))
                    <form action="{{ route('transaksi_sales_orders.approve', $salesOrder->id) }}" method="POST"
                        class="d-inline form-approval">
                        @csrf
                        <button type="submit" class="btn btn-sm rounded-4 btn-success btn-approve">
                            <i class="bi bi-check2-square"></i> Ajukan
                        </button>
                    </form>
                @else
                    <div class="dropdown dropstart">
                        <button class="btn btn-sm rounded-4 btn-success dropdown-toggle h-100" type="button"
                            id="actionDropdown{{ $salesOrder->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                        </button>
                        <ul class="dropdown-menu dropdown-menu-start"
                            aria-labelledby="actionDropdown{{ $salesOrder->id }}">
                            <li>
                                <form action="{{ route('transaksi_sales_orders.approve', $salesOrder->id) }}"
                                    method="POST" class="d-inline form-approval">
                                    @csrf
                                    <button type="submit" class="dropdown-item btn-approve text-success">Approve</button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('transaksi_sales_orders.revise', $salesOrder->id) }}" method="POST"
                                    class="d-inline form-revisi">
                                    @csrf
                                    <button type="submit" class="dropdown-item btn-revisi text-warning">Revisi</button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('transaksi_sales_orders.reject', $salesOrder->id) }}" method="POST"
                                    class="d-inline form-reject">
                                    @csrf
                                    <button type="submit" class="dropdown-item btn-reject text-danger">Reject</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
