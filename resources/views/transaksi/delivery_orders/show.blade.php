@extends('layouts.dashboard')

@php
    $page = 'transaksi/delivery_orders';
    $action = 'show';
@endphp

@section('dashboard-content')
    <x-breadcrumbs></x-breadcrumbs>

    <div class="card rounded-4 w-100 m-0">
        <div class="card-header">
            <h5 class="card-title mb-0">Detail Delivery Order</h5>
        </div>
        <div class="card-body">
            {{-- Informasi DO --}}
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Nomor DO:</div>
                <div class="col-sm-9">{{ $deliveryOrder->no_do }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Tanggal:</div>
                <div class="col-sm-9">{{ optional($deliveryOrder->tanggal)->format('d-m-Y') ?? '-' }}</div>
            </div>

            <hr>

            {{-- Informasi Sales Order --}}
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Nomor SO:</div>
                <div class="col-sm-9">{{ $deliveryOrder->sales_order->no_so ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Tanggal SO:</div>
                <div class="col-sm-9">{{ optional($deliveryOrder->sales_order->tanggal)->format('d-m-Y') ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Metode Pembayaran:</div>
                <div class="col-sm-9">{{ $deliveryOrder->sales_order->metode_pembayaran ?? '-' }}</div>
            </div>

            <hr>

            {{-- Customer --}}
            @php $customer = $deliveryOrder->sales_order->customer ?? null; @endphp
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Kode Customer:</div>
                <div class="col-sm-9">{{ $customer->kode_customer ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Nama Toko:</div>
                <div class="col-sm-9">{{ $customer->nama_toko ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Pemilik:</div>
                <div class="col-sm-9">{{ $customer->pemilik ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Alamat:</div>
                <div class="col-sm-9">{{ $customer->alamat ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Pasar:</div>
                <div class="col-sm-9">{{ $customer->nama_pasar ?? '-' }}</div>
            </div>

            <hr>

            {{-- Pengiriman --}}
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Asal Pengiriman:</div>
                <div class="col-sm-9">{{ $deliveryOrder->origin_name ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Tujuan Pengiriman:</div>
                <div class="col-sm-9">{{ $deliveryOrder->destination_name ?? '-' }}</div>
            </div>

            <hr>

            {{-- Status --}}
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Status:</div>
                <div class="col-sm-9">{{ $deliveryOrder->status }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Approval Level:</div>
                <div class="col-sm-9">{{ $deliveryOrder->approval_level }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold">Keterangan:</div>
                <div class="col-sm-9">{{ $deliveryOrder->keterangan ?? '-' }}</div>
            </div>

            {{-- Detail Shipping --}}
            <h6 class="fw-bold mt-4 mb-3">Ongkos Kirim</h6>
            @if ($deliveryOrder->shippings->count())
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Kurir</th>
                                <th>Layanan</th>
                                <th>Estimasi</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deliveryOrder->shippings as $shipping)
                                <tr>
                                    <td>{{ $shipping->courier_name }}</td>
                                    <td>{{ $shipping->courier_service_name }}</td>
                                    <td>{{ $shipping->shipment_duration_range ?? '-' }} hari</td>
                                    <td class="text-end">{{ number_format($shipping->price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center alert alert-warning rounded-4">
                    Tidak ada data ongkos kirim yang tersimpan.
                </div>
            @endif

            {{-- Detail Produk --}}
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
                        @foreach ($deliveryOrder->details as $i => $detail)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>({{ $detail->product->kode_produk }}) - {{ $detail->product->nama_produk }}</td>
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
                            <td class="text-end">{{ $deliveryOrder->total_qty }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($deliveryOrder->total_diskon, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($deliveryOrder->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <a href="{{ route('transaksi_delivery_orders.index') }}" class="btn btn-sm btn-secondary rounded-4">
                Kembali <i class="bi bi-arrow-left-circle"></i>
            </a>

            @if ($approvalRoute && $deliveryOrder->approval_level == $approvalRoute->sequence - 1)
                @if (!($deliveryOrder->approval_level > 0))
                    <form action="{{ route('transaksi_delivery_orders.approve', $deliveryOrder->id) }}" method="POST"
                        class="d-inline form-approval">
                        @csrf
                        <button type="submit" class="btn btn-sm rounded-4 btn-success btn-approve">
                            <i class="bi bi-check2-square"></i> Ajukan
                        </button>
                    </form>
                @else
                    <div class="dropdown dropstart">
                        <button class="btn btn-sm rounded-4 btn-success dropdown-toggle h-100" type="button"
                            id="actionDropdown{{ $deliveryOrder->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                        </button>
                        <ul class="dropdown-menu dropdown-menu-start"
                            aria-labelledby="actionDropdown{{ $deliveryOrder->id }}">
                            <li>
                                <form action="{{ route('transaksi_delivery_orders.approve', $deliveryOrder->id) }}"
                                    method="POST" class="d-inline form-approval">
                                    @csrf
                                    <button type="submit" class="dropdown-item btn-approve text-success">Approve</button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('transaksi_delivery_orders.revise', $deliveryOrder->id) }}"
                                    method="POST" class="d-inline form-revisi">
                                    @csrf
                                    <button type="submit" class="dropdown-item btn-revisi text-warning">Revisi</button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('transaksi_delivery_orders.reject', $deliveryOrder->id) }}"
                                    method="POST" class="d-inline form-reject">
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
