<div class="row">
    {{-- Nomor LHP --}}
    <div class="col-md-3 mb-3">
        <label class="form-label">Nomor LHP</label><span class="text-warning"><small> (readonly)</small></span>
        <input type="text" name="nomor_lhp" class="form-control"
            value="{{ old('nomor_lhp') ?? ($lhp->nomor_lhp ?? '') }}" readonly>
    </div>

    {{-- PKPT --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">PKPT</label>
        <select name="pkpt_id" id="pkptSelect" class="form-select select2" required
            data-selected="{{ old('pkpt_id') ?? ($lhp->pkpt_id ?? '') }}"
            data-selected-auditi="{{ old('auditi_id') ?? ($lhp->auditi_id ?? '') }}">
            <option value="">-- pilih PKPT --</option>
            @foreach ($pkpts as $p)
                <option value="{{ $p->id }}" data-jenis="{{ $p->jenisPengawasan?->nama_jenis_pengawasan ?? '' }}"
                    data-auditis='@json($p->auditis->map(fn($a) => ['id' => $a->id, 'nama_auditi' => $a->nama_auditi]))'
                    {{ (old('pkpt_id') ?? ($lhp->pkpt_id ?? '')) == $p->id ? 'selected' : '' }}>
                    {{ $p->tahun }}
                    @if ($p->bulan)
                        - {{ \Carbon\Carbon::create()->month($p->bulan)->translatedFormat('F') }}
                    @endif
                    | {{ $p->no_pkpt }}
                    @if ($p->auditis->count())
                        | Auditi: {{ $p->auditis->pluck('nama_auditi')->implode(', ') }}
                    @endif
                    @if ($p->sasaran)
                        | {{ Str::limit($p->sasaran, 30) }}
                    @endif
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Auditi</label>
        <select name="auditi_id" id="auditiSelect" class="form-select select2" required>
            <option value="">-- pilih auditi --</option>
            {{-- Options akan diisi via JS berdasarkan pkpt_id --}}
        </select>
    </div>


    {{-- Tanggal LHP --}}
    <div class="col-md-3 mb-3">
        <label class="form-label">Tanggal LHP</label>
        <input type="text" name="tanggal_lhp" class="form-control filterTanggal"
            value="{{ old('tanggal_lhp') ?? (isset($lhp) ? \Carbon\Carbon::parse($lhp->tanggal_lhp)->format('d-m-Y') : '') }}">
    </div>

    <div class="col-md-9 mb-3">
        {{-- File LHP --}}
        <label class="form-label">File LHP (pdf/doc)</label>
        <input type="file" name="file_lhp" class="form-control">
        @if (isset($lhp) && $lhp->file_lhp)
            <small class="text-muted">File saat ini:
                <a href="{{ asset('storage/' . $lhp->file_lhp) }}" target="_blank">{{ basename($lhp->file_lhp) }}</a>
            </small>
        @endif
    </div>
</div>
