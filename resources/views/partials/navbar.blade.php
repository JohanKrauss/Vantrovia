<header class="navbar navbar-expand-md d-print-none">

    <div class="container-xl">

        <div class="navbar-nav flex-row order-md-last ms-auto">

            <div class="nav-item me-3">

                <button
                    type="button"
                    class="btn btn-link nav-link px-0"
                    id="themeToggle"
                    title="Cambiar tema"
                    aria-label="Cambiar tema">
                    <i
                        id="themeIcon"
                        class="ti ti-moon fs-2"></i>
                </button>

            </div>

            <div class="nav-item dropdown">

                <a
                    href="#"
                    class="nav-link d-flex lh-1 text-reset p-0"
                    data-bs-toggle="dropdown"
                    aria-label="Abrir menú de usuario">

                    <span class="avatar avatar-sm me-2">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </span>

                    <div class="d-none d-xl-block">

                        <div>
                            {{ auth()->user()->name }}
                        </div>

                        <div class="mt-1 small text-secondary">
                            {{ auth()->user()->email }}
                        </div>

                    </div>

                    <i class="ti ti-chevron-down ms-2"></i>

                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">

                    <a
                        href="{{ route('password.edit') }}"
                        class="dropdown-item">
                        <i class="ti ti-lock me-2"></i>
                        Cambiar contraseña
                    </a>

                    <div class="dropdown-divider"></div>

                    <form
                        action="{{ route('logout') }}"
                        method="POST">
                        @csrf

                        <button
                            type="submit"
                            class="dropdown-item text-danger">
                            <i class="ti ti-logout me-2"></i>
                            Cerrar sesión
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</header>