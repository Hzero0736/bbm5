<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        @if(Auth::check())
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle mr-2"></i>
                {{ Auth::user()->nama }}
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <div class="dropdown-header">
                    <small>Logged in as:</small><br>
                    <strong>{{ Auth::user()->nama }}</strong>
                    <small class="d-block text-muted">{{ Auth::user()->email }}</small>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('profile.index') }}">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </li>
        @endif
    </ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="" class="brand-link text-center">
        <span class="brand-text font-weight-bold">BBM Claims</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                @if(Auth::user()->roles->contains('nama', 'Admin'))
                <li class="nav-header">ADMIN MENU</li>
                <li class="nav-item">
                    <a href="{{ route('users.pending') }}" class="nav-link {{ Request::routeIs('users.pending') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Persetujuan User</p>
                        @php $pendingCount = App\Models\User::where('status', 'menunggu')->count(); @endphp
                        @if($pendingCount > 0)
                        <span class="badge badge-info right">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('departments.index') }}" class="nav-link {{ Request::routeIs('departments.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Department</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('bbm.index') }}" class="nav-link {{ Request::routeIs('bbm.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-gas-pump"></i>
                        <p>Data BBM</p>
                    </a>
                </li>

                @endif

                <li class="nav-header">MAIN MENU</li>
                <li class="nav-item">
                    <a href="{{ route('kendaraan.index') }}" class="nav-link {{ Request::routeIs('kendaraan.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-car"></i>
                        <p>Kendaraan</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('claims.index') }}" class="nav-link {{ Request::routeIs('claims.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Claims</p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>