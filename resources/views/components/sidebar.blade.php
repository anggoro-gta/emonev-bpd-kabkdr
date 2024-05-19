<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">Stisla</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">St</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="nav-item dropdown {{ isset($data->type_menu) && $data->type_menu === 'dashboard' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-fire"></i><span>Dashboard</span></a>
                <ul class="dropdown-menu">
                    <li class='{{ Request::is('dashboard-general-dashboard') ? 'active' : '' }}'>
                        <a class="nav-link"
                            href="{{ url('dashboard-general-dashboard') }}">General Dashboard</a>
                    </li>
                    <li class="{{ Request::is('dashboard-ecommerce-dashboard') ? 'active' : '' }}">
                        <a class="nav-link"
                            href="{{ url('dashboard-ecommerce-dashboard') }}">Ecommerce Dashboard</a>
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown {{ isset($data->type_menu) && $data->type_menu === 'setting' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-gear"></i> <span>Setting</span></a>
                <ul class="dropdown-menu">

                    @can('setting.user.read')
                    <li class="{{ Request::is('auth-forgot-password') ? 'active' : '' }}">
                        <a href="{{ route('setting.user.index') }}">User</a>
                    </li>
                    @endcan
                    @can('setting.permission.read')
                    <li class="{{ Request::is('auth-login') ? 'active' : '' }}">
                        <a href="{{ route('setting.permission.index') }}">Permission</a>
                    </li>
                    @endcan
                    @can('setting.role.read')
                    <li class="{{ Request::is('auth-login2') ? 'active' : '' }}">
                        <a class="beep beep-sidebar"
                            href="{{ route('setting.role.index') }}">Role</a>
                    </li>
                    @endcan
                </ul>
            </li>
        </ul>
    </aside>
</div>
