@extends('layouts.dashboard')

@php
    $page = 'pelaksanaan/tindak_lanjut_temuan';
    $action = 'show';
@endphp

@section('dashboard-content')
    <x-breadcrumbs />

    {{-- Informasi Umum --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Detail Tindak Lanjut Temuan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>No. LHP:</strong></p>
                    <p class="mb-3">{{ $tindak_lanjut_temuan->lhp->nomor_lhp ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Status Approval:</strong></p>
                    <span
                        class="badge bg-{{ $tindak_lanjut_temuan->approval_status === 'approved' ? 'success' : ($tindak_lanjut_temuan->approval_status === 'waiting' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($tindak_lanjut_temuan->approval_status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Temuan --}}
    @php
        $temuan = $tindak_lanjut_temuan->temuan; // satu data saja
    @endphp

    @if ($temuan)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0 fw-bold">
                    Judul : {{ $temuan->judul_temuan ?? 'Temuan' }}
                </h6>
            </div>

            <div class="card-body">
                {{-- Detail Temuan --}}
                <div class="mb-3">
                    <p class="mb-1"><strong>Kode Temuan:</strong></p>
                    <p class="mb-2">
                        {{ $temuan->kodeTemuan->kode ?? '-' }} |
                        {{ $temuan->kodeTemuan->nama_temuan ?? '-' }}
                    </p>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>Kondisi:</strong></p>
                        <div class="border rounded p-2 bg-light">{!! $temuan->kondisi_temuan ?? '-' !!}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>Kriteria:</strong></p>
                        <div class="border rounded p-2 bg-light">{!! $temuan->kriteria_temuan ?? '-' !!}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>Sebab:</strong></p>
                        <div class="border rounded p-2 bg-light">{!! $temuan->sebab_temuan ?? '-' !!}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>Akibat:</strong></p>
                        <div class="border rounded p-2 bg-light">{!! $temuan->akibat_temuan ?? '-' !!}</div>
                    </div>
                </div>

                {{-- File Pendukung --}}
                <div class="mb-3">
                    <p class="mb-1"><strong>File Pendukung:</strong></p>
                    @if ($temuan->files && $temuan->files->count())
                        <ul>
                            @foreach ($temuan->files as $file)
                                <li><a href="{{ asset('storage/' . $file->file_path) }}" target="_blank">
                                        {{ $file->file_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Tidak ada file pendukung</p>
                    @endif
                </div>

                {{-- Rekomendasi & Tindak Lanjut --}}
                <div>
                    <p class="fw-bold mb-2">Rekomendasi & Tindak Lanjut:</p>
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="width: 20%">Kode Rekomendasi</th>
                                <th style="width: 30%">Rekomendasi</th>
                                <th style="width: 20%">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($temuan->rekomendasis && $temuan->rekomendasis->count())
                                @foreach ($temuan->rekomendasis as $rekom)
                                    <tr>
                                        <td>
                                            {{ $rekom->kodeRekomendasi->kode ?? '-' }}<br>
                                            <small
                                                class="text-muted">{{ $rekom->kodeRekomendasi->nama_rekomendasi ?? '-' }}</small>
                                        </td>
                                        <td>{!! $rekom->rekomendasi_temuan ?? '-' !!}</td>
                                        <td class="text-end">{{ number_format($rekom->nominal ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada rekomendasi</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <hr>
                <div class="alert text-center {{ $masihDalamBatas ? 'alert-info' : 'alert-danger' }}">
                    <small>
                        @if ($masihDalamBatas)
                            Batas waktu pengisian tindak lanjut sampai:
                            <strong>{{ $batasWaktu->translatedFormat('d F Y') }}</strong>
                        @else
                            Waktu pengisian tindak lanjut sudah melewati batas terakhir
                            (<strong>{{ $batasWaktu->translatedFormat('d F Y') }}</strong>)
                        @endif
                    </small>
                </div>
                {{-- Input / Tampil Tindak Lanjut --}}
                <div class="mb-3 border border-primary rounded p-3">
                    <p class="fw-bold mb-2 text-center">Tindak Lanjut</p>

                    {{-- Mode input: hanya auditi dan draft --}}
                    @if (auth()->user()->hasRole('auditi') && $tindak_lanjut_temuan->approval_status === 'draft' && $canInputTindakLanjut)
                        <form action="{{ route('tindak_lanjut_temuan.update', $tindak_lanjut_temuan->id) }}" method="POST"
                            enctype="multipart/form-data" class="mt-3">
                            @csrf
                            @method('PUT')

                            {{-- Deskripsi Tindak Lanjut --}}
                            <div class="mb-3">
                                <label for="deskripsi_tindak_lanjut" class="form-label fw-bold">Deskripsi Tindak
                                    Lanjut</label>
                                <textarea name="deskripsi_tindak_lanjut" id="deskripsi_tindak_lanjut" rows="5" class="form-control summernote"
                                    required>{{ old('deskripsi_tindak_lanjut', $tindak_lanjut_temuan->deskripsi_tindak_lanjut) }}</textarea>
                            </div>

                            {{-- Lampiran --}}
                            <div class="mb-3">
                                <label for="lampiran" class="form-label fw-bold">Upload File Pendukung (opsional)</label>
                                <input type="file" name="lampiran[]" id="lampiran" class="form-control" multiple>
                                @if ($tindak_lanjut_temuan->lampiran)
                                    @php
                                        $files = is_array(json_decode($tindak_lanjut_temuan->lampiran, true))
                                            ? json_decode($tindak_lanjut_temuan->lampiran, true)
                                            : [$tindak_lanjut_temuan->lampiran];
                                    @endphp
                                    <small class="text-muted d-block mt-1">Lampiran saat ini:</small>
                                    <ul class="mb-0">
                                        @foreach ($files as $file)
                                            <li>
                                                <a href="{{ asset('storage/' . $file) }}" target="_blank">
                                                    {{ basename($file) }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            {{-- Catatan Approval --}}
                            @if ($tindak_lanjut_temuan->approval_note)
                                <div class="mb-3">
                                    <p class="mb-1"><strong>Catatan Approval:</strong></p>
                                    <div class="border rounded p-2 bg-light">
                                        {!! $tindak_lanjut_temuan->approval_note !!}
                                    </div>
                                </div>
                            @endif

                            <div class="text-center">
                                <button type="submit" class="btn btn-sm btn-primary rounded-4">
                                    <i class="bi bi-save"></i> Simpan Tindak Lanjut
                                </button>
                            </div>
                        </form>

                        {{-- Mode readonly --}}
                    @else
                        <div class="border rounded p-3 mt-2">

                            {{-- Deskripsi --}}
                            <div class="mb-3">
                                <p class="mb-1"><strong>Deskripsi Tindak Lanjut:</strong></p>
                                <div class="border rounded p-2 bg-light">
                                    {!! $tindak_lanjut_temuan->deskripsi_tindak_lanjut
                                        ? $tindak_lanjut_temuan->deskripsi_tindak_lanjut
                                        : '<em class="text-muted">Belum diisi</em>' !!}
                                </div>
                            </div>

                            {{-- Tanggal --}}
                            <div class="mb-3">
                                <p class="mb-1"><strong>Tanggal Tindak Lanjut:</strong></p>
                                <p class="mb-0">
                                    {{ $tindak_lanjut_temuan->tanggal_tindak_lanjut
                                        ? \Carbon\Carbon::parse($tindak_lanjut_temuan->tanggal_tindak_lanjut)->translatedFormat('d F Y')
                                        : '-' }}
                                </p>
                            </div>

                            {{-- Lampiran --}}
                            <div class="mb-3">
                                <p class="mb-1"><strong>Lampiran:</strong></p>
                                @if ($tindak_lanjut_temuan->lampiran)
                                    @php
                                        $files = is_array(json_decode($tindak_lanjut_temuan->lampiran, true))
                                            ? json_decode($tindak_lanjut_temuan->lampiran, true)
                                            : [$tindak_lanjut_temuan->lampiran];
                                    @endphp
                                    <ul class="mb-0">
                                        @foreach ($files as $file)
                                            <li>
                                                <a href="{{ asset('storage/' . $file) }}" target="_blank">
                                                    {{ basename($file) }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted mb-0">Tidak ada lampiran</p>
                                @endif
                            </div>

                            {{-- Catatan Approval --}}
                            @if ($tindak_lanjut_temuan->approval_note)
                                <div class="mb-3">
                                    <p class="mb-1"><strong>Catatan Approval:</strong></p>
                                    <div class="border rounded p-2 bg-light">
                                        {!! $tindak_lanjut_temuan->approval_note !!}
                                    </div>
                                </div>
                            @endif

                        </div>
                    @endif
                </div>
            </div>
            {{-- Footer --}}
            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ route('tindak_lanjut_temuan.index') }}" class="btn btn-sm btn-secondary rounded-4">
                    <i class="bi bi-x-square-fill"></i> Kembali
                </a>

                @if ($canApprove && auth()->user()->hasRole('auditor'))
                    @if ($tindak_lanjut_temuan->approval_status === 'draft')
                        <button type="button" class="btn btn-success btn-sm rounded-4 btn-approve" data-action="approve"
                            data-url="{{ route('tindak_lanjut_temuan.approve', $tindak_lanjut_temuan->id) }}"
                            data-redirect="{{ route('tindak_lanjut_temuan.index') }}">
                            <i class="bi bi-send"></i> Kirim untuk Approval
                        </button>
                    @elseif($tindak_lanjut_temuan->approval_status === 'waiting')
                        <button type="button" class="btn btn-success btn-sm rounded-4 btn-approve" data-action="approve"
                            data-url="{{ route('tindak_lanjut_temuan.approve', $tindak_lanjut_temuan->id) }}"
                            data-redirect="{{ route('tindak_lanjut_temuan.index') }}">
                            <i class="bi bi-check-circle"></i> Setujui
                        </button>

                        <button type="button" class="btn btn-secondary btn-sm rounded-4 btn-revise" data-action="revise"
                            data-url="{{ route('tindak_lanjut_temuan.approve', $tindak_lanjut_temuan->id) }}"
                            data-redirect="{{ route('tindak_lanjut_temuan.index') }}">
                            <i class="bi bi-arrow-return-left"></i> Revisi
                        </button>
                    @endif
                @endif
            </div>
        </div>
    @else
        <p class="text-center text-muted">Tidak ada data temuan untuk tindak lanjut ini.</p>
    @endif
@endsection
