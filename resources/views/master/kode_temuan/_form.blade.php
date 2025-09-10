<div class="row">
    {{-- Kode Temuan --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Kode Temuan" name="kode" :value="$kode_temuan->kode ?? ''" required placeholder="Contoh: 1.01.00" />
        </div>
    </div>

    {{-- Nama Temuan --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Nama Temuan" name="nama_temuan" :value="$kode_temuan->nama_temuan ?? ''" required />
        </div>
    </div>

    {{-- Parent / Kategori --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.select label="Parent Kode Temuan" name="parent_id" :options="['' => 'Pilih Parent'] + $parentOptions->pluck('nama_temuan', 'id')->toArray()" :value="$kode_temuan->parent_id ?? old('parent_id')" />
        </div>
    </div>

    {{-- Level (otomatis dari parent, tapi bisa diinput manual jika perlu) --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Level" name="level" type="number" :value="$kode_temuan->level ?? 1" required min="1" />
        </div>
    </div>

    {{-- Urutan --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Urutan" name="urutan" type="number" :value="$kode_temuan->urutan ?? 0" required min="0" />
        </div>
    </div>
</div>
