@extends('layouts.dashboard')
@php
    $page = 'pelaksanaan/lhp';
    $action = 'show';
@endphp

@section('dashboard-content')
    <x-breadcrumbs></x-breadcrumbs>

    {{-- Informasi LHP --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Informasi LHP</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Nomor LHP:</strong></p>
                    <p class="mb-0">{{ $lhp->nomor_lhp }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>No PKPT:</strong></p>
                    <p class="mb-0">{{ $lhp->pkpt->no_pkpt ?? '-' }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Tanggal LHP:</strong></p>
                    <p class="mb-0">{{ optional($lhp->tanggal_lhp)->format('d-m-Y') }}</p>
                </div>
                <div class="col-md-6">
                    @if ($lhp->file_lhp)
                        <p class="mb-1"><strong>File LHP:</strong></p>
                        <a href="{{ asset('storage/' . $lhp->file_lhp) }}" target="_blank"
                            class="btn btn-sm btn-outline-info rounded-4">
                            <i class="bi bi-download"></i> Download
                        </a>
                    @endif
                </div>
            </div>

            @if ($lhp->uraian_temuan)
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="mb-1"><strong>Uraian Temuan:</strong></p>
                        <p class="mb-0">{!! $lhp->uraian_temuan !!}</p>
                    </div>
                </div>
            @endif

            @if ($lhp->rekomendasi)
                <div class="row mb-3">
                    <div class="col-12">
                        <p class="mb-1"><strong>Rekomendasi:</strong></p>
                        <p class="mb-0">{!! $lhp->rekomendasi !!}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Daftar KKA --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Daftar KKA</h6>
            <button class="btn btn-sm btn-primary rounded-4" id="btnAddKka">
                <i class="bi bi-plus-lg"></i> Tambah KKA
            </button>
        </div>
        <div class="card-body p-3">
            <div id="kkaList">
                @include('pelaksanaan.lhp._kka_list', ['kkas' => $lhp->kkas])
            </div>
        </div>
    </div>

    {{-- Daftar Temuan --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Daftar Temuan</h5>
        </div>
        <div class="card-body">
            @forelse($lhp->temuans as $index => $temuan)
                <div class="temuan-item border border-primary rounded p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Temuan #{{ $index + 1 }} - {{ $temuan->judul_temuan }}</h6>
                    </div>

                    {{-- Detail Temuan --}}
                    <div class="row mb-2">
                        <div class="col-md-6 mb-2">
                            <p class="mb-1"><strong>Kode Temuan:</strong></p>
                            <p class="mb-0">{{ $temuan->kodeTemuan->kode ?? '-' }} |
                                {{ $temuan->kodeTemuan->nama_temuan ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-12 mb-2">
                            <p class="mb-1"><strong>Kondisi:</strong></p>
                            <div class="border rounded p-2 bg-light">{!! $temuan->kondisi_temuan !!}</div>
                        </div>
                        <div class="col-12 mb-2">
                            <p class="mb-1"><strong>Kriteria:</strong></p>
                            <div class="border rounded p-2 bg-light">{!! $temuan->kriteria_temuan !!}</div>
                        </div>
                        <div class="col-12 mb-2">
                            <p class="mb-1"><strong>Sebab:</strong></p>
                            <div class="border rounded p-2 bg-light">{!! $temuan->sebab_temuan !!}</div>
                        </div>
                        <div class="col-12 mb-2">
                            <p class="mb-1"><strong>Akibat:</strong></p>
                            <div class="border rounded p-2 bg-light">{!! $temuan->akibat_temuan !!}</div>
                        </div>
                    </div>

                    {{-- Rekomendasi Temuan --}}
                    <div class="mb-3">
                        <p class="mb-1"><strong>Rekomendasi:</strong></p>
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode Rekomendasi</th>
                                    <th>Rekomendasi</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($temuan->rekomendasis as $rekom)
                                    <tr>
                                        <td>{{ $rekom->kodeRekomendasi->kode ?? '-' }} |
                                            {{ $rekom->kodeRekomendasi->nama_rekomendasi ?? '-' }}</td>
                                        <td>{!! $rekom->rekomendasi_temuan !!}</td>
                                        <td class="text-end">{{ number_format($rekom->nominal, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada rekomendasi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- File Pendukung --}}
                    <div>
                        <p class="mb-1"><strong>File Pendukung:</strong></p>
                        <ul>
                            @forelse($temuan->files as $file)
                                <li><a href="{{ asset('storage/' . $file->file_path) }}"
                                        target="_blank">{{ $file->file_name }}</a></li>
                            @empty
                                <li>Tidak ada file pendukung</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">Belum ada temuan untuk LHP ini.</p>
            @endforelse
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <a href="{{ route('lhp.index') }}" class="btn btn-sm btn-secondary rounded-4">
                <i class="bi bi-x-square-fill"></i> Kembali
            </a>

            @if ($canApprove ?? false)
                @if ($lhp->approval_status === 'draft')
                    <button type="button" class="btn btn-success btn-sm rounded-4 btn-approve" data-action="approve"
                        data-url="{{ route('lhp.approve', $lhp->id) }}" data-redirect="{{ route('lhp.index') }}">
                        <i class="bi bi-send"></i> Kirim untuk Approval
                    </button>
                @elseif($lhp->approval_status === 'waiting')
                    <button type="button" class="btn btn-success btn-sm rounded-4 btn-approve" data-action="approve"
                        data-url="{{ route('lhp.approve', $lhp->id) }}" data-redirect="{{ route('lhp.index') }}">
                        <i class="bi bi-check-circle"></i> Setujui
                    </button>

                    <button type="button" class="btn btn-secondary btn-sm rounded-4 btn-revise" data-action="revise"
                        data-url="{{ route('lhp.approve', $lhp->id) }}" data-redirect="{{ route('lhp.index') }}">
                        <i class="bi bi-arrow-return-left"></i> Revisi
                    </button>

                    <button type="button" class="btn btn-danger btn-sm rounded-4 btn-reject" data-action="reject"
                        data-url="{{ route('lhp.approve', $lhp->id) }}" data-redirect="{{ route('lhp.index') }}">
                        <i class="bi bi-x-circle"></i> Tolak
                    </button>
                @endif
            @endif
        </div>

    </div>

    {{-- Modal Tambah KKA --}}
    <div class="modal fade" id="modalKka" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content rounded-4 shadow-sm">
                <div class="modal-header">
                    <h5 class="modal-title mb-0">Tambah KKA</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formKka" action="{{ route('kka.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="lhp_id" value="{{ $lhp->id }}">

                        <div class="mb-3">
                            <label class="form-label">Judul KKA</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Uraian Prosedur</label>
                            <input type="text" name="uraian_prosedur" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hasil</label>
                            <textarea name="hasil" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">File KKA (opsional)</label>
                            <input type="file" name="file_kka" class="form-control" accept=".pdf,.doc,.docx">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle-fill"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary rounded-4" id="btnSaveKka">
                        <i class="bi bi-save-fill"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
