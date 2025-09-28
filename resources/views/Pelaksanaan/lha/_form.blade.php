<div class="row">
    {{-- Nomor LHA --}}
    <div class="col-md-3 mb-3">
        <label class="form-label">Nomor LHA</label>
        <input type="text" name="nomor_lha" class="form-control"
            value="{{ old('nomor_lha') ?? ($lha->nomor_lha ?? '') }}" readonly>
    </div>

    {{-- PKPT --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">PKPT</label>
        <select name="pkpt_id" id="pkptSelect" class="form-select" required>
            <option value="">-- pilih PKPT --</option>
            @foreach ($pkpts as $p)
                <option value="{{ $p->id }}" data-jenis="{{ $p->jenis_pengawasan }}"
                    {{ (old('pkpt_id') ?? ($lha->pkpt_id ?? '')) == $p->id ? 'selected' : '' }}>
                    {{ $p->tahun }}
                    @if ($p->bulan)
                        - {{ \Carbon\Carbon::create()->month($p->bulan)->translatedFormat('F') }}
                    @endif
                    | {{ $p->no_pkpt }}
                    | {{ $p->auditi->nama_auditi }}
                    @if ($p->sasaran)
                        | {{ Str::limit($p->sasaran, 30) }}
                    @endif
                </option>
            @endforeach
        </select>
    </div>

    {{-- Tanggal LHA --}}
    <div class="col-md-3 mb-3">
        <label class="form-label">Tanggal LHA</label>
        <input type="date" name="tanggal_lha" class="form-control"
            value="{{ old('tanggal_lha') ?? (isset($lha) ? optional($lha->tanggal_lha)->format('Y-m-d') : '') }}"
            min="{{ date('Y-m-d') }}">
    </div>
</div>

{{-- Uraian Temuan & Rekomendasi --}}
<div id="auditFields" style="display: none;">
    {{-- Uraian Temuan --}}
    <div class="mb-3">
        <label class="form-label">Uraian Temuan</label>
        <textarea name="uraian_temuan" class="form-control" rows="4">{{ old('uraian_temuan') ?? ($lha->uraian_temuan ?? '') }}</textarea>
    </div>

    {{-- Rekomendasi --}}
    <div class="mb-3">
        <label class="form-label">Rekomendasi</label>
        <textarea name="rekomendasi" class="form-control" rows="4">{{ old('rekomendasi') ?? ($lha->rekomendasi ?? '') }}</textarea>
    </div>
</div>


{{-- File LHA --}}
<div class="mb-3">
    <label class="form-label">File LHA (pdf/doc)</label>
    <input type="file" name="file_lha" class="form-control">
    @if (isset($lha) && $lha->file_lha)
        <small class="text-muted">File saat ini:
            <a href="{{ asset('storage/' . $lha->file_lha) }}" target="_blank">{{ basename($lha->file_lha) }}</a>
        </small>
    @endif
</div>
