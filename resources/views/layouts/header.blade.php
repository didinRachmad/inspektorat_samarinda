<!--start header-->
<header class="top-header">
    <nav class="navbar navbar-expand align-items-center gap-4">
        <div class="btn-toggle">
            <a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
        </div>

        <div class="search-bar flex-grow-1">
            <div class="position-relative">
                <input id="search-input" class="form-control rounded-5 px-5 search-control d-lg-block d-none"
                    type="text" placeholder="Search" autocomplete="off" />
                <span
                    class="material-icons-outlined position-absolute d-lg-block d-none ms-3 translate-middle-y start-0 top-50">search</span>
                <span
                    class="material-icons-outlined position-absolute me-3 translate-middle-y end-0 top-50 search-close">close</span>

                <div class="search-popup p-3 d-none">
                    <div class="card rounded-4 overflow-hidden">
                        <div class="card-header d-lg-none">
                            <div class="position-relative">
                                <input id="mobile-search-input"
                                    class="form-control rounded-5 px-5 mobile-search-control" type="text"
                                    placeholder="Search" autocomplete="off" />
                                <span
                                    class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50">search</span>
                                <span
                                    class="material-icons-outlined position-absolute me-3 translate-middle-y end-0 top-50 mobile-search-close">close</span>
                            </div>
                        </div>
                        <div class="card-body search-content" data-simplebar>
                            <!-- Hasil pencarian akan diisi oleh JavaScript -->
                            <div id="search-results"></div>
                        </div>
                        <div class="card-footer text-center bg-transparent">
                            <a href="#" id="see-all-results" class="btn w-100">See All Search Results</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ul class="navbar-nav gap-1 nav-right-links align-items-center">

            <!-- Notifikasi -->
            <li class="nav-item dropdown">
                <a href="javascript:;" class="dropdown-toggle dropdown-toggle-nocaret position-relative"
                    data-bs-toggle="dropdown" id="notifDropdown">
                    <i
                        class="material-icons-outlined fs-4 rounded-circle p-1 bg-warning text-white border">notifications</i>
                    @php
                        $unreadCount = auth()->user()->unreadNotifications()->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            id="notifBadge">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow py-2" style="min-width:300px;">
                    <h6 class="dropdown-header fw-bold text-uppercase">Notifications</h6>
                    <div class="list-group list-group-flush" id="notifList">
                        @forelse(auth()->user()->unreadNotifications()->take(5)->get() as $notif)
                            <a href="{{ route(($notif->data['module'] ?? 'dashboard') . '.show', $notif->data['id'] ?? '#') }}"
                                class="list-group-item list-group-item-action" data-id="{{ $notif->id }}">
                                {{ $notif->data['message'] }}
                                <small class="text-muted d-block">{{ $notif->created_at->diffForHumans() }}</small>
                            </a>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                No new notifications
                            </div>
                        @endforelse
                    </div>
                    <div class="dropdown-footer text-center py-2">
                        <a href="{{ route('notifications.index') }}" class="text-decoration-none">See all
                            notifications</a>
                    </div>
                </div>
            </li>

            <!-- Avatar User -->
            <li class="nav-item dropdown">
                <a href="javascript:;" class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
                    <img src="{{ asset('images/avatars/profil.png') }}" class="rounded-circle p-1 border" width="45"
                        height="45" alt="" />
                </a>
                <div class="dropdown-menu dropdown-user dropdown-menu-end shadow py-3">
                    <div class="gap-2 py-2">
                        <div class="text-center">
                            <img src="{{ asset('images/avatars/profil.png') }}" class="rounded-circle p-1 shadow mb-3"
                                width="90" height="90" alt="" />
                            <h5 class="user-name mb-0 fw-bold">{{ Auth::user()->name }}</h5>
                        </div>
                    </div>
                    <hr class="dropdown-divider m-0" />
                    @if ($hasProfileAccess)
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                            href="{{ route('profile.index') }}">
                            <i class="material-icons-outlined">person_outline</i>Profile
                        </a>
                        <hr class="dropdown-divider m-0" />
                    @endif
                    <hr class="dropdown-divider m-0" />
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('logout') }}">
                        <i class="material-icons-outlined">power_settings_new</i>Logout
                    </a>
                </div>
            </li>
        </ul>
    </nav>
</header>
<!--end top header-->
