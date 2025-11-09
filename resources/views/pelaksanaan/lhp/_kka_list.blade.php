@if ($kkas->isEmpty())
    <p class="text-muted">Belum ada KKA.</p>
@else
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 20%">Judul</th>
                    <th style="width: 25%">Uraian Prosedur</th>
                    <th style="width: 25%">Hasil</th>
                    {{-- <th style="width: 15%">Auditor</th> --}}
                    <th style="width: 15%">File</th>
                    <th style="width: 10%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kkas as $kka)
                    <tr>
                        <td>{{ $kka->judul }}</td>
                        <td>{{ $kka->uraian_prosedur }}</td>
                        <td>{!! nl2br(e($kka->hasil)) !!}</td>
                        {{-- <td>{{ $kka->auditor?->name ?? '-' }}</td> --}}
                        <td class="text-center">
                            @if ($kka->file_kka)
                                <a href="{{ asset('storage/' . $kka->file_kka) }}" target="_blank"
                                    class="btn btn-sm btn-outline-info rounded-4">Download</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('kka.destroy', $kka->id) }}" method="POST"
                                class="d-inline form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger rounded-4 btn-delete"
                                    title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
