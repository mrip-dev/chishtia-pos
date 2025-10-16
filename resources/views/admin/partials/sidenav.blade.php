@php
    $sideBarLinks = json_decode($sidenav);
@endphp

<style>
    /* === SIDEBAR BASE === */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 80px; /* collapsed by default */
        height: 100%;
        overflow-y: auto;
        transition: all 0.3s ease;
        z-index: 999;
    }

    .sidebar.expanded {
        width: 260px;
    }

    .sidebar__inner {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* === HIDE TEXT WHEN COLLAPSED === */
    .sidebar:not(.expanded) .menu-title,
    .sidebar:not(.expanded) .sidebar__menu-header {
        display: none;
    }

    /* === CENTER ICONS WHEN COLLAPSED === */
    .sidebar:not(.expanded) .menu-icon {
        display: block;
        text-align: center;
        width: 100%;
        font-size: 20px;
        margin: 0;
    }

    .sidebar__menu li a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        transition: 0.3s;
    }

    .sidebar__menu li a:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    /* === GRADIENT BACKGROUND === */
    .rgbg {
        background: linear-gradient(135deg, #fdf8e1 0%, #e8d8b1 100%);
        background-color: #fdf8e1;
    }

    /* === TOGGLE BUTTONS === */
    .mobile-menu-toggle,
    .sidebar-collapse-toggle {
        position: fixed;
        top: 15px;
        left: 15px;
        background: #f0d98a;
        color: #333;
        border: none;
        padding: 8px 10px;
        border-radius: 8px;
        cursor: pointer;
        z-index: 1000;
        font-size: 20px;
    }

    .sidebar-collapse-toggle {
        left: 90px;
        transition: left 0.3s ease;
    }

    .sidebar.expanded ~ .sidebar-collapse-toggle {
        left: 270px;
    }

    /* === MOBILE STYLES === */
    @media (max-width: 992px) {
        .sidebar {
            left: -260px;
            width: 260px;
        }

        .sidebar.expanded {
            left: 0;
        }

        .mobile-menu-toggle {
            display: block;
        }

        .sidebar-collapse-toggle {
            display: none;
        }
    }

    @media (min-width: 993px) {
        .mobile-menu-toggle {
            display: none;
        }
    }
</style>

<!-- === MOBILE MENU ICON === -->
<button class="mobile-menu-toggle"><i class="las la-bars"></i></button>

<!-- === SIDEBAR === -->
<div class="sidebar rgbg" id="adminSidebar">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div>
            <div class="sidebar__logo py-3 text-center">
                <a class="sidebar__main-logo" href="{{ route('admin.dashboard') }}">
                    <img src="{{ siteLogo('light') }}" alt="logo" style="max-width:40px;">
                </a>
            </div>

            <div class="sidebar__menu-wrapper">
                <ul class="sidebar__menu">
                    @foreach ($sideBarLinks as $key => $data)
                        @php
                            $showHeader = @$data->header && ((!@$data->submenu && permit(@$data->route_name)) || (@$data->submenu && permit(array_column($data->submenu, 'route_name'))));
                        @endphp

                        @if ($showHeader)
                            <li class="sidebar__menu-header">{{ __($data->header) }}</li>
                        @endif

                        @if (@$data->submenu)
                            @permit(array_column($data->submenu, 'route_name'))
                                <li class="sidebar-menu-item sidebar-dropdown">
                                    <a href="javascript:void(0)">
                                        <i class="menu-icon {{ @$data->icon }}"></i>
                                        <span class="menu-title">{{ __(@$data->title) }}</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            @foreach ($data->submenu as $menu)
                                                @permit($menu->route_name)
                                                    <li class="sidebar-menu-item">
                                                        <a href="{{ route(@$menu->route_name) }}">
                                                            <i class="menu-icon las la-dot-circle"></i>
                                                            <span class="menu-title">{{ __($menu->title) }}</span>
                                                        </a>
                                                    </li>
                                                @endpermit
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @endpermit
                        @else
                            @permit(@$data->route_name)
                                <li class="sidebar-menu-item">
                                    <a href="{{ route(@$data->route_name) }}">
                                        <i class="menu-icon {{ $data->icon }}"></i>
                                        <span class="menu-title">{{ __(@$data->title) }}</span>
                                    </a>
                                </li>
                            @endpermit
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- === COLLAPSE BUTTON === -->
<button class="sidebar-collapse-toggle"><i class="las la-angle-double-right"></i></button>

@push('script')
<script>
    const sidebar = document.getElementById('adminSidebar');

    // --- Desktop toggle (collapsed/expanded) ---
    document.querySelector('.sidebar-collapse-toggle').addEventListener('click', function () {
        sidebar.classList.toggle('expanded');
        const icon = this.querySelector('i');
        icon.classList.toggle('la-angle-double-left');
        icon.classList.toggle('la-angle-double-right');
    });

    // --- Mobile open ---
    document.querySelector('.mobile-menu-toggle').addEventListener('click', function () {
        sidebar.classList.add('expanded');
    });

    // --- Mobile close ---
    document.querySelector('.res-sidebar-close-btn').addEventListener('click', function () {
        sidebar.classList.remove('expanded');
    });
</script>
@endpush
