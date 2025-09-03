<!--start sidebar-->
<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div class="d-flex align-items-center">
            <div class="logo-icon me-1">
                <img src="{{ asset('images/logo.png') }}" class="logo-img" alt="" style="width: 50px;" />
            </div>
            <div class="logo-name">
                <h5 class="mb-0">Admin</h5>
            </div>
        </div>
        <div class="sidebar-close">
            <span class="material-icons-outlined">close</span>
        </div>
    </div>

    <div class="sidebar-nav">
        <ul class="metismenu" id="sidenav">
            <li>
                <a href="{{ route('dashboard') }}">
                    <div class="parent-icon">
                        <i class="material-icons-outlined">home</i>
                    </div>
                    <div class="menu-title">Dashboard</div>
                </a>
            </li>

            @foreach ($menuTree as $menu)
                @if ($menu->children->isEmpty() && $menu->route)
                    <li>
                        <a href="{{ route($menu->route . '.index') }}" class="">
                            <div class="parent-icon">
                                <i class="material-icons-outlined">{{ $menu->icon ?? 'label_important' }}</i>
                            </div>
                            <div class="menu-title">{{ __($menu->title) }}</div>
                        </a>
                    </li>
                @else
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon">
                                <i class="material-icons-outlined">{{ $menu->icon ?? '' }}</i>
                            </div>
                            <div class="menu-title">{{ __($menu->title) }}</div>
                        </a>
                        <ul>
                            @foreach ($menu->children as $child)
                                @if ($child->children->isNotEmpty())
                                    <li>
                                        <a href="javascript:;">
                                            <i
                                                class="material-icons-outlined">{{ $child->icon ?? 'label_important' }}</i>
                                            {{ __($child->title) }}
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ route($child->route . '.index') }}" class="">
                                            <i class="material-icons-outlined">{{ $child->icon ?? '' }}</i>
                                            {{ __($child->title) }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</aside>
<!--end sidebar-->
