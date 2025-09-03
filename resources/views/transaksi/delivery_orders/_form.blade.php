@php
    $deliveryOrder = $deliveryOrder ?? null;
    $salesOrder = $salesOrder ?? null;
    $customer = $customer ?? null;
    $deliveryDetails = $deliveryDetails ?? [];

    $tanggal = $deliveryOrder?->tanggal?->format('Y-m-d') ?? now()->format('Y-m-d');
    $tanggal_so = $salesOrder?->tanggal->format('d/m/Y') ?? '-';
    $metode_pembayaran = $salesOrder?->metode_pembayaran ?? '-';
    $kode_customer = $customer?->kode_customer ?? '-';
    $nama_toko = $customer?->nama_toko ?? '-';
    $alamat = $customer?->alamat ?? '-';
    $pemilik = $customer?->pemilik ?? '-';
    $id_pasar = $customer?->id_pasar ?? '-';
    $nama_pasar = $customer?->nama_pasar ?? '-';

    $origin = $deliveryOrder?->origin ?? null;
    $origin_name = $deliveryOrder?->origin_name ?? null;
    $destination = $deliveryOrder?->destination ?? null;
    $destination_name = $deliveryOrder?->destination_name ?? null;
    $total_qty = $deliveryOrder?->total_qty ?? '-';
    $total_diskon = $deliveryOrder?->total_diskon ?? '-';
    $grand_total = $deliveryOrder?->grand_total ?? '-';

    $origin = $deliveryOrder?->origin ?? null;
    $desination = $deliveryOrder?->desination ?? null;

    $details = $deliveryDetails ?? [];
@endphp

{{-- Header Delivery Order --}}
<div class="row gy-1 justify-content-center">
    {{-- Nomor Sales Order --}}
    <div class="col-md-4" id="so-select-wrapper">
        <div class="form-group mb-3">
            <label class="form-label" for="selectSalesOrder">Sales Order</label>
            <select id="selectSalesOrder" name="sales_order_id"
                class="form-select form-select-sm @error('sales_order_id') is-invalid @enderror">
                @if (isset($salesOrder->id))
                    <option value="{{ $salesOrder->id }}" selected>
                        {{ $salesOrder->no_so }} – {{ $salesOrder->customer->nama_toko }}
                    </option>
                @endif
            </select>
            @error('sales_order_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    {{-- Tanggal Kirim --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.date label="Tanggal Kirim" name="tanggal" :value="$tanggal" required />
        </div>
    </div>

</div>
<div class="row gy-3 mt-2">
    {{-- Sales Order Info --}}
    <div class="col-md-3">
        <label class="form-label" class="fw-bold mb-1 d-block">Tanggal Sales Order</label>
        <p id="tanggal_so" class="mb-0 text-muted">{{ $tanggal_so }}</p>
    </div>
    <div class="col-md-3">
        <label class="form-label" class="fw-bold mb-1 d-block">Metode Pembayaran</label>
        <p id="metode_pembayaran" class="mb-0 text-muted">{{ $metode_pembayaran }}</p>
    </div>

    {{-- Customer Info --}}
    <div class="col-md-3">
        <label class="form-label" class="fw-bold mb-1 d-block">Kode Toko</label>
        <p id="kode_customer" class="mb-0 text-muted">{{ $kode_customer }}</p>
    </div>
    <div class="col-md-3">
        <label class="form-label" class="fw-bold mb-1 d-block">Nama Toko</label>
        <p id="nama_toko" class="mb-0 text-muted">{{ $nama_toko }}</p>
    </div>
    <div class="col-md-6">
        <label class="form-label" class="fw-bold mb-1 d-block">Alamat</label>
        <p id="alamat" class="mb-0 text-muted">{{ $alamat }}</p>
    </div>
    <div class="col-md-3">
        <label class="form-label" class="fw-bold mb-1 d-block">Pemilik</label>
        <p id="pemilik" class="mb-0 text-muted">{{ $pemilik }}</p>
    </div>
    <div class="col-md-6">
        <label class="form-label" class="fw-bold mb-1 d-block">Pasar</label>
        <p id="pasar" class="mb-0 text-muted">({{ $id_pasar ?? '-' }}) {{ $nama_pasar ?? '-' }}</p>
    </div>
</div>

<hr>

{{-- Informasi Ekspedisi --}}
<h5>Informasi Pengiriman</h5>
<div id="area-wrapper">
    <div class="row gy-1">
        {{-- ORIGIN --}}
        <div class="col-md-6">
            <label class="form-label" for="origin">Asal Pengiriman</label>
            <select id="origin" name="origin"
                class="form-select form-select-sm area-select @error('origin') is-invalid @enderror">
                @if (old('origin', $origin))
                    <option value="{{ old('origin', $origin) }}" selected>
                        {{ old('origin_name', $origin_name) }}
                    </option>
                @endif
            </select>
            <input type="hidden" id="origin_name" name="origin_name"
                value="{{ old('origin_name', $origin_name ?? '') }}">
            @error('origin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- DESTINATION --}}
        <div class="col-md-6">
            <label class="form-label" for="destination">Tujuan Pengiriman</label>
            <select id="destination" name="destination"
                class="form-select form-select-sm area-select @error('destination') is-invalid @enderror">
                @if (old('destination', $destination))
                    <option value="{{ old('destination', $destination) }}" selected>
                        {{ old('destination_name', $destination_name) }}
                    </option>
                @endif
            </select>
            <input type="hidden" id="destination_name" name="destination_name"
                value="{{ old('destination_name', $deliveryOrder->destination_name ?? '') }}">
            @error('destination')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row gy-1 mt-2 justify-content-center">
            <div class="col-md-3">
                <button type="button" id="btnCekOngkir" class="btn btn-sm rounded-4 btn-primary w-100">
                    Cek Ongkir <i class="bi bi-truck"></i>
                </button>
            </div>
        </div>
        <div class="row gy-1 justify-content-center">
            <input type="hidden" name="shippings" id="shippings-data" value="">

            <div id="harga_ongkir_list" class="mt-3"></div>

            @if (isset($shippings))
                @php
                    $shippingArray = $shippings->map(function ($item) {
                        return [
                            'courier_code' => $item->courier_code,
                            'courier_name' => $item->courier_name,
                            'courier_service_name' => $item->courier_service_name,
                            'shipment_duration_range' => $item->shipment_duration_range,
                            'price' => $item->price,
                        ];
                    });
                @endphp
                <div id="shipping-init-data" data-shippings='@json($shippingArray)'></div>
            @endif
        </div>
    </div>
</div>

<hr>

{{-- Detail Produk --}}
<h5>Detail Produk</h5>
<div id="so-details-wrapper">
    @if (empty($details))
        <div class="text-center alert alert-warning rounded-4">
            Tidak ada detail yang ditampilkan.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th>Kemasan</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Diskon</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details as $item)
                        @php
                            $product_id = $item->product_id;
                        @endphp

                        <tr class="product-row" data-product_id="{{ $item->product_id }}"
                            data-nama="{{ $item->product->nama_produk }}"
                            data-description="{{ $item->product->kemasan }}" data-value="{{ $item->harga }}"
                            data-length="{{ $item->panjang ?? 10 }}" data-width="{{ $item->lebar ?? 10 }}"
                            data-height="{{ $item->tinggi ?? 10 }}" data-weight="{{ $item->berat ?? 1000 }}"
                            data-qty="{{ $item->qty }}" data-diskon="{{ $item->diskon }}"
                            data-subtotal="{{ $item->subtotal }}">

                            <input type="hidden" name="detail[{{ $product_id }}][product_id]"
                                value="{{ $item->product_id }}">

                            {{-- Nama --}}
                            <td>
                                {{ $item->product->kode_produk }} - {{ $item->product->nama_produk }}
                            </td>

                            {{-- Kemasan --}}
                            <td>
                                {{ $item->product->kemasan }}
                            </td>

                            {{-- Harga --}}
                            <td class="text-end">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                                <input type="hidden" name="detail[{{ $product_id }}][harga]"
                                    value="{{ $item->harga }}">
                            </td>

                            <td class="text-end">
                                {{ number_format($item->qty, 0, ',', '.') }}
                                <input type="hidden" name="detail[{{ $product_id }}][qty]"
                                    value="{{ $item->qty }}">
                            </td>

                            {{-- Diskon --}}
                            <td class="text-end">
                                Rp {{ number_format($item->diskon, 0, ',', '.') }}
                                <input type="hidden" name="detail[{{ $product_id }}][diskon]"
                                    value="{{ $item->diskon }}">
                            </td>

                            {{-- Subtotal --}}
                            <td class="text-end">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                <input type="hidden" name="detail[{{ $product_id }}][subtotal]"
                                    value="{{ $item->subtotal }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gd">
                    <tr>
                        <td colspan="5" class="text-end fw-semibold">QTY Total</td>
                        <td class="text-end" id="total-qty">
                            {{ number_format($total_qty ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end fw-semibold">Diskon Total</td>
                        <td class="text-end" id="total-diskon">
                            Rp {{ number_format($total_diskon ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end fw-semibold">Grand Total</td>
                        <td class="text-end fw-bold" id="grand-total">
                            Rp {{ number_format($grand_total ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>
