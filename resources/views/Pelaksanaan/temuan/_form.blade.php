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
        <input type="text" name="kode_temuan" class="form-control"
            value="{{ old('kode_temuan', $temuan->kode_temuan ?? '') }}">
    </div>

    {{-- Kondisi, Kriteria, Sebab, Akibat --}}
    <div class="col-md-12 mb-3">
        <label class="form-label">Kondisi Temuan</label>
        <textarea name="kondisi_temuan" class="form-control" rows="3">{{ old('kondisi_temuan', $temuan->kondisi_temuan ?? '') }}</textarea>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Kriteria Temuan</label>
        <textarea name="kriteria_temuan" class="form-control" rows="3">{{ old('kriteria_temuan', $temuan->kriteria_temuan ?? '') }}</textarea>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Sebab Temuan</label>
        <textarea name="sebab_temuan" class="form-control" rows="3">{{ old('sebab_temuan', $temuan->sebab_temuan ?? '') }}</textarea>
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Akibat Temuan</label>
        <textarea name="akibat_temuan" class="form-control" rows="3">{{ old('akibat_temuan', $temuan->akibat_temuan ?? '') }}</textarea>
    </div>

    {{-- Rekomendasi Dinamis --}}
    <div class="col-md-12 mb-3" id="rekomendasi-wrapper">
        <label class="form-label">Rekomendasi Temuan</label>
        @php $oldRekom = old('rekomendasis', $temuan->rekomendasis ?? []); @endphp
        @foreach ($oldRekom as $i => $r)
            <div class="input-group mb-2 rekomendasi-item">
                <input type="text" name="rekomendasis[{{ $i }}][kode_rekomendasi]" class="form-control"
                    placeholder="Kode Rekomendasi" value="{{ $r['kode_rekomendasi'] ?? '' }}">
                <input type="text" name="rekomendasis[{{ $i }}][rekomendasi_temuan]" class="form-control"
                    placeholder="Rekomendasi" value="{{ $r['rekomendasi_temuan'] ?? '' }}">
                @if ($loop->last)
                    <button type="button" class="btn btn-success add-rekomendasi">+</button>
                @else
                    <button type="button" class="btn btn-danger remove-rekomendasi">-</button>
                @endif
            </div>
        @endforeach

        @if (empty($oldRekom))
            <div class="input-group mb-2 rekomendasi-item">
                <input type="text" name="rekomendasis[0][kode_rekomendasi]" class="form-control"
                    placeholder="Kode Rekomendasi">
                <input type="text" name="rekomendasis[0][rekomendasi_temuan]" class="form-control"
                    placeholder="Rekomendasi">
                <button type="button" class="btn btn-success add-rekomendasi">+</button>
            </div>
        @endif
    </div>

    {{-- Upload File Dinamis --}}
    <div class="col-md-12 mb-3" id="file-wrapper">
        <label class="form-label">Upload Bukti Pendukung</label>
        <div class="input-group mb-2 file-item">
            <input type="file" name="files[]" class="form-control">
            <button type="button" class="btn btn-success add-file">+</button>
        </div>

        @if (isset($temuan) && $temuan->files->count())
            @foreach ($temuan->files as $file)
                <small class="d-block text-muted">
                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">{{ $file->file_name }}</a>
                </small>
            @endforeach
        @endif
    </div>

</div>
