@extends('layouts.dashboard')

@php
    $page = 'dashboard';
    $action = 'index';
    $user = Auth::user();
@endphp

@section('dashboard-content')
    <div class="row g-1">

        {{-- === Card Selamat Datang === --}}
        <div class="col-12">
            <div class="card rounded-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <h4 class="fw-bold mb-1 text-white">
                                👋 Selamat datang, {{ $user->name }}
                            </h4>
                            <p class="mb-0 text-white-50">
                                Anda login sebagai
                                <span class="fw-semibold text-info">
                                    {{ $user->getRoleNames()->first() ?? 'Belum punya role' }}
                                </span>.
                            </p>
                        </div>
                        <div class="text-end">
                            <i class="bi bi-person-check fs-1 text-info opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- === Card Tenggat Waktu Tindak Lanjut === --}}
        @if ($tindakLanjut->isNotEmpty())
            <div class="col-12">
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-header bg-info bg-gradient text-white rounded-top-4 py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-semibold">
                                <i class="bi bi-hourglass-split me-2"></i> Tindak Lanjut Dalam Tenggat Waktu
                            </h5>
                            <span class="badge bg-light text-info px-3 py-2">
                                {{ $tindakLanjut->count() }} item
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach ($tindakLanjut as $item)
                                @php
                                    $isOverdue = $item->sisa_hari < 0;
                                    $badgeColor = $isOverdue
                                        ? 'danger'
                                        : ($item->sisa_hari <= 7
                                            ? 'warning'
                                            : 'success');
                                @endphp

                                <div
                                    class="list-group-item bg-transparent border-secondary border-opacity-25 px-4 py-3 d-flex justify-content-between align-items-start flex-wrap gap-3">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-semibold mb-1 text-white">
                                            {{ $item->temuan->judul_temuan ?? 'Tanpa Judul' }}
                                        </h6>
                                        <div class="small text-white-50">
                                            <i class="bi bi-file-earmark-text me-1"></i>
                                            LHP:
                                            <span class="fw-medium text-light">
                                                {{ $item->lhp->nomor_lhp ?? '-' }}
                                            </span><br>
                                            <i class="bi bi-info-circle me-1"></i>
                                            Status:
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($item->approval_status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <div class="fw-semibold small text-white-50">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            {{ $item->deadline->format('d M Y') }}
                                        </div>
                                        <span class="badge bg-{{ $badgeColor }} mt-1 px-3 py-2">
                                            {{ $isOverdue ? 'Terlambat ' . abs($item->sisa_hari) . ' hari' : $item->sisa_hari . ' hari tersisa' }}
                                        </span>
                                        <div class="mt-3">
                                            <a href="{{ route('tindak_lanjut_temuan.show', $item->id) }}"
                                                class="btn btn-sm btn-outline-info rounded-pill px-3">
                                                <i class="bi bi-eye me-1"></i> Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
