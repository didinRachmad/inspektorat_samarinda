<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Kode Customer" name="kode_customer" :value="$customer->kode_customer ?? ''" required />
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Nama Toko" name="nama_toko" :value="$customer->nama_toko ?? ''" required />
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Pemilik" name="pemilik" :value="$customer->pemilik ?? ''" />
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group mb-3">
            <x-form.textarea label="Alamat" name="alamat" :value="$customer->alamat ?? ''" required />
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group mb-3">
            <x-form.input label="ID Pasar" name="id_pasar" type="number" :value="$customer->id_pasar ?? ''" required />
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <x-form.input label="Nama Pasar" name="nama_pasar" :value="$customer->nama_pasar ?? ''" required />
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group mb-3">
            <x-form.select label="Tipe Outlet" name="tipe_outlet" :options="['retail' => 'retail', 'grosir' => 'grosir']" :value="$customer->tipe_outlet ?? ''" required />
        </div>
    </div>
</div>
