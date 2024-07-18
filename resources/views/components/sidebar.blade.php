<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">emonev</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">ev</a>
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
            @canany(['setting.user.read', 'setting.permission.read', 'setting.role.read'])
            <li class="nav-item dropdown {{ isset($data->type_menu) && $data->type_menu === 'setting' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-gear"></i> <span>Setting</span></a>
                <ul class="dropdown-menu">

                    @can('setting.user.read')
                    <li class="{{ Request::is('setting/user') ? 'active' : '' }}">
                        <a href="{{ route('setting.user.index') }}">User</a>
                    </li>
                    @endcan
                    @can('setting.permission.read')
                    <li class="{{ Request::is('setting/permission') ? 'active' : '' }}">
                        <a href="{{ route('setting.permission.index') }}">Permission</a>
                    </li>
                    @endcan
                    @can('setting.role.read')
                    <li class="{{ Request::is('setting/role') ? 'active' : '' }}">
                        <a href="{{ route('setting.role.index') }}">Role</a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany

            <li class="nav-item dropdown {{ isset($data->type_menu) && $data->type_menu === 'master' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-gear"></i> <span>Master</span></a>
                <ul class="dropdown-menu">

                    {{-- @can('master.urusan.read') --}}
                    @if (Auth::user()->hasRole('Admin'))
                        <li class="{{ Request::is('master/skpd') ? 'active' : '' }}">
                            <a href="{{ route('master.skpd.index') }}">OPD</a>
                        </li>
                        <li class="{{ Request::is('master/skpd_unit') ? 'active' : '' }}">
                            <a href="{{ route('master.skpd_unit.index') }}">OPD Unit</a>
                        </li>
                        <li class="{{ Request::is('master/urusan') ? 'active' : '' }}">
                            <a href="{{ route('master.urusan.index') }}">Urusan</a>
                        </li>
                        <li class="{{ Request::is('master/bidang_urusan') ? 'active' : '' }}">
                            <a href="{{ route('master.bidang_urusan.index') }}">Bidang Urusan</a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRole(['Admin','OPD']))
                        <li class="{{ Request::is('master/program') ? 'active' : '' }}">
                            <a href="{{ route('master.program.index') }}">Program</a>
                        </li>
                        <li class="{{ Request::is('master/kegiatan') ? 'active' : '' }}">
                            <a href="{{ route('master.kegiatan.index') }}">Kegiatan</a>
                        </li>
                        <li class="{{ Request::is('master/sub_kegiatan') ? 'active' : '' }}">
                            <a href="{{ route('master.sub_kegiatan.index') }}">Sub Kegiatan</a>
                        </li>
                    @endcan
                    {{-- @endcan --}}
                </ul>
            </li>

            <li class="nav-item dropdown {{ isset($data->type_menu) && $data->type_menu === 'entri' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-gear"></i> <span>Entri Data</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('master/sub_kegiatan') ? 'active' : '' }}">
                        <a href="#">Input OPD</a>
                    </li>
                </ul>
            </li>


            <li class="nav-item dropdown {{ isset($data->type_menu) && $data->type_menu === 'laporan' ? 'active' : '' }}">
                <a href="#"
                    class="nav-link has-dropdown"><i class="fas fa-gear"></i> <span>Laporan</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('master/sub_kegiatan') ? 'active' : '' }}">
                        <a href="#">Monitoring OPD</a>
                    </li>
                    <li class="{{ Request::is('master/sub_kegiatan') ? 'active' : '' }}">
                        <a href="#">Laporan Triwulan</a>
                    </li>
                </ul>
            </li>
        </ul>
    </aside>
</div>
