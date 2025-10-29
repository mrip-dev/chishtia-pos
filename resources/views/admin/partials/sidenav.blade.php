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
    z-index: 1001;
}
.sidebar.expanded {
    width: 200px;
}

/* === BACKGROUND === */
.rgbg {
    background: linear-gradient(135deg, #fdf8e1 0%, #e8d8b1 100%);
    background-color: #fdf8e1;
}

/* === INNER STRUCTURE === */
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
}

/* === MENU ITEMS === */
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

/* === TOOLTIP FOR COLLAPSED SIDEBAR === */
.sidebar:not(.expanded) .sidebar__menu li a {
    position: relative;
}

.sidebar:not(.expanded) .sidebar__menu li a[title]:hover::after {
    content: attr(title);
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    background: #333;
    color: #fff;
    white-space: nowrap;
    padding: 4px 8px;
    border-radius: 4px;
    margin-left: 10px;
    font-size: 12px;
    z-index: 2000;
    opacity: 0;
    animation: fadeInTooltip 0.2s forwards;
}

/* Fade in tooltip */
@keyframes fadeInTooltip {
    to { opacity: 1; }
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
    z-index: 1100;
    font-size: 20px;
}
.sidebar-collapse-toggle {
    left: 90px;
    transition: left 0.3s ease;
}
.sidebar.expanded ~ .sidebar-collapse-toggle {
    left: 210px;
}

/* === OVERLAY === */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease;
    z-index: 1000;
}
.sidebar-overlay.active {
    opacity: 1;
    visibility: visible;
}

/* === NAVBAR & BODY SHIFT === */
.navbar-wrapper {
    position: relative;
    background: #fff;
    padding: 15px 30px;
    margin-left: 60px;
    border-bottom: 1px solid #dee4ec;
    transition: all 0.3s cubic-bezier(0.4, -0.25, 0.25, 1.1);
}
.navbar-wrapper.ip {
    margin-left: 200px !important;
}
.body-wrapper {
    margin-left: 60px;
    transition: all 0.3s cubic-bezier(0.4, -0.25, 0.25, 1.1);
}
.body-wrapper.ip {
    margin-left: 200px !important;
}

/* === MOBILE RESPONSIVE === */
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
    .navbar-wrapper,
    .body-wrapper {
        margin-left: 0 !important;
    }
    body.menu-open {
        overflow: hidden;
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

<!-- === OVERLAY === -->
<div class="sidebar-overlay"></div>

<!-- === COLLAPSE BUTTON === -->
<button class="sidebar-collapse-toggle"><i class="las la-angle-double-right"></i></button>

@push('script')
<script>
const sidebar = document.getElementById('adminSidebar');
const overlay = document.querySelector('.sidebar-overlay');
const navbar = document.querySelector('.navbar-wrapper');
const bodyWrapper = document.querySelector('.body-wrapper');
const collapseBtn = document.querySelector('.sidebar-collapse-toggle');
const mobileMenuBtn = document.querySelector('.mobile-menu-toggle');
const closeBtn = document.querySelector('.res-sidebar-close-btn');

// === Desktop toggle ===
collapseBtn.addEventListener('click', () => {
    sidebar.classList.toggle('expanded');
    const icon = collapseBtn.querySelector('i');
    icon.classList.toggle('la-angle-double-left');
    icon.classList.toggle('la-angle-double-right');
    navbar.classList.toggle('ip', sidebar.classList.contains('expanded'));
    bodyWrapper.classList.toggle('ip', sidebar.classList.contains('expanded'));
});
collapseBtn.click();

// === Mobile open ===
mobileMenuBtn.addEventListener('click', () => {
    sidebar.classList.add('expanded');
    overlay.classList.add('active');
    document.body.classList.add('menu-open');
});

// === Mobile close ===
closeBtn.addEventListener('click', closeMobileMenu);
overlay.addEventListener('click', closeMobileMenu);

function closeMobileMenu() {
    sidebar.classList.remove('expanded');
    overlay.classList.remove('active');
    document.body.classList.remove('menu-open');
}

// === Add tooltips dynamically ===
const sidebarLinks = document.querySelectorAll('.sidebar__menu li a');
sidebarLinks.forEach(link => {
    const titleText = link.querySelector('.menu-title')?.innerText.trim();
    if (titleText) {
        link.setAttribute('title', titleText);
    }
});
</script>
@endpush
