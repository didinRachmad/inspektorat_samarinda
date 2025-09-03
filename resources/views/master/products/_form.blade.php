<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Kode Produk" name="kode_produk" :value="$product->kode_produk ?? ''" required />
        </div>
    </div>

    <div class="col-md-8">
        <div class="form-group mb-3">
            <x-form.input label="Nama Produk" name="nama_produk" :value="$product->nama_produk ?? ''" required />
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Harga" name="harga" type="text" :value="$product->harga ?? ''" format required
                class="numeric" />
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.select label="Kemasan" name="kemasan" :options="['Pack' => 'Pack', 'Rtg' => 'Rtg', 'Pcs' => 'Pcs', 'Krt' => 'Krt']" :value="$product->kemasan ?? ''" required />
        </div>
    </div>
</div>
