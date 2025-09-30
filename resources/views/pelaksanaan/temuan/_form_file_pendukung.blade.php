<div class="row">
    <div class="col-md-12 mb-3">
        <label class="form-label">File Pendukung</label>
        {{-- Tabel File Pendukung --}}
        <table class="table table-bordered" id="fileTable">
            <thead>
                <tr>
                    <th>File Pendukung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $oldFiles = $temuan->files ?? []; @endphp
                @foreach ($oldFiles as $i => $file)
                    <tr>
                        <td>
                            <a href="{{ asset('storage/' . $file->file_path) }}"
                                target="_blank">{{ $file->file_name }}</a>
                            <input type="hidden" name="old_files[{{ $i }}]" value="{{ $file->id }}">
                        </td>
                        <td class="text-center"><button type="button" class="btn btn-danger remove-file">-</button>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td><input type="file" name="files[]" class="form-control"></td>
                    <td class="text-center"><button type="button" class="btn btn-danger remove-file">-</button></td>
                </tr>
            </tbody>
        </table>
        <div class="text-center mb-3">
            <button type="button" class="btn btn-success" id="btnAddFile">+</button>
        </div>
        <small class="d-block text-center text-muted">Klik + untuk menambah file pendukung</small>
    </div>
</div>
