<div class="row">
    {{-- Nama Jenis Pengawasan --}}
    <div class="col-md-6">
        <div class="form-group mb-3">
            <x-form.input label="Nama Jenis Pengawasan" name="nama" :value="$jenis_pengawasan->nama ?? old('nama')" required
                placeholder="Contoh: Audit Kinerja" />
        </div>
    </div>

    {{-- Urutan --}}
    <div class="col-md-6">
        <div class="form-group mb-3">
            <x-form.input label="Urutan" name="urutan" type="number" :value="$jenis_pengawasan->urutan ?? 1" required min="1" />
        </div>
    </div>
</div>
<hr>
<div class="row mb-2">
    <div class="col-md-6">
        <label class="form-label fw-bold">Sub Jenis</label>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold">Urutan</label>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold">Aksi</label>
    </div>
</div>
{{-- Bagian Child (jika parent sedang di-edit) --}}
@if (isset($jenis_pengawasan) && $jenis_pengawasan->children->count())
    <div id="child-items">
        @foreach ($jenis_pengawasan->children as $index => $child)
            <div class="row mb-2 child-item">
                <div class="col-md-6">
                    <x-form.input name="children[{{ $index }}][nama]" :value="$child->nama" required
                        placeholder="Contoh: Audit Kinerja" />
                </div>
                <div class="col-md-3">
                    <x-form.input name="children[{{ $index }}][urutan]" type="number" :value="$child->urutan ?? 0" required
                        min="0" />
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-child">Hapus <i
                            class="bi bi-trash-fill"></i></button>
                </div>
            </div>
        @endforeach
    </div>

    <button type="button" class="btn btn-success btn-sm mt-2" id="add-child">Tambah <i
            class="bi bi-plus-circle"></i></button>
@else
    <div id="child-items"></div>
    <button type="button" class="btn btn-success btn-sm mt-2" id="add-child">Tambah <i
            class="bi bi-plus-circle"></i></button>
@endif
