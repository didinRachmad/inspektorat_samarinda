<div class="row">
    {{-- Kode Rekomendasi --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Kode Rekomendasi" name="kode" :value="$kode_rekomendasi->kode ?? old('kode')" required />
        </div>
    </div>

    {{-- Nama Rekomendasi --}}
    <div class="col-md-8">
        <div class="form-group mb-3">
            <x-form.input label="Nama Rekomendasi" name="nama_rekomendasi" :value="$kode_rekomendasi->nama_rekomendasi ?? old('nama_rekomendasi')" required />
        </div>
    </div>

    {{-- Urutan --}}
    <div class="col-md-4">
        <div class="form-group mb-3">
            <x-form.input label="Urutan" name="urutan" type="number" :value="$kode_rekomendasi->urutan ?? old('urutan', 0)" required min="0" />
        </div>
    </div>

    {{-- Multi-select Temuan --}}
    <div class="col-md-8">
        <div class="form-group mb-3">
            <label for="temuan_ids" class="form-label">Temuan Terkait</label>
            <select id="temuan_ids" name="temuan_ids[]" class="form-select select2" multiple required>
                @foreach ($temuans as $id => $temuan)
                    <option value="{{ $id }}" @selected(in_array($id, old('temuan_ids', $selectedTemuans ?? [])))>
                        {{ $temuan['kode'] }} - {{ $temuan['nama_temuan'] }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Pilih satu atau lebih temuan yang terkait dengan rekomendasi ini.</small>
        </div>
    </div>
</div>
