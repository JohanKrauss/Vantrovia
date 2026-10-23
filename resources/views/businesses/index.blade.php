@extends('layouts.app')

@section('title', 'Negocios')

@section('pretitle')
Plataforma
@endsection

@section('page-title')
Negocios
@endsection


@section('page-actions')

<a
    href="{{ route('businesses.create') }}"
    class="btn btn-primary">
    <i class="ti ti-plus me-2"></i>
    Nuevo negocio
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
        data-bs-dismiss="alert"></button>

</div>

@endif


<div class="card">

    <div class="card-header">

        <form
            method="GET"
            action="{{ route('businesses.index') }}"
            class="row g-2 w-100">

            <div class="col-md-5">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Buscar negocio...">

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
                        value="pending"
                        @selected(request('status')==='pending' )>
                        Pendientes
                    </option>

                    <option
                        value="suspended"
                        @selected(request('status')==='suspended' )>
                        Suspendidos
                    </option>

                    <option
                        value="cancelled"
                        @selected(request('status')==='cancelled' )>
                        Cancelados
                    </option>

                </select>

            </div>


            <div class="col-auto">

                <button class="btn btn-primary">
                    <i class="ti ti-search me-1"></i>
                    Buscar
                </button>

            </div>


            <div class="col-auto">

                <a
                    href="{{ route('businesses.index') }}"
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
                    <th>Negocio</th>
                    <th>Contacto</th>
                    <th>Estado</th>
                    <th>Creado</th>
                    <th class="w-1">Acciones</th>
                </tr>

            </thead>


            <tbody>

                @forelse($businesses as $business)

                @php

                [$statusClass, $statusLabel] = match($business->status) {
                'active' =>
                ['bg-green-lt', 'Activo'],

                'pending' =>
                ['bg-yellow-lt', 'Pendiente'],

                'suspended' =>
                ['bg-orange-lt', 'Suspendido'],

                'cancelled' =>
                ['bg-red-lt', 'Cancelado'],

                default =>
                ['bg-secondary-lt', $business->status],
                };

                @endphp


                <tr>

                    <td>

                        <div class="fw-bold">
                            {{ $business->name }}
                        </div>

                        <div class="text-secondary small">
                            {{ $business->slug }}.vantrovia.com
                        </div>

                    </td>


                    <td>

                        <div>
                            {{ $business->email ?: 'Sin correo' }}
                        </div>

                        @if($business->phone)

                        <div class="text-secondary small">
                            {{ $business->phone }}
                        </div>

                        @endif

                    </td>


                    <td>

                        <span class="badge {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>

                    </td>


                    <td>
                        {{ $business->created_at->format('d/m/Y') }}
                    </td>


                    <td>

                        <div
                            class="dropdown {{ $loop->last ? 'dropup' : '' }}">

                            <button
                                type="button"
                                class="btn btn-icon"
                                data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>


                            <div class="dropdown-menu dropdown-menu-end">

                                <a
                                    href="{{ route('businesses.edit', $business) }}"
                                    class="dropdown-item">
                                    <i class="ti ti-edit me-2"></i>
                                    Editar
                                </a>

                                <a
                                    href="{{ route('businesses.users.index', $business) }}"
                                    class="dropdown-item">
                                    <i class="ti ti-users me-2"></i>
                                    Usuarios
                                </a>


                                @if($business->status !== 'active')

                                <form
                                    action="{{ route('businesses.status', $business) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="active">

                                    <button
                                        class="dropdown-item text-success">
                                        <i class="ti ti-circle-check me-2"></i>
                                        Activar
                                    </button>

                                </form>

                                @endif


                                @if($business->status !== 'suspended')

                                <form
                                    action="{{ route('businesses.status', $business) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="suspended">

                                    <button
                                        class="dropdown-item">
                                        <i class="ti ti-player-pause me-2"></i>
                                        Suspender
                                    </button>

                                </form>

                                @endif


                                @if($business->status !== 'cancelled')

                                <div class="dropdown-divider"></div>

                                <form
                                    action="{{ route('businesses.status', $business) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Cancelar este negocio?')">

                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="cancelled">

                                    <button
                                        class="dropdown-item text-danger">
                                        <i class="ti ti-ban me-2"></i>
                                        Cancelar
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
                        colspan="5"
                        class="text-center text-secondary py-5">
                        No hay negocios registrados.
                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($businesses->hasPages())

    <div class="card-footer">
        {{ $businesses->links() }}
    </div>

    @endif

</div>

@endsection