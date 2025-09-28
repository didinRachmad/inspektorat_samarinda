<div class="row">
    {{-- Tahun --}}
    <div class="col-md-3 mb-3">
        <label class="form-label">Tahun</label>
        <input type="number" name="tahun" class="form-control" value="{{ old('tahun', $pkpt->tahun ?? date('Y')) }}"
            required>
    </div>

    {{-- Bulan --}}
    <div class="col-md-3 mb-3">
        <label class="form-label">Bulan</label>
        <select name="bulan" class="form-select">
            <option value="">-- Pilih Bulan --</option>
            @foreach (range(1, 12) as $m)
                <option value="{{ $m }}" @selected(old('bulan', $pkpt->bulan ?? '') == $m)>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- No PKPT --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">No PKPT</label>
        <input type="text" name="no_pkpt" class="form-control" value="{{ old('no_pkpt', $pkpt->no_pkpt ?? '') }}"
            readonly>
    </div>
</div>

<div class="row">
    {{-- Auditi --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Auditi</label>
        <select name="auditi_id" class="form-select" required>
            <option value="">-- pilih Auditi --</option>
            @foreach ($auditis as $auditi)
                <option value="{{ $auditi->id }}"
                    {{ (old('auditi_id') ?? ($pkpt->auditi_id ?? '')) == $auditi->id ? 'selected' : '' }}>
                    {{ $auditi->kode_auditi }} - {{ $auditi->nama_auditi }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Sasaran --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Sasaran</label>
        <input type="text" name="sasaran" class="form-control" value="{{ old('sasaran', $pkpt->sasaran ?? '') }}"
            required>
    </div>
</div>

<div class="row">
    {{-- Ruang Lingkup --}}
    <div class="col-md-12 mb-3">
        <label class="form-label">Ruang Lingkup</label>
        <textarea name="ruang_lingkup" class="form-control">{{ old('ruang_lingkup', $pkpt->ruang_lingkup ?? '') }}</textarea>
    </div>
</div>

<div class="row">
    {{-- Jenis Pengawasan --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Jenis Pengawasan</label>
        <select name="jenis_pengawasan" class="form-select" required>
            @foreach (['REVIU', 'AUDIT', 'PENGAWASAN', 'EVALUASI', 'MONITORING', 'PRA_REVIU'] as $item)
                <option value="{{ $item }}" @selected(old('jenis_pengawasan', $pkpt->jenis_pengawasan ?? '') === $item)>{{ $item }}</option>
            @endforeach
        </select>
    </div>

    {{-- Irbanwil --}}
    <div class="col-md-6 mb-3">
        <label class="form-label">Irbanwil</label>
        <select name="irbanwil" class="form-select">
            <option value="">-- Pilih Irbanwil --</option>
            @foreach (['SEMUA IRBAN', 'IRBAN I', 'IRBAN II', 'IRBAN III', 'IRBAN IV', 'IRBAN KHUSUS'] as $item)
                <option value="{{ $item }}" @selected(old('irbanwil', $pkpt->irbanwil ?? '') === $item)>{{ $item }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    {{-- Jadwal RPM, RSP, RPL, HP --}}
    <div class="col-md-3 mb-3">
        <label class="form-label">Rencana Pemeriksaan Mulai (RPM)</label>
        <select name="jadwal_rmp_bulan" class="form-select">
            <option value="">-- Pilih Bulan --</option>
            @foreach (range(1, 12) as $m)
                <option value="{{ $m }}" @selected(old('jadwal_rmp_bulan', $pkpt->jadwal_rmp_bulan ?? '') == $m)>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Rencana Selesai Pemeriksaan (RSP)</label>
        <select name="jadwal_rsp_bulan" class="form-select">
            <option value="">-- Pilih Bulan --</option>
            @foreach (range(1, 12) as $m)
                <option value="{{ $m }}" @selected(old('jadwal_rsp_bulan', $pkpt->jadwal_rsp_bulan ?? '') == $m)>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Rencana Penerbitan Laporan (RPL)</label>
        <select name="jadwal_rpl_bulan" class="form-select">
            <option value="">-- Pilih Bulan --</option>
            @foreach (range(1, 12) as $m)
                <option value="{{ $m }}" @selected(old('jadwal_rpl_bulan', $pkpt->jadwal_rpl_bulan ?? '') == $m)>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Hari Pemeriksaan (HP)</label>
        <input type="number" name="jadwal_hp_hari" class="form-control"
            value="{{ old('jadwal_hp_hari', $pkpt->jadwal_hp_hari ?? '') }}">
    </div>
</div>

<hr>
<h6>Detail Jabatan (PJ / WPJ / PT / KT / AT)</h6>
<table class="table table-bordered" id="jabatanTable">
    <thead>
        <tr>
            <th>Jabatan</th>
            <th>Jumlah Pejabat</th>
            <th>Anggaran</th>
        </tr>
    </thead>
    <tbody>
        @php
            $jabatanList = ['PJ', 'WPJ', 'PT', 'KT', 'AT'];
            $rows = old('jabatans', isset($pkpt) ? $pkpt->jabatans->toArray() : []);
        @endphp

        @foreach ($jabatanList as $i => $jabatan)
            @php
                // ambil row dari data lama jika ada
                $row = $rows[$i] ?? ['jabatan' => $jabatan, 'jumlah' => 1, 'anggaran' => 0];
            @endphp
            <tr>
                <td>
                    <select name="jabatans[{{ $i }}][jabatan]" class="form-select" required>
                        <option value="{{ $jabatan }}" selected>{{ $jabatan }}</option>
                    </select>
                </td>
                <td>
                    <input type="number" min="0" name="jabatans[{{ $i }}][jumlah]"
                        class="form-control jumlah" value="{{ $row['jumlah'] ?? 1 }}" required>
                </td>
                <td>
                    <input type="text" name="jabatans[{{ $i }}][anggaran]"
                        class="form-control anggaran numeric" value="{{ $row['anggaran'] ?? 0 }}" required>
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" class="text-end">Total Anggaran</th>
            <th>
                <input type="text" name="anggaran_total" id="totalAnggaran" class="form-control numeric" readonly
                    value="0">
            </th>
        </tr>
    </tfoot>
</table>

<hr>
{{-- Keterangan --}}
<div class="row">
    <div class="col-md-12 mb-3">
        <label class="form-label">Keterangan</label>
        <input type="text" name="keterangan" class="form-control"
            value="{{ old('keterangan', $pkpt->keterangan ?? '') }}">
    </div>
</div>
<hr>
<div class="row">
    {{-- File Surat Tugas --}}
    <div class="col-md-12 mb-3">
        <label class="form-label">File Surat Tugas</label>
        <input type="file" name="file_surat_tugas" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">

        {{-- Jika sudah ada file sebelumnya --}}
        @if (!empty($pkpt->file_surat_tugas))
            <div class="mt-2">
                <p>File saat ini:
                    <a href="{{ asset('storage/' . $pkpt->file_surat_tugas) }}" target="_blank">
                        Lihat File
                    </a>
                </p>
            </div>
        @endif
    </div>
</div>
