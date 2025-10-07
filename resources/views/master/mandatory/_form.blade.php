<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <x-form.input label="Mandatory" name="nama" :value="$mandatory->nama ?? old('mandatory')" required />
        </div>
    </div>
</div>
