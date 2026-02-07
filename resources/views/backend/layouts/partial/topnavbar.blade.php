@php
$user = auth()->user();
@endphp

<nav
class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
id="layout-navbar">

<!-- Left side: menu toggle (mobile) -->
<div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="ti ti-menu-2 ti-md"></i>
    </a>
</div>

<!-- Main navbar content -->
<div class="d-flex align-items-center flex-grow-1" id="navbar-collapse">

    {{-- ======= Marquee Fixed Width Area ======= --}}
    <div class="marque-wrapper">
        <div class="marque-text-soft">
            <a href="#">
            <span class="mq-label-soft">NOTICE</span>
              <span class="mq-content-soft">
              DHAKA MEDICAL COLLEGE – &amp; ORTHOPAEDICS DEPARTMENT
            </span>
            </a>
        </div>
    </div>

    {{-- ======= Right Side: Style Switcher + User Dropdown ======= --}}
    <ul class="navbar-nav flex-row align-items-center ms-auto">

        <!-- Style Switcher -->
        <li class="nav-item dropdown-style-switcher dropdown">
            <a
            class="nav-link btn btn-text-secondary btn-icon rounded-pill dropdown-toggle hide-arrow"
            href="javascript:void(0);"
            data-bs-toggle="dropdown">
            <i class="ti ti-md"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
            <li>
                <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                    <span class="align-middle">
                        <i class="ti ti-sun ti-md me-3"></i>Light
                    </span>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                    <span class="align-middle">
                        <i class="ti ti-moon-stars ti-md me-3"></i>Dark
                    </span>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                    <span class="align-middle">
                        <i class="ti ti-device-desktop-analytics ti-md me-3"></i>System
                    </span>
                </a>
            </li>
        </ul>
    </li>
    <!-- / Style Switcher-->

    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a
        class="nav-link dropdown-toggle hide-arrow p-0"
        href="javascript:void(0);"
        data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
            <img src="{{ asset("storage/user/$user->user_image") }}" alt class="rounded-circle" />
        </div>
    </a>

    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item mt-0" href="#">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-2">
                        <div class="avatar avatar-online">
                            <img src="{{ asset("storage/user/$user->user_image") }}" alt class="rounded-circle" />
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $user->name }}</h6>
                        <small class="text-muted">
                            {{ $user->roles->pluck('name')->implode(', ') }}
                        </small>
                    </div>
                </div>
            </a>
        </li>

        <li>
            <div class="dropdown-divider my-1 mx-n2"></div>
        </li>

        <li>
            <a class="dropdown-item" href="{{ route('profile') }}">
                <i class="ti ti-user me-3 ti-md"></i>
                <span class="align-middle">My Profile</span>
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="{{ route('change-password') }}">
                <i class="ti ti-lock me-3 ti-md"></i>
                <span class="align-middle">Change Password</span>
            </a>
        </li>

        <li>
            <div class="d-grid px-2 pt-2 pb-1">
                <a class="btn btn-sm btn-danger d-flex"
                href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <small class="align-middle">Logout</small>
                <i class="ti ti-logout ms-2 ti-14px"></i>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </a>
        </div>
    </li>
</ul>
</li>
<!--/ User -->

</ul>

</div>

<!-- Search Small Screens -->
<div class="navbar-search-wrapper search-input-wrapper d-none">
    <input
    type="text"
    class="form-control search-input container-xxl border-0"
    placeholder="Search..."
    aria-label="Search..." />
    <i class="ti ti-x search-toggler cursor-pointer"></i>
</div>
</nav>

<style>
    .marque-text-soft {
        background: transparent;  
        padding: 0;
        overflow: hidden;
        position: relative;
    }

    .marque-text-soft a {
        display: inline-block;
        white-space: nowrap;
        padding-left: 100%;
        animation: soft-marquee 22s linear infinite;
        text-decoration: none;
        color: #2b5c2b;
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 0.5px;
        will-change: transform;
    }

    .marque-text-soft a:hover {
        animation-play-state: paused !important;
    }

/* আরও smooth animation */
@keyframes soft-marquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}

.mq-label-soft {
    background: #7367f0;
    color: white;
    padding: 4px 10px;
    border-radius: 4px;
    margin-right: 12px;
    font-weight: 700;
}

.mq-content-soft {
    opacity: 0.9;
}

@keyframes soft-marquee {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-100%); }
}

/* ===== Responsive – ছোট স্ক্রিন behaviour ===== */
@media (max-width: 991.98px) {
    #layout-navbar #navbar-collapse {
        flex-wrap: wrap;
    }

    .marque-wrapper {
        flex: 0 0 100%;
        max-width: 100%;
        order: 3;
        margin-top: 6px;
    }

    .navbar-nav.ms-auto {
        order: 2;
        margin-left: auto;
    }

    .marque-text-soft a {
        font-size: 13px;
    }
}
.marque-wrapper {
    flex: 0 0 91%;
    max-width: 100%;
}

</style>
