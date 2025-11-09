<div class="row">
    <div class="col-md-12 mb-3">
        <label class="form-label">Rekomendasi Temuan</label>
        @php
            $oldRekom = old('rekomendasis', $temuan->rekomendasis ?? []);
            $oldFiles = old('files', $temuan->files ?? []);
        @endphp

        {{-- Tabel Rekomendasi --}}
        <table class="table table-bordered" id="rekomendasiTable">
            <thead>
                <tr>
                    <th style="width: 25%;">Kode Rekomendasi</th>
                    <th style="width: 40%;">Rekomendasi Temuan</th>
                    <th style="width: 15%;">Nominal</th>
                    <th style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $oldRekom = old('rekomendasis', $temuan->rekomendasis ?? []); @endphp
                @if ($oldRekom)
                    @foreach ($oldRekom as $i => $r)
                        <tr>
                            <td>
                                <select name="rekomendasis[{{ $i }}][kode_rekomendasi_id]"
                                    class="form-select rekom-select">
                                    <option value="">-- Pilih Kode Rekomendasi --</option>
                                    @foreach ($kodeRekomendasis as $kr)
                                        <option value="{{ $kr->id }}"
                                            {{ ($r['kode_rekomendasi_id'] ?? ($r->kode_rekomendasi_id ?? '')) == $kr->id ? 'selected' : '' }}>
                                            {{ $kr->kode }} | {{ $kr->nama_rekomendasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <textarea name="rekomendasis[{{ $i }}][rekomendasi_temuan]" class="form-control summernote">
                                    {{ $r['rekomendasi_temuan'] ?? ($r->rekomendasi_temuan ?? '') }}
                                </textarea>
                            </td>
                            <td><input type="text" name="rekomendasis[{{ $i }}][nominal]"
                                    class="form-control rekom-nominal numeric" value="{{ $r['nominal'] ?? 0 }}"></td>
                            <td class="text-center"><button type="button"
                                    class="btn btn-danger remove-rekomendasi">-</button></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>
                            <select name="rekomendasis[0][kode_rekomendasi_id]" class="form-select select2">
                                <option value="">-- Pilih Kode Rekomendasi --</option>
                                @foreach ($kodeRekomendasis as $kr)
                                    <option value="{{ $kr->id }}">{{ $kr->kode }} |
                                        {{ $kr->nama_rekomendasi }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <textarea name="rekomendasis[0][rekomendasi_temuan]" class="form-control summernote"></textarea>
                        </td>
                        <td><input type="text" name="rekomendasis[0][nominal]" class="form-control numeric"
                                value="0">
                        </td>
                        <td class="text-center"><button type="button"
                                class="btn btn-danger remove-rekomendasi">-</button></td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="text-center mb-3">
            <button type="button" class="btn btn-success" id="btnAddRekomendasi">+</button>
        </div>
        <small class="d-block text-center text-muted">Klik + untuk menambah rekomendasi</small>
    </div>
</div>
