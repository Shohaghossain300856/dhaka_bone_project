<ul class="menu-inner py-1">
  {{-- Header: User roles --}}
  <li class="menu-header small text-uppercase">
    <span class="menu-header-text">
      User Role: {{ implode(', ', auth()->user()->roles->pluck('name')->toArray()) }}
    </span>
  </li>

  {{-- Dashboard --}}
  <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <a href="{{ route('dashboard') }}" class="menu-link">
      <i class="menu-icon tf-icons ti ti-dashboard"></i>
      <div data-i18n="Dashboard">Dashboard</div>
    </a>
  </li>

  @canany(['bone-cases'])

<li class="menu-item {{ request()->routeIs('bone-cases.*') ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-bone"></i>
        <div data-i18n="Bone Cases">Bone Cases</div>
    </a>

    <ul class="menu-sub" style="{{ request()->routeIs('bone-cases.*') ? 'display:block;' : '' }}">

        {{-- Add Bone Case --}}
        <li class="menu-item {{ request()->routeIs('bone-cases.index') ? 'active' : '' }}">
            <a href="{{ route('bone-cases-create.index') }}" class="menu-link">
                <div data-i18n="Add Bone Case">Add Bone </div>
            </a>
        </li>     

        {{-- Add Bone Case --}}
        <li class="menu-item {{ request()->routeIs('bone-details-create.index') ? 'active' : '' }}">
            <a href="{{ route('bone-details-create.index') }}" class="menu-link">
                <div data-i18n="Add Bone Case">Add Bone Details</div>
            </a>
        </li> 

    </ul>
</li>

@endcanany

 @canany(['delivary'])

<li class="menu-item {{ request()->routeIs('delivary.*') ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-bone"></i>
        <div data-i18n="Delivary">Delivary</div>
    </a>

    <ul class="menu-sub" style="{{ request()->routeIs('delivary.*') ? 'display:block;' : '' }}">

        <li class="menu-item {{ request()->routeIs('delivary.index') ? 'active' : '' }}">
            <a href="{{ route('delivary.index') }}" class="menu-link">
                <div data-i18n="Delivary">Delivary</div>
            </a>
        </li>     

  

    </ul>
</li>

@endcanany


  {{-- Setting --}}

  @canany(['setting-index','setting-create','setting-edit','setting-delete'])
    @php
      $settingOpen = request()->is('backend/setting/*') || request()->routeIs('setting.*');
    @endphp
    <li class="menu-item {{ $settingOpen ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-settings"></i>
        <div data-i18n="Basic Setting">System Settings</div>
      </a>

      <ul class="menu-sub" style="{{ $settingOpen ? 'display:block;' : '' }}">
        <li class="menu-item {{ request()->routeIs('setting.index') && request()->segment(3) == 'web_setting' ? 'active' : '' }}">
          <a href="{{ route('setting.index', 'web_setting') }}" class="menu-link">
            <i class="ti ti-world"></i>
            <div data-i18n="Web Setting">Web Setting</div>
          </a>
        </li>

        <li class="menu-item {{ request()->routeIs('setting.index') && request()->segment(3) == 'logo_setting' ? 'active' : '' }}">
          <a href="{{ route('setting.index', 'logo_setting') }}" class="menu-link">
            <i class="ti ti-photo"></i>
            <div data-i18n="Logo Setting">Logo Setting</div>
          </a>
        </li>
      </ul>
    </li>
  @endcanany

  {{-- User Management --}}
  @canany(['user-index','user-create','user-edit','user-delete'])
    @php
      // যে কোন user/role/permission রুটে গেলে parent menu open থাকবে
      $userOpen = request()->routeIs('user.*') || request()->routeIs('role.*') || request()->routeIs('permission.*');
    @endphp

    <li class="menu-item {{ $userOpen ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-users"></i>
        <div data-i18n="User">User Settings</div>
      </a>

      <ul class="menu-sub" style="{{ $userOpen ? 'display:block;' : '' }}">
        @can('user-create')
          <li class="menu-item {{ request()->routeIs('user.create') ? 'active' : '' }}">
            <a href="{{ route('user.create') }}" class="menu-link">
              <i class="ti ti-user-plus"></i>
              <div data-i18n="Add New">Add New</div>
            </a>
          </li>
        @endcan

        @canany(['user-index','user-edit','user-delete'])
          <li class="menu-item {{ request()->routeIs('user.index') ? 'active' : '' }}">
            <a href="{{ route('user.index') }}" class="menu-link">
              <i class="ti ti-address-book"></i>
              <div data-i18n="User List">User List</div>
            </a>
          </li>
        @endcanany

        @canany(['role-index','role-create','role-edit','role-delete'])
          <li class="menu-item {{ request()->routeIs('role.index') ? 'active' : '' }}">
            <a href="{{ route('role.index') }}" class="menu-link">
              <i class="ti ti-id-badge"></i>
              <div data-i18n="Role">Role</div>
            </a>
          </li>
        @endcanany

        @canany(['permission-index','permission-create','permission-edit','permission-delete'])
          <li class="menu-item {{ request()->routeIs('permission.index') ? 'active' : '' }}">
            <a href="{{ route('permission.index') }}" class="menu-link">
              <i class="ti ti-key"></i>
              <div data-i18n="Permission">Permission</div>
            </a>
          </li>
        @endcanany
      </ul>
    </li>
  @endcanany

<!--   {{-- Profile --}}
  <li class="menu-item {{ request()->routeIs('profile') ? 'active' : '' }}">
    <a href="{{ route('profile') }}" class="menu-link">
      <i class="menu-icon tf-icons ti ti-user"></i>
      <div data-i18n="Profile">Profile</div>
    </a>
  </li>

  {{-- Change Password --}}
  <li class="menu-item {{ request()->routeIs('change-password') ? 'active' : '' }}">
    <a href="{{ route('change-password') }}" class="menu-link">
      <i class="menu-icon tf-icons ti ti-lock-check"></i>
      <div data-i18n="Change Password">Change Password</div>
    </a>
  </li> -->
<!-- 
  {{-- Logout --}}
  <li class="menu-item">
    <div class="d-grid px-2 pt-2 pb-1">
      <a
        class="btn btn-sm btn-danger d-flex"
        href="{{ route('logout') }}"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
      >
        <small class="align-middle">Logout</small>
        <i class="ti ti-logout ms-2 ti-14px"></i>
      </a>

      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
      </form>
    </div>
  </li> -->
</ul>

<style>
  /* Sub-menu icon styling (only inside dropdown items) */
  .menu-sub .menu-link i {
    margin-left: 8px;
    margin-right: 8px;
    font-size: 0.9rem;
    opacity: 0.8;
  }
  .menu-sub .menu-item.active i {
    opacity: 1;
  }
</style>
