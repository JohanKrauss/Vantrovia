@php

$currentUser = auth()->user();

$currentBusiness = null;

if (
$currentUser &&
! $currentUser->isPlatformAdmin()
) {
$currentBusiness = $currentUser
->businesses()
->wherePivot('is_active', true)
->where('businesses.status', 'active')
->first();
}

@endphp


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

                {{-- ========================================== --}}
                {{-- ADMINISTRADOR DE PLATAFORMA                --}}
                {{-- ========================================== --}}

                @if(
                $currentUser &&
                $currentUser->isPlatformAdmin()
                )

                {{-- Dashboard --}}
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

                    </a>

                </li>


                {{-- Negocios --}}
                <li class="nav-item">

                    <a
                        class="nav-link {{ request()->routeIs('businesses.index') ? 'active' : '' }}"
                        href="{{ route('businesses.index') }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-building-store"></i>
                        </span>

                        <span class="nav-link-title">
                            Negocios
                        </span>

                    </a>

                </li>


                {{-- Sucursales --}}
                <li class="nav-item">

                    <a
                        class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}"
                        href="{{ route('branches.index') }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-building"></i>
                        </span>

                        <span class="nav-link-title">
                            Sucursales
                        </span>

                    </a>

                </li>


                {{-- Usuarios --}}
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


                {{-- ========================================== --}}
                {{-- USUARIO DE NEGOCIO                         --}}
                {{-- ========================================== --}}

                @elseif($currentBusiness)

                {{-- Nombre del negocio --}}
                <li class="nav-item mt-2 mb-2">

                    <div class="nav-link disabled">

                        <span class="nav-link-icon">
                            <i class="ti ti-building-store"></i>
                        </span>

                        <span class="nav-link-title">

                            <strong>
                                {{ $currentBusiness->name }}
                            </strong>

                        </span>

                    </div>

                </li>


                {{-- Usuarios del negocio --}}
                @if(
                $currentUser->hasBusinessPermission(
                $currentBusiness,
                'users.view'
                )
                )

                <li class="nav-item">

                    <a
                        class="nav-link {{ request()->routeIs('businesses.users.*') ? 'active' : '' }}"
                        href="{{ route(
                                    'businesses.users.index',
                                    $currentBusiness
                                ) }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-users"></i>
                        </span>

                        <span class="nav-link-title">
                            Usuarios
                        </span>

                    </a>

                </li>

                @endif


                {{-- Roles y permisos --}}
                @if(
                $currentUser->hasBusinessPermission(
                $currentBusiness,
                'roles.view'
                )
                )

                <li class="nav-item">

                    <a
                        class="nav-link {{ request()->routeIs('businesses.roles.*') ? 'active' : '' }}"
                        href="{{ route(
                                    'businesses.roles.index',
                                    $currentBusiness
                                ) }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-shield-lock"></i>
                        </span>

                        <span class="nav-link-title">
                            Roles y permisos
                        </span>

                    </a>

                </li>

                @endif


                {{-- ========================================== --}}
                {{-- SIN NEGOCIO                                --}}
                {{-- ========================================== --}}

                @elseif($currentUser)

                <li class="nav-item">

                    <div class="nav-link disabled">

                        <span class="nav-link-icon">
                            <i class="ti ti-alert-circle"></i>
                        </span>

                        <span class="nav-link-title">
                            Sin negocio asignado
                        </span>

                    </div>

                </li>

                @endif

            </ul>

        </div>

    </div>

</aside>