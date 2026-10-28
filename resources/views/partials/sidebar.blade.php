@php

$currentUser = auth()->user();

$currentBusiness = null;


if (
$currentUser &&
! $currentUser->isPlatformAdmin() &&
session()->has('current_business_id')
) {

$currentBusiness = $currentUser
->businesses()
->wherePivot('is_active', true)
->where(
'businesses.status',
'active'
)
->where(
'businesses.id',
session('current_business_id')
)
->first();

}


if ($currentUser?->isPlatformAdmin()) {

$homeUrl = route('dashboard');

} elseif($currentBusiness) {

$homeUrl = route(
'business.dashboard',
$currentBusiness
);

} else {

$homeUrl = route('business.select');

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
                href="{{ $homeUrl }}"
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

                {{-- ================================================== --}}
                {{-- ADMINISTRACIÓN DE VANTROVIA                       --}}
                {{-- ================================================== --}}

                @if(
                $currentUser &&
                $currentUser->isPlatformAdmin()
                )

                <li class="nav-item">

                    <a
                        href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-home"></i>
                        </span>

                        <span class="nav-link-title">
                            Dashboard
                        </span>

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="{{ route('businesses.index') }}"
                        class="nav-link {{ request()->routeIs('businesses.index') ? 'active' : '' }}">

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
                        href="{{ route('branches.index') }}"
                        class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-building"></i>
                        </span>

                        <span class="nav-link-title">
                            Sucursales
                        </span>

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="{{ route('users.index') }}"
                        class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-users"></i>
                        </span>

                        <span class="nav-link-title">
                            Usuarios
                        </span>

                    </a>

                </li>


                {{-- ================================================== --}}
                {{-- NEGOCIO ACTIVO                                    --}}
                {{-- ================================================== --}}

                @elseif($currentBusiness)

                {{-- Negocio --}}
                <li class="nav-item mb-2">

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


                {{-- Dashboard --}}
                @if(
                $currentUser->hasBusinessPermission(
                $currentBusiness,
                'dashboard.view'
                )
                )

                <li class="nav-item">

                    <a
                        href="{{ route(
                                    'business.dashboard',
                                    $currentBusiness
                                ) }}"
                        class="nav-link {{ request()->routeIs('business.dashboard') ? 'active' : '' }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-home"></i>
                        </span>

                        <span class="nav-link-title">
                            Dashboard
                        </span>

                    </a>

                </li>

                @endif


                {{-- Usuarios --}}
                @if(
                $currentUser->hasBusinessPermission(
                $currentBusiness,
                'users.view'
                )
                )

                <li class="nav-item">

                    <a
                        href="{{ route(
                                    'businesses.users.index',
                                    $currentBusiness
                                ) }}"
                        class="nav-link {{ request()->routeIs('businesses.users.*') ? 'active' : '' }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-users"></i>
                        </span>

                        <span class="nav-link-title">
                            Usuarios
                        </span>

                    </a>

                </li>

                @endif


                {{-- Roles --}}
                @if(
                $currentUser->hasBusinessPermission(
                $currentBusiness,
                'roles.view'
                )
                )

                <li class="nav-item">

                    <a
                        href="{{ route(
                                    'businesses.roles.index',
                                    $currentBusiness
                                ) }}"
                        class="nav-link {{ request()->routeIs('businesses.roles.*') ? 'active' : '' }}">

                        <span class="nav-link-icon">
                            <i class="ti ti-shield-lock"></i>
                        </span>

                        <span class="nav-link-title">
                            Roles y permisos
                        </span>

                    </a>

                </li>

                @endif


                {{-- Cambiar negocio --}}
                <li class="nav-item mt-3">

                    <a
                        href="{{ route('business.select') }}"
                        class="nav-link">

                        <span class="nav-link-icon">
                            <i class="ti ti-switch-horizontal"></i>
                        </span>

                        <span class="nav-link-title">
                            Cambiar negocio
                        </span>

                    </a>

                </li>


                {{-- ================================================== --}}
                {{-- SIN NEGOCIO ACTIVO                                --}}
                {{-- ================================================== --}}

                @elseif($currentUser)

                <li class="nav-item">

                    <a
                        href="{{ route('business.select') }}"
                        class="nav-link">

                        <span class="nav-link-icon">
                            <i class="ti ti-building-store"></i>
                        </span>

                        <span class="nav-link-title">
                            Seleccionar negocio
                        </span>

                    </a>

                </li>

                @endif

            </ul>

        </div>

    </div>

</aside>