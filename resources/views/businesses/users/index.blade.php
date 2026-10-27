@extends('layouts.app')

@section('title', 'Usuarios del negocio')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Usuarios del negocio
@endsection


@section('page-actions')

<a
    href="{{ route('users.create') }}"
    class="btn btn-outline-primary">
    <i class="ti ti-user-plus me-2"></i>
    Crear usuario
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


<div class="row row-cards">

    {{-- Asignar usuario --}}
    <div class="col-lg-4">

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    Asignar usuario
                </h3>
            </div>


            <form
                action="{{ route('businesses.users.store', $business) }}"
                method="POST">
                @csrf

                <div class="card-body">

                    {{-- Usuario --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Usuario
                        </label>

                        <select
                            name="user_id"
                            class="form-select @error('user_id') is-invalid @enderror"
                            required>

                            <option value="">
                                Selecciona un usuario
                            </option>

                            @foreach($availableUsers as $user)

                            <option
                                value="{{ $user->id }}"
                                @selected(old('user_id')==$user->id)
                                @disabled(!$user->is_active)
                                >
                                {{ $user->name }}
                                — {{ $user->email }}

                                @if(!$user->is_active)
                                (Desactivado)
                                @endif
                            </option>

                            @endforeach

                        </select>

                        @error('user_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    {{-- Rol --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Rol
                        </label>

                        <select
                            name="role_id"
                            class="form-select @error('role_id') is-invalid @enderror"
                            required>

                            <option value="">
                                Selecciona un rol
                            </option>

                            @foreach($roles as $role)

                            <option
                                value="{{ $role->id }}"
                                @selected(old('role_id')==$role->id)
                                >
                                {{ $role->name }}
                            </option>

                            @endforeach

                        </select>

                        @error('role_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    @if($availableUsers->isEmpty())

                    <div class="text-secondary small">
                        Todos los usuarios registrados ya están
                        relacionados con este negocio.
                    </div>

                    @elseif($roles->isEmpty())

                    <div class="text-warning small">
                        Este negocio no tiene roles disponibles.
                        Crea un rol antes de asignar usuarios.
                    </div>

                    @endif

                </div>


                <div class="card-footer">

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                        @disabled(
                        $availableUsers->isEmpty() ||
                        $roles->isEmpty()
                        )
                        >
                        <i class="ti ti-user-plus me-2"></i>
                        Asignar usuario
                    </button>

                </div>

            </form>

        </div>


        {{-- Información del negocio --}}
        <div class="card mt-3">

            <div class="card-body">

                <div class="text-secondary mb-1">
                    Negocio
                </div>

                <div class="fw-bold">
                    {{ $business->name }}
                </div>

                <div class="text-secondary small">
                    {{ $business->slug }}.vantrovia.com
                </div>

            </div>

        </div>

    </div>


    {{-- Usuarios asignados --}}
    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">

                <div>

                    <h3 class="card-title">
                        Usuarios asignados
                    </h3>

                    <div class="text-secondary small">
                        {{ $business->users->count() }}
                        usuario(s)
                    </div>

                </div>

            </div>


            <div class="table-responsive-lg">

                <table class="table table-vcenter card-table">

                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th class="w-1">
                                Acciones
                            </th>
                        </tr>
                    </thead>


                    <tbody>

                        @forelse($business->users as $user)

                        <tr>

                            {{-- Usuario --}}
                            <td>

                                <div class="d-flex align-items-center">

                                    <span class="avatar avatar-sm me-3">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </span>

                                    <div>

                                        <div class="fw-bold">
                                            {{ $user->name }}
                                        </div>

                                        @if(auth()->id() === $user->id)

                                        <span class="badge bg-blue-lt">
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


                            {{-- Rol --}}
                            <td>

                                <form
                                    action="{{ route(
                                        'businesses.users.role',
                                        [
                                            $business,
                                            $user
                                        ]
                                    ) }}"
                                    method="POST"
                                    class="d-flex gap-2">

                                    @csrf
                                    @method('PATCH')

                                    <select
                                        name="role_id"
                                        class="form-select form-select-sm"
                                        required>

                                        @if(!$user->pivot->role_id)

                                        <option value="" selected disabled>
                                            Sin rol
                                        </option>

                                        @endif

                                        @foreach($roles as $role)

                                        <option
                                            value="{{ $role->id }}"
                                            @selected(
                                            $user->pivot->role_id == $role->id
                                            )
                                            >
                                            {{ $role->name }}
                                        </option>

                                        @endforeach

                                    </select>


                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Guardar rol">
                                        <i class="ti ti-check"></i>
                                    </button>

                                </form>

                            </td>


                            {{-- Estado --}}
                            <td>

                                @if($user->pivot->is_active)

                                <span class="badge bg-green-lt">
                                    Activo
                                </span>

                                @else

                                <span class="badge bg-red-lt">
                                    Inactivo
                                </span>

                                @endif

                            </td>


                            {{-- Acciones --}}
                            <td>

                                <div
                                    class="dropdown {{ $loop->last ? 'dropup' : '' }}">

                                    <button
                                        type="button"
                                        class="btn btn-icon"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        aria-label="Acciones">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>


                                    <div class="dropdown-menu dropdown-menu-end">

                                        <a
                                            href="{{ route('users.edit', $user) }}"
                                            class="dropdown-item">
                                            <i class="ti ti-edit me-2"></i>
                                            Editar usuario
                                        </a>


                                        <div class="dropdown-divider"></div>


                                        <form
                                            action="{{ route(
                                                'businesses.users.status',
                                                [
                                                    $business,
                                                    $user
                                                ]
                                            ) }}"
                                            method="POST">

                                            @csrf
                                            @method('PATCH')


                                            <input
                                                type="hidden"
                                                name="is_active"
                                                value="{{ $user->pivot->is_active ? 0 : 1 }}">


                                            <button
                                                type="submit"
                                                class="dropdown-item {{ $user->pivot->is_active ? 'text-danger' : 'text-success' }}">

                                                <i
                                                    class="ti {{ $user->pivot->is_active ? 'ti-user-off' : 'ti-user-check' }} me-2"></i>

                                                {{ $user->pivot->is_active
                                                    ? 'Desactivar del negocio'
                                                    : 'Activar en el negocio'
                                                }}

                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center text-secondary py-5">

                                <i class="ti ti-users-minus fs-1 d-block mb-2"></i>

                                Este negocio todavía no tiene usuarios asignados.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection