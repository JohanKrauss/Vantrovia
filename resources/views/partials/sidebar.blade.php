<aside class="navbar navbar-vertical navbar-expand-lg position-absolute">

    <div class="container-fluid">

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu"
            aria-expanded="false"
            aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <h1 class="navbar-brand navbar-brand-autodark">

            <a
                href="{{ route('dashboard') }}"
                class="text-decoration-none">
                <span class="fs-3 fw-bold">
                    Vantrovia
                </span>
            </a>

        </h1>

        <div
            class="collapse navbar-collapse"
            id="sidebar-menu">

            <ul class="navbar-nav pt-lg-3">

                <li class="nav-item">

                    <a
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-home"></i>
                        </span>

                        <span class="nav-link-title">
                            Dashboard
                        </span>

                <li class="nav-item">

                    <a
                        class="nav-link {{ request()->routeIs('businesses.*') ? 'active' : '' }}"
                        href="{{ route('businesses.index') }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-building-store"></i>
                        </span>

                        <span class="nav-link-title">
                            Negocios
                        </span>

                    </a>

                </li>

                <li class="nav-item">

                    <a
                        class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}"
                        href="{{ route('branches.index') }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-building-store"></i>
                        </span>

                        <span class="nav-link-title">
                            Sucursales
                        </span>

                    </a>

                </li>

                <li class="nav-item">

                    <a
                        class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-users"></i>
                        </span>

                        <span class="nav-link-title">
                            Usuarios
                        </span>

                    </a>

                </li>

                </a>

                </li>

            </ul>

        </div>

    </div>

</aside>