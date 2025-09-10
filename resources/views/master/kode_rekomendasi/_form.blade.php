<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Kode Rekomendasi" name="kode" :value="$kode_rekomendasi->kode ?? old('kode')" required />
        </div>
    </div>

    <div class="col-md-8">
        <div class="form-group mb-3">
            <x-form.input label="Nama Rekomendasi" name="nama_rekomendasi" :value="$kode_rekomendasi->nama_rekomendasi ?? old('nama_rekomendasi')" required />
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Urutan" name="urutan" type="number" :value="$kode_rekomendasi->urutan ?? old('urutan', 0)" required min="0" />
        </div>
    </div>
</div>
