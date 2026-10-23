@extends('layouts.app')

@section('title', 'Sucursales')

@section('pretitle')
Plataforma
@endsection

@section('page-title')
Sucursales
@endsection

@section('page-actions')

<a
    href="{{ route('branches.create') }}"
    class="btn btn-primary">
    <i class="ti ti-plus me-2"></i>
    Nueva sucursal
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
        data-bs-dismiss="alert"></button>

</div>

@endif


<div class="card">

    <div class="card-header">

        <form
            method="GET"
            action="{{ route('branches.index') }}"
            class="row g-2 w-100">

            <div class="col-md-4">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Buscar sucursal...">

            </div>


            <div class="col-md-3">

                <select
                    name="business_id"
                    class="form-select">

                    <option value="">
                        Todos los negocios
                    </option>

                    @foreach($businesses as $business)

                    <option
                        value="{{ $business->id }}"
                        @selected(
                        request('business_id')==$business->id
                        )
                        >
                        {{ $business->name }}
                    </option>

                    @endforeach

                </select>

            </div>


            <div class="col-md-2">

                <select
                    name="status"
                    class="form-select">

                    <option value="">
                        Todos
                    </option>

                    <option
                        value="active"
                        @selected(request('status')==='active' )>
                        Activas
                    </option>

                    <option
                        value="inactive"
                        @selected(request('status')==='inactive' )>
                        Inactivas
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
                    href="{{ route('branches.index') }}"
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
                    <th>Sucursal</th>
                    <th>Negocio</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th class="w-1">Acciones</th>
                </tr>

            </thead>


            <tbody>

                @forelse($branches as $branch)

                <tr>

                    <td>

                        <div class="fw-bold">

                            {{ $branch->name }}

                            @if($branch->is_main)

                            <span class="badge bg-blue-lt ms-1">
                                Principal
                            </span>

                            @endif

                        </div>

                        <div class="text-secondary small">
                            {{ $branch->code }}
                        </div>

                    </td>


                    <td>
                        {{ $branch->business->name }}
                    </td>


                    <td>

                        @if($branch->city || $branch->state)

                        {{ $branch->city }}

                        @if($branch->city && $branch->state)
                        ,
                        @endif

                        {{ $branch->state }}

                        @else

                        <span class="text-secondary">
                            Sin ubicación
                        </span>

                        @endif

                    </td>


                    <td>

                        @if($branch->status === 'active')

                        <span class="badge bg-green-lt">
                            Activa
                        </span>

                        @else

                        <span class="badge bg-red-lt">
                            Inactiva
                        </span>

                        @endif

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
                                    href="{{ route('branches.edit', $branch) }}"
                                    class="dropdown-item">
                                    <i class="ti ti-edit me-2"></i>
                                    Editar
                                </a>


                                @if(!$branch->is_main && $branch->status === 'active')

                                <form
                                    action="{{ route('branches.make-main', $branch) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button class="dropdown-item">
                                        <i class="ti ti-star me-2"></i>
                                        Hacer principal
                                    </button>

                                </form>

                                @endif


                                @if(!$branch->is_main)

                                <div class="dropdown-divider"></div>

                                <form
                                    action="{{ route('branches.status', $branch) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="{{ $branch->status === 'active' ? 'inactive' : 'active' }}">

                                    <button
                                        class="dropdown-item {{ $branch->status === 'active' ? 'text-danger' : 'text-success' }}">

                                        <i
                                            class="ti {{ $branch->status === 'active' ? 'ti-building-off' : 'ti-building-check' }} me-2"></i>

                                        {{ $branch->status === 'active' ? 'Desactivar' : 'Activar' }}

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
                        No hay sucursales registradas.
                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($branches->hasPages())

    <div class="card-footer">
        {{ $branches->links() }}
    </div>

    @endif

</div>

@endsection