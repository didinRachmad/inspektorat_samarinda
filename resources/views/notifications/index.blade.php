@extends('layouts.dashboard')

@php
    $page = 'notification';
    $action = 'index';
@endphp

@section('dashboard-content')
    <div class="container py-4">
        <h3>All Notifications</h3>
        <div class="list-group mt-3">
            @forelse($notifications as $notif)
                <a href="{{ route(($notif->data['module'] ?? 'dashboard') . '.show', $notif->data['id'] ?? '#') }}"
                    class="list-group-item list-group-item-action notif-item d-flex justify-content-between align-items-center"
                    data-id="{{ $notif->id }}">

                    <div>
                        {{ $notif->data['message'] }}
                        <small class="text-muted d-block">{{ $notif->created_at->diffForHumans() }}</small>
                    </div>

                    @if (is_null($notif->read_at))
                        <span class="badge bg-primary rounded-pill">Baru</span>
                    @endif
                </a>
            @empty
                <div class="list-group-item text-center text-muted">
                    No notifications found.
                </div>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    </div>
@endsection
