<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Kode Auditi" name="kode_auditi" :value="$auditi->kode_auditi ?? ''" required readonly />
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Nama Auditi" name="nama_auditi" :value="$auditi->nama_auditi ?? ''" required />
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.select label="Irbanwil" name="irbanwil_id" :options="['' => 'Pilih Irbanwil'] + $irbanwils->pluck('nama', 'id')->toArray()" :value="old('irbanwil_id', $auditi->irbanwil_id ?? '')" />
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Telepon" name="telepon" type="text" :value="$auditi->telepon ?? ''" />
        </div>
    </div>

    <div class="col-md-8">
        <div class="form-group mb-3">
            <x-form.input label="Alamat" name="alamat" type="text" :value="$auditi->alamat ?? ''" />
        </div>
    </div>
</div>
