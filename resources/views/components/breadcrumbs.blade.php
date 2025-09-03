<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <!-- Title dengan icon -->
    <div class="breadcrumb-title pe-3 d-flex align-items-center">
        {!! $icon !!} <span class="ms-2">{{ $title }}</span>
    </div>

    <div class="ps-3 flex-grow-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0 p-0">
                @foreach ($breadcrumbs as $crumb)
                    @if ($loop->first)
                        @continue
                    @endif

                    @if ($loop->last)
                        <li class="breadcrumb-item active">
                            {{ $crumb->title }}
                        </li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ route($crumb->route ?? 'dashboard') }}" class="h-100 d-flex align-items-center">
                                {{ $crumb->title }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>

    <div class="ms-auto">
        {{ $slot }}
    </div>
</div>
