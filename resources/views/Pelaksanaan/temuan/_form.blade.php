<div class="row justify-content-center">

    {{-- Pilih LHA --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Nomor LHA</label>
        <select name="lha_id" class="form-select" required>
            <option value="">-- pilih LHA --</option>
            @foreach ($lhas as $l)
                <option value="{{ $l->id }}"
                    {{ (old('lha_id') ?? ($temuan->lha_id ?? '')) == $l->id ? 'selected' : '' }}>
                    {{ $l->nomor_lha }} | {{ $l->pkpt->auditi->nama_auditi ?? '-' }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    {{-- Judul & Kode Temuan --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Judul Temuan</label>
        <input type="text" name="judul_temuan" class="form-control"
            value="{{ old('judul_temuan', $temuan->judul_temuan ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Kode Temuan</label>
        <select name="kode_temuan_id" class="form-select" required>
            <option value="">-- Pilih Kode Temuan --</option>
            @foreach ($kodeTemuans as $kt)
                <option value="{{ $kt->id }}"
                    {{ (old('kode_temuan_id') ?? ($temuan->kode_temuan_id ?? '')) == $kt->id ? 'selected' : '' }}>
                    {{ $kt->kode }} | {{ $kt->nama_temuan }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Kondisi, Kriteria, Sebab, Akibat --}}
    <div class="col-md-12 mb-3">
        <label class="form-label">Kondisi Temuan</label>
        <textarea name="kondisi_temuan" class="form-control summernote" rows="3">{{ old('kondisi_temuan', $temuan->kondisi_temuan ?? '') }}</textarea>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Kriteria Temuan</label>
        <textarea name="kriteria_temuan" class="form-control summernote" rows="3">{{ old('kriteria_temuan', $temuan->kriteria_temuan ?? '') }}</textarea>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Sebab Temuan</label>
        <textarea name="sebab_temuan" class="form-control summernote" rows="3">{{ old('sebab_temuan', $temuan->sebab_temuan ?? '') }}</textarea>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Akibat Temuan</label>
        <textarea name="akibat_temuan" class="form-control summernote" rows="3">{{ old('akibat_temuan', $temuan->akibat_temuan ?? '') }}</textarea>
    </div>
</div>
