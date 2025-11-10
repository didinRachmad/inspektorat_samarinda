@extends('layouts.dashboard')

@php
    $page = 'faq';
    $action = 'index';
@endphp

@section('dashboard-content')
    <x-breadcrumbs>
        @if (Auth::user()->hasMenuPermission($activeMenu->id, 'create'))
            <button id="btnTambahFaq" class="btn btn-sm rounded-4 btn-primary shadow-sm">
                <i class="bi bi-plus-circle-fill"></i> Tambah FAQ
            </button>
        @endif
    </x-breadcrumbs>

    <div class="row">
        <div class="col-12 d-flex">
            <div class="card rounded-4 w-100 m-0 shadow-sm">
                <div class="card-body">
                    <div class="container my-5">
                        <h1 class="mb-4 text-center">Frequently Asked Questions</h1>
                        <p class="text-center text-muted mb-5">
                            Temukan jawaban atas pertanyaan umum seputar layanan dan fitur kami.
                        </p>

                        @if ($faqs->isNotEmpty())
                            <div class="accordion" id="faqAccordion">
                                @foreach ($faqs as $faq)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                            <button
                                                class="accordion-button @if (!$loop->first) collapsed @endif"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $loop->index }}"
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                                aria-controls="collapse{{ $loop->index }}">
                                                {{ $faq->question }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $loop->index }}"
                                            class="accordion-collapse collapse @if ($loop->first) show @endif"
                                            aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#faqAccordion">
                                            <div
                                                class="accordion-body d-flex justify-content-between align-items-start p-5">
                                                <div>{!! $faq->answer !!}</div> {{-- langsung render HTML Summernote --}}

                                                <div class="d-flex gap-2">
                                                    @if (Auth::user()->hasMenuPermission($activeMenu->id, 'update'))
                                                        <button class="btn btn-sm btn-warning rounded-4 btn-edit-faq"
                                                            data-id="{{ $faq->id }}"
                                                            data-question="{{ $faq->question }}"
                                                            data-answer="{{ $faq->answer }}">
                                                            <i class="bi bi-pencil-square"></i> Edit
                                                        </button>
                                                    @endif

                                                    @if (Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
                                                        <form action="{{ route('faq.destroy', $faq->id) }}" method="POST"
                                                            class="d-inline form-delete">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-danger rounded-4 btn-delete">
                                                                <i class="bi bi-trash-fill"></i> Hapus
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center p-5">
                                <p class="text-muted">Belum ada FAQ yang tersedia saat ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal FAQ --}}
    <div class="modal fade" id="faqModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="faqForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="faq_id" name="faq_id">
                        <div class="mb-3">
                            <label for="question" class="form-label">Pertanyaan</label>
                            <input type="text" class="form-control" id="question" name="question" required>
                        </div>
                        <div class="mb-3">
                            <label for="answer" class="form-label">Jawaban</label>
                            <textarea class="form-control summernote" id="answer" name="answer" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
