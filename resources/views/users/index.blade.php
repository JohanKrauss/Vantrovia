@extends('layouts.app')

@section('title', 'Usuarios')

@section('pretitle')
Administración
@endsection

@section('page-title')
Usuarios
@endsection

@section('page-actions')

<a
    href="{{ route('users.create') }}"
    class="btn btn-primary">
    <i class="ti ti-plus me-2"></i>
    Nuevo usuario
</a>

@endsection


@section('content')


@if(session('success'))
<div
    class="alert alert-success alert-dismissible auto-dismiss-alert"
    role="alert">
    <div>
        <i class="ti ti-circle-check me-2"></i>
        {{ session('success') }}
    </div>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Cerrar"></button>
</div>
@endif

@if(session('warning'))
<div
    class="alert alert-warning alert-dismissible auto-dismiss-alert"
    role="alert">
    <div>
        <i class="ti ti-alert-triangle me-2"></i>
        {{ session('warning') }}
    </div>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Cerrar"></button>
</div>
@endif


<div class="card">

    <div class="card-header">

        <form
            method="GET"
            action="{{ route('users.index') }}"
            class="row g-2 w-100">

            <div class="col-md-5">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Buscar por nombre o correo...">

            </div>

            <div class="col-md-3">

                <select
                    name="status"
                    class="form-select">

                    <option value="">
                        Todos los estados
                    </option>

                    <option
                        value="active"
                        @selected(request('status')==='active' )>
                        Activos
                    </option>

                    <option
                        value="inactive"
                        @selected(request('status')==='inactive' )>
                        Inactivos
                    </option>

                </select>

            </div>

            <div class="col-auto">

                <button class="btn btn-primary">
                    <i class="ti ti-search"></i>
                    Buscar
                </button>

            </div>

            <div class="col-auto">

                <a
                    href="{{ route('users.index') }}"
                    class="btn btn-outline-secondary">
                    Limpiar
                </a>

            </div>

        </form>

    </div>


    <div class="table-responsive-lg">

        <table class="table table-vcenter card-table">

            <thead>

                <tr>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Negocios</th>
                    <th>Estado</th>
                    <th>Creado</th>
                    <th class="w-1">Acciones</th>
                </tr>

            </thead>

            <tbody>

                @forelse($users as $user)

                <tr>

                    {{-- Usuario --}}
                    <td>

                        <div class="d-flex align-items-center">

                            <span class="avatar avatar-sm me-3">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>

                            <div>

                                <strong>
                                    {{ $user->name }}
                                </strong>

                                @if(auth()->id() === $user->id)

                                <span class="badge bg-blue-lt ms-1">
                                    Tú
                                </span>

                                @endif

                            </div>

                        </div>

                    </td>


                    {{-- Correo --}}
                    <td>
                        {{ $user->email }}
                    </td>


                    {{-- Negocios --}}
                    <td>

                        <span class="badge bg-blue-lt">
                            {{ $user->active_businesses_count }}
                        </span>

                    </td>


                    {{-- Estado --}}
                    <td>

                        @if($user->is_active)

                        <span class="badge bg-green-lt">
                            Activo
                        </span>

                        @else

                        <span class="badge bg-red-lt">
                            Inactivo
                        </span>

                        @endif

                    </td>


                    {{-- Creado --}}
                    <td>
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>


                    {{-- Acciones --}}
                    <td>

                        <div class="dropdown {{ $loop->last ? 'dropup' : '' }}">

                            <button
                                class="btn btn-icon"
                                data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">

                                <a
                                    href="{{ route('users.edit', $user) }}"
                                    class="dropdown-item">
                                    <i class="ti ti-edit me-2"></i>
                                    Editar
                                </a>


                                <form
                                    action="{{ route('users.password-reset', $user) }}"
                                    method="POST">

                                    @csrf

                                    <button
                                        type="submit"
                                        class="dropdown-item">
                                        <i class="ti ti-mail me-2"></i>
                                        Restablecer contraseña
                                    </button>

                                </form>


                                @if(auth()->id() !== $user->id)

                                <div class="dropdown-divider"></div>

                                <form
                                    action="{{ route('users.toggle-status', $user) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="dropdown-item {{ $user->is_active ? 'text-danger' : 'text-success' }}">

                                        <i class="ti {{ $user->is_active ? 'ti-user-off' : 'ti-user-check' }} me-2"></i>

                                        {{ $user->is_active ? 'Desactivar' : 'Activar' }}

                                    </button>

                                </form>

                                @endif

                            </div>

                        </div>

                    </td>

                </tr>

                @empty

                <tr>

                    <td
                        colspan="6"
                        class="text-center text-secondary py-5">
                        No hay usuarios registrados.
                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($users->hasPages())

    <div class="card-footer">

        {{ $users->links() }}

    </div>

    @endif

</div>

@endsection