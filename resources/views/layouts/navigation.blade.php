<!-- Top Header Navigation -->
<nav class="navbar navbar-expand navbar-light bg-white border-bottom d-none d-md-block">
    <div class="container-fluid">
        <div class="ms-auto">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile.edit') }}">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        <span class="badge bg-secondary ms-1">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar Navigation -->
<nav id="sidebar" class="bg-dark border-end">
    <div class="position-sticky pt-3">
        <!-- Brand -->
        <div class="px-3 mb-3 pb-3 border-bottom border-secondary">
            <a href="{{ route('dashboard') }}" class="text-decoration-none">
                <h5 class="text-white mb-0">
                    <i class="bi bi-cash-stack"></i> Budget Management
                </h5>
            </a>
        </div>

        <!-- Navigation Links -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                    <i class="bi bi-folder"></i> Project
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}" href="{{ route('accounts.index') }}">
                    <i class="bi bi-book"></i> Kategori
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('account-banks.*') ? 'active' : '' }}" href="{{ route('account-banks.index') }}">
                    <i class="bi bi-bank"></i> Rekening Bank
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}" href="{{ route('budgets.index') }}">
                    <i class="bi bi-journal-text"></i> Pengajuan
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('realisasi-budgets.*') ? 'active' : '' }}" href="{{ route('realisasi-budgets.index') }}">
                    <i class="bi bi-cash-coin"></i> Realisasi
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">
                    <i class="bi bi-receipt"></i> Invoice
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.profit_loss') }}">
                    <i class="bi bi-file-earmark-text"></i> Laporan
                </a>
            </li>

            @if(in_array(Auth::user()->role, ['project_manager', 'finance']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}" href="{{ route('approvals.index') }}">
                    <i class="bi bi-check-circle"></i> Approvals
                </a>
            </li>
            @endif
        </ul>
    </div>
</nav>

<!-- Top Navbar for Mobile -->
<nav class="navbar navbar-dark bg-primary d-md-none">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-cash-stack"></i> Budget Management
        </a>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" id="mobileUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mobileUserDropdown">
                    <li>
                        <h6 class="dropdown-header">{{ Auth::user()->name }}</h6>
                    </li>
                    <li><span class="dropdown-item-text small"><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span></span></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Offcanvas Menu -->
<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title">
            <i class="bi bi-cash-stack"></i> Budget Management
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body px-0">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}" href="{{ route('budgets.index') }}">
                    <i class="bi bi-journal-text"></i> Budgets
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('realisasi-budgets.*') ? 'active' : '' }}" href="{{ route('realisasi-budgets.index') }}">
                    <i class="bi bi-cash-coin"></i> Realisasi Budget
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">
                    <i class="bi bi-receipt"></i> Invoice
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.profit_loss') }}">
                    <i class="bi bi-file-earmark-text"></i> Laporan
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                    <i class="bi bi-folder"></i> Project
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}" href="{{ route('accounts.index') }}">
                    <i class="bi bi-book"></i> Accounts
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('account-banks.*') ? 'active' : '' }}" href="{{ route('account-banks.index') }}">
                    <i class="bi bi-bank"></i> Rekening Bank
                </a>
            </li>

            @if(in_array(Auth::user()->role, ['project_manager', 'finance']))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}" href="{{ route('approvals.index') }}">
                    <i class="bi bi-check-circle"></i> Approvals
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>

<style>
    /* Top Header Navigation */
    .navbar.d-none.d-md-block {
        position: fixed;
        top: 0;
        right: 0;
        left: 250px;
        z-index: 1020;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    .navbar .dropdown-item form button,
    .dropdown-menu .dropdown-item form button {
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        padding: 0;
        color: inherit;
    }

    /* Sidebar Styles */
    #sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        z-index: 1000;
        overflow-y: auto;
    }

    #sidebar .nav-link {
        color: rgba(255, 255, 255, 0.75);
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    #sidebar .nav-link:hover,
    #sidebar .nav-link.active {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
    }

    #sidebar .nav-link i {
        margin-right: 0.5rem;
        width: 1.2rem;
    }

    #sidebar .nav-link button {
        color: rgba(255, 255, 255, 0.75);
    }

    #sidebar .nav-link button:hover {
        color: #fff;
    }

    /* Mobile styles */
    @media (max-width: 767.98px) {
        #sidebar {
            display: none;
        }
    }

    @media (min-width: 768px) {
        .navbar.d-md-none {
            display: none !important;
        }
    }

    /* Mobile menu styles */
    .offcanvas .nav-link {
        color: rgba(255, 255, 255, 0.75);
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    .offcanvas .nav-link:hover,
    .offcanvas .nav-link.active {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .offcanvas .nav-link i {
        margin-right: 0.5rem;
        width: 1.2rem;
    }
</style>
