<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Daftar Temuan</h5>
</div>

<div id="temuanContainer">
    {{-- Jika ada old data --}}
    @if (old('temuans'))
        @foreach (old('temuans') as $i => $temuan)
            <div class="temuan-item border border-primary rounded p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Temuan #{{ $i + 1 }}</h6>
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-4 remove-temuan">
                        <i class="bi bi-trash"></i> Hapus Temuan
                    </button>
                </div>

                {{-- Input Temuan --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Judul Temuan</label>
                        <input type="text" name="temuans[{{ $i }}][judul_temuan]" class="form-control"
                            value="{{ old("temuans.$i.judul_temuan") }}" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode Temuan</label>
                        <select name="temuans[{{ $i }}][kode_temuan_id]"
                            class="form-select select2 kode-temuan-select" data-rekom='@json($mapping)'>
                            <option value="">-- Pilih Kode Temuan --</option>
                            @foreach ($kodeTemuans as $kt)
                                <option value="{{ $kt->id }}"
                                    {{ old("temuans.$i.kode_temuan_id", $temuan['kode_temuan_id'] ?? '') == $kt->id ? 'selected' : '' }}>
                                    {{ $kt->kode }} | {{ $kt->nama_temuan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kondisi, Kriteria, Sebab, Akibat --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Kondisi Temuan</label>
                        <textarea name="temuans[{{ $i }}][kondisi_temuan]" class="form-control summernote" rows="3">{{ old("temuans.$i.kondisi_temuan") }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Kriteria Temuan</label>
                        <textarea name="temuans[{{ $i }}][kriteria_temuan]" class="form-control summernote" rows="3">{{ old("temuans.$i.kriteria_temuan") }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Sebab Temuan</label>
                        <textarea name="temuans[{{ $i }}][sebab_temuan]" class="form-control summernote" rows="3">{{ old("temuans.$i.sebab_temuan") }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Akibat Temuan</label>
                        <textarea name="temuans[{{ $i }}][akibat_temuan]" class="form-control summernote" rows="3">{{ old("temuans.$i.akibat_temuan") }}</textarea>
                    </div>
                </div>

                <hr class="text-primary">

                {{-- Rekomendasi --}}
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Rekomendasi Temuan</label>
                        <table class="table table-bordered rekomendasiTable">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Kode Rekomendasi</th>
                                    <th style="width: 55%;">Rekomendasi Temuan</th>
                                    <th style="width: 10%;">Nominal</th>
                                    <th style="width: 5%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rekomendasis = old("temuans.$i.rekomendasis", $temuan['rekomendasis'] ?? []);
                                @endphp
                                @forelse ($rekomendasis as $j => $rekom)
                                    <tr>
                                        <td>
                                            <select
                                                name="temuans[{{ $i }}][rekomendasis][{{ $j }}][kode_rekomendasi_id]"
                                                class="form-select select2">
                                                <option value="">-- Pilih Kode Rekomendasi --</option>
                                                @foreach ($kodeRekomendasis as $kr)
                                                    <option value="{{ $kr->id }}"
                                                        {{ ($rekom['kode_rekomendasi_id'] ?? '') == $kr->id ? 'selected' : '' }}>
                                                        {{ $kr->kode }} | {{ $kr->nama_rekomendasi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="temuans[{{ $i }}][rekomendasis][{{ $j }}][rekomendasi_temuan]"
                                                class="form-control summernote">{{ $rekom['rekomendasi_temuan'] ?? '' }}</textarea>
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="temuans[{{ $i }}][rekomendasis][{{ $j }}][nominal]"
                                                class="form-control numeric" value="{{ $rekom['nominal'] ?? 0 }}">
                                        </td>
                                        <td class="text-center align-middle">
                                            <button type="button"
                                                class="btn btn-danger rounded-4 remove-rekomendasi">-</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada rekomendasi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="text-center mb-3">
                            <button type="button" class="btn btn-success rounded-4 btn-sm btnAddRekomendasi">+</button>
                            <small class="d-block text-center text-muted">Klik + untuk menambah rekomendasi</small>
                        </div>
                    </div>
                </div>

                <hr class="text-primary">

                {{-- File Pendukung --}}
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">File Pendukung</label><span class="text-danger">*</span>
                        <table class="table table-bordered filePendukung">
                            <thead>
                                <tr>
                                    <th>File Pendukung</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $oldFiles = old("temuans.$i.files", []);
                                @endphp
                                @forelse ($oldFiles as $j => $file)
                                    <tr>
                                        <td>
                                            <input type="file"
                                                name="temuans[{{ $i }}][files][{{ $j }}]"
                                                class="form-control" />
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-danger rounded-4 remove-file">-</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td><input type="file" name="temuans[{{ $i }}][files][0]"
                                                class="form-control" /></td>
                                        <td class="text-center"><button type="button"
                                                class="btn btn-danger rounded-4 remove-file">-</button></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="text-center mb-3">
                            <button type="button" class="btn btn-success rounded-4 btn-sm btnAddFile">+</button>
                            <small class="d-block text-center text-muted">Klik + untuk menambah file pendukung</small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        {{-- Jika edit dari database --}}
    @elseif(isset($lhp) && $lhp->temuans->count())
        @foreach ($lhp->temuans as $i => $temuan)
            <div class="temuan-item border border-primary rounded p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Temuan #{{ $i + 1 }}</h6>
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-4 remove-temuan">
                        <i class="bi bi-trash"></i> Hapus Temuan
                    </button>
                </div>

                <input type="hidden" name="temuans[{{ $i }}][id]" value="{{ $temuan->id }}">

                {{-- Input Temuan --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Judul Temuan</label>
                        <input type="text" name="temuans[{{ $i }}][judul_temuan]" class="form-control"
                            value="{{ old("temuans.$i.judul_temuan", $temuan->judul_temuan) }}" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode Temuan</label>
                        <select name="temuans[{{ $i }}][kode_temuan_id]"
                            class="form-select select2 kode-temuan-select" data-rekom='@json($mapping)'>
                            <option value="">-- Pilih Kode Temuan --</option>
                            @foreach ($kodeTemuans as $kt)
                                <option value="{{ $kt->id }}"
                                    {{ old("temuans.$i.kode_temuan_id", $temuan->kode_temuan_id ?? '') == $kt->id ? 'selected' : '' }}>
                                    {{ $kt->kode }} | {{ $kt->nama_temuan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kondisi, Kriteria, Sebab, Akibat --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Kondisi Temuan</label>
                        <textarea name="temuans[{{ $i }}][kondisi_temuan]" class="form-control summernote" rows="3">{{ old("temuans.$i.kondisi_temuan", $temuan->kondisi_temuan) }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Kriteria Temuan</label>
                        <textarea name="temuans[{{ $i }}][kriteria_temuan]" class="form-control summernote" rows="3">{{ old("temuans.$i.kriteria_temuan", $temuan->kriteria_temuan) }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Sebab Temuan</label>
                        <textarea name="temuans[{{ $i }}][sebab_temuan]" class="form-control summernote" rows="3">{{ old("temuans.$i.sebab_temuan", $temuan->sebab_temuan) }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Akibat Temuan</label>
                        <textarea name="temuans[{{ $i }}][akibat_temuan]" class="form-control summernote" rows="3">{{ old("temuans.$i.akibat_temuan", $temuan->akibat_temuan) }}</textarea>
                    </div>
                </div>

                <hr class="text-primary">

                {{-- Rekomendasi --}}
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Rekomendasi Temuan</label>
                        <table class="table table-bordered rekomendasiTable">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Kode Rekomendasi</th>
                                    <th style="width: 55%;">Rekomendasi Temuan</th>
                                    <th style="width: 10%;">Nominal</th>
                                    <th style="width: 5%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (old("temuans.$i.rekomendasis", $temuan->rekomendasis->toArray()) as $j => $rekom)
                                    <tr>
                                        <input type="hidden"
                                            name="temuans[{{ $i }}][rekomendasis][{{ $j }}][id]"
                                            value="{{ $rekom['id'] ?? '' }}">
                                        <td>
                                            <select
                                                name="temuans[{{ $i }}][rekomendasis][{{ $j }}][kode_rekomendasi_id]"
                                                class="form-select select2">
                                                <option value="">-- Pilih Kode Rekomendasi --</option>
                                                @foreach ($kodeRekomendasis as $kr)
                                                    <option value="{{ $kr->id }}"
                                                        {{ ($rekom['kode_rekomendasi_id'] ?? '') == $kr->id ? 'selected' : '' }}>
                                                        {{ $kr->kode }} | {{ $kr->nama_rekomendasi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <textarea name="temuans[{{ $i }}][rekomendasis][{{ $j }}][rekomendasi_temuan]"
                                                class="form-control summernote">{{ $rekom['rekomendasi_temuan'] ?? '' }}</textarea>
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="temuans[{{ $i }}][rekomendasis][{{ $j }}][nominal]"
                                                class="form-control numeric" value="{{ $rekom['nominal'] ?? 0 }}">
                                        </td>
                                        <td class="text-center align-middle">
                                            <button type="button"
                                                class="btn btn-danger rounded-4 remove-rekomendasi">-</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-center mb-3">
                            <button type="button"
                                class="btn btn-success btn-sm rounded-4 btnAddRekomendasi">+</button>
                            <small class="d-block text-center text-muted">Klik + untuk menambah rekomendasi</small>
                        </div>
                    </div>
                </div>

                <hr class="text-primary">

                {{-- File Pendukung --}}
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">File Pendukung</label><span class="text-danger">*</span>
                        <table class="table table-bordered filePendukung">
                            <thead>
                                <tr>
                                    <th>File Pendukung</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($temuan->files ?? [] as $j => $file)
                                    <tr>
                                        <td>
                                            <a href="{{ asset('storage/' . $file['file_path']) }}"
                                                target="_blank">{{ $file['file_name'] ?? 'File' }}</a>
                                            <input type="hidden"
                                                name="temuans[{{ $i }}][old_files][{{ $j }}]"
                                                value="{{ $file['id'] }}">
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-danger rounded-4 remove-file">-</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td><input type="file" name="files[{{ $i }}][]"
                                                class="form-control" /></td>
                                        <td class="text-center"><button type="button"
                                                class="btn btn-danger rounded-4 remove-file">-</button></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="text-center mb-3">
                            <button type="button" class="btn btn-success btn-sm rounded-4 btnAddFile">+</button>
                        </div>
                        <small class="d-block text-center text-muted">Klik + untuk menambah file pendukung</small>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<div class="d-flex justify-content-center align-items-center mb-4">
    <button type="button" class="btn btn-primary rounded-4 btn-sm" id="btnAddTemuan">
        <i class="bi bi-plus-circle me-1"></i> Tambah Temuan
    </button>
</div>

<!-- Templates -->
<template id="temuanTemplate">
    <div class="temuan-item border border-primary rounded p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Temuan #[index]</h6>
            <button type="button" class="btn btn-outline-danger btn-sm rounded-4 remove-temuan">
                <i class="bi bi-trash"></i> Hapus Temuan
            </button>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Judul Temuan</label>
                <input type="text" name="temuans[[i]][judul_temuan]" class="form-control" />
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Kode Temuan</label>
                <select name="temuans[[i]][kode_temuan_id]" class="form-select select2 kode-temuan-select"
                    data-rekom='@json($mapping)'>
                    <option value="">-- Pilih Kode Temuan --</option>
                    @foreach ($kodeTemuans as $kt)
                        <option value="{{ $kt->id }}">{{ $kt->kode }} | {{ $kt->nama_temuan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label">Kondisi Temuan</label>
                <textarea name="temuans[[i]][kondisi_temuan]" class="form-control summernote" rows="3"></textarea>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Kriteria Temuan</label>
                <textarea name="temuans[[i]][kriteria_temuan]" class="form-control summernote" rows="3"></textarea>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Sebab Temuan</label>
                <textarea name="temuans[[i]][sebab_temuan]" class="form-control summernote" rows="3"></textarea>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Akibat Temuan</label>
                <textarea name="temuans[[i]][akibat_temuan]" class="form-control summernote" rows="3"></textarea>
            </div>
        </div>

        <hr class="text-primary">

        <!-- Rekomendasi -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Rekomendasi Temuan</label>
                <table class="table table-bordered rekomendasiTable">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Kode Rekomendasi</th>
                            <th style="width: 55%;">Rekomendasi Temuan</th>
                            <th style="width: 10%;">Nominal</th>
                            <th style="width: 5%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="temuans[[i]][rekomendasis][0][kode_rekomendasi_id]"
                                    class="form-select select2">
                                    <option value="">-- Pilih Kode Rekomendasi --</option>
                                    @foreach ($kodeRekomendasis as $kr)
                                        <option value="{{ $kr->id }}">{{ $kr->kode }} |
                                            {{ $kr->nama_rekomendasi }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <textarea name="temuans[[i]][rekomendasis][0][rekomendasi_temuan]" class="form-control summernote"></textarea>
                            </td>
                            <td>
                                <input type="text" name="temuans[[i]][rekomendasis][0][nominal]"
                                    class="form-control numeric" value="0" />
                            </td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-danger rounded-4 remove-rekomendasi">-</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-success btn-sm rounded-4 btnAddRekomendasi">+</button>
                </div>
            </div>
        </div>

        <hr class="text-primary">

        <!-- File Pendukung -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">File Pendukung</label><span class="text-danger">*</span>
                <table class="table table-bordered filePendukung">
                    <thead>
                        <tr>
                            <th>File Pendukung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="file" name="temuans[[i]][files][0]" class="form-control"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger rounded-4 remove-file">-</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-success btn-sm rounded-4 btnAddFile">+</button>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="rekomendasiTemplate">
    <tr>
        <td>
            <select name="temuans[[i]][rekomendasis][[j]][kode_rekomendasi_id]" class="form-select select2">
                <option value="">-- Pilih Kode Rekomendasi --</option>
                @foreach ($kodeRekomendasis as $kr)
                    <option value="{{ $kr->id }}">{{ $kr->kode }} | {{ $kr->nama_rekomendasi }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <textarea name="temuans[[i]][rekomendasis][[j]][rekomendasi_temuan]" class="form-control summernote"></textarea>
        </td>
        <td>
            <input type="text" name="temuans[[i]][rekomendasis][[j]][nominal]" class="form-control numeric"
                value="0" />
        </td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-danger rounded-4 remove-rekomendasi">-</button>
        </td>
    </tr>
</template>

<template id="fileTemplate">
    <tr>
        <td><input type="file" name="temuans[[i]][files][[j]]" class="form-control" /></td>
        <td class="text-center">
            <button type="button" class="btn btn-danger rounded-4 remove-file">-</button>
        </td>
    </tr>
</template>
