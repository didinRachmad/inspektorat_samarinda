<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <x-form.select label="Irbanwil" name="irbanwil_id" :options="['' => 'Pilih Irbanwil'] + $irbanwils->pluck('nama', 'id')->toArray()" :value="old('irbanwil_id', $auditi->irbanwil_id ?? '')" />
        </div>
    </div>
</div>
