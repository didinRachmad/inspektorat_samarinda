@extends('layouts.dashboard')

@section('dashboard-content')
    {{-- <x-breadcrumbs></x-breadcrumbs> --}}
    <div class="container py-4">
        <div class="card rounded-4 w-100 m-0">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="material-icons-outlined me-2">search</i>
                    Hasil Pencarian: "{{ $query }}"
                </h4>
            </div>

            <div class="card-body">
                @if (isset($error))
                    <div class="alert alert-danger">
                        {{ $error }}
                    </div>
                @endif

                @forelse($results as $type => $result)
                    <div class="mb-4">
                        <h5 class="d-flex align-items-center gap-2">
                            <i class="material-icons-outlined">{{ $result['icon'] != '' ? $result['icon'] : 'search' }}</i>
                            {{ $result['title'] }}
                        </h5>

                        <div class="list-group">
                            @forelse($result['items'] as $item)
                                <a href="{{ $item['url'] }}" class="list-group-item list-group-item-action">
                                    {{ $item['display'] }}
                                </a>
                            @empty
                                <div class="text-muted p-3">Tidak ditemukan hasil</div>
                            @endforelse
                        </div>

                        {{ $result['items']->links() }} <!-- Pagination -->
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="material-icons-outlined display-4 text-muted">search_off</i>
                        <h4 class="mt-3">Tidak ada hasil pencarian</h4>
                        <p class="text-muted">Coba dengan kata kunci lain</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
