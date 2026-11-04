@extends('layouts.app')

@section('title', 'Sucursales')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Sucursales
@endsection


@php

$canCreate = auth()
->user()
->hasBusinessPermission(
$business,
'branches.create'
);

$canUpdate = auth()
->user()
->hasBusinessPermission(
$business,
'branches.update'
);

$canStatus = auth()
->user()
->hasBusinessPermission(
$business,
'branches.status'
);

@endphp


@section('page-actions')

@if($canCreate)

<a
    href="{{ route(
            'businesses.branches.create',
            $business
        ) }}"
    class="btn btn-primary">
    <i class="ti ti-plus me-2"></i>
    Nueva sucursal
</a>

@endif

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

        <div>

            <h3 class="card-title">
                {{ $business->name }}
            </h3>

            <div class="text-secondary small">
                {{ $branches->total() }}
                sucursal(es)
            </div>

        </div>

    </div>


    <div class="table-responsive-lg">

        <table class="table table-vcenter card-table">

            <thead>

                <tr>
                    <th>Sucursal</th>
                    <th>Ubicación</th>
                    <th>Contacto</th>
                    <th>Estado</th>

                    @if($canUpdate || $canStatus)
                    <th class="w-1">
                        Acciones
                    </th>
                    @endif
                </tr>

            </thead>


            <tbody>

                @forelse($branches as $branch)

                <tr>

                    {{-- Sucursal --}}
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


                    {{-- Ubicación --}}
                    <td>

                        @if(
                        $branch->city ||
                        $branch->state
                        )

                        {{ $branch->city }}

                        @if(
                        $branch->city &&
                        $branch->state
                        )
                        ,
                        @endif

                        {{ $branch->state }}

                        @else

                        <span class="text-secondary">
                            Sin ubicación
                        </span>

                        @endif

                    </td>


                    {{-- Contacto --}}
                    <td>

                        @if($branch->email)

                        <div>
                            {{ $branch->email }}
                        </div>

                        @endif


                        @if($branch->phone)

                        <div class="text-secondary small">
                            {{ $branch->phone }}
                        </div>

                        @endif


                        @if(
                        !$branch->email &&
                        !$branch->phone
                        )

                        <span class="text-secondary">
                            Sin contacto
                        </span>

                        @endif

                    </td>


                    {{-- Estado --}}
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


                    {{-- Acciones --}}
                    @if($canUpdate || $canStatus)

                    <td>

                        <div
                            class="dropdown {{ $loop->last ? 'dropup' : '' }}">

                            <button
                                type="button"
                                class="btn btn-icon"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </button>


                            <div class="dropdown-menu dropdown-menu-end">

                                {{-- Editar --}}
                                @if($canUpdate)

                                <a
                                    href="{{ route(
                                                'businesses.branches.edit',
                                                [
                                                    $business,
                                                    $branch
                                                ]
                                            ) }}"
                                    class="dropdown-item">
                                    <i class="ti ti-edit me-2"></i>
                                    Editar
                                </a>

                                @endif


                                {{-- Hacer principal --}}
                                @if(
                                $canUpdate &&
                                !$branch->is_main &&
                                $branch->status === 'active'
                                )

                                <form
                                    action="{{ route(
                                                'businesses.branches.make-main',
                                                [
                                                    $business,
                                                    $branch
                                                ]
                                            ) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="dropdown-item">
                                        <i class="ti ti-star me-2"></i>
                                        Hacer principal
                                    </button>

                                </form>

                                @endif


                                {{-- Estado --}}
                                @if(
                                $canStatus &&
                                !$branch->is_main
                                )

                                <div class="dropdown-divider"></div>

                                <form
                                    action="{{ route(
                                                'businesses.branches.status',
                                                [
                                                    $business,
                                                    $branch
                                                ]
                                            ) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="status"
                                        value="{{ $branch->status === 'active' ? 'inactive' : 'active' }}">


                                    <button
                                        type="submit"
                                        class="dropdown-item {{ $branch->status === 'active' ? 'text-danger' : 'text-success' }}">

                                        <i
                                            class="ti {{ $branch->status === 'active' ? 'ti-building-off' : 'ti-building-check' }} me-2"></i>

                                        {{ $branch->status === 'active'
                                                    ? 'Desactivar'
                                                    : 'Activar'
                                                }}

                                    </button>

                                </form>

                                @endif

                            </div>

                        </div>

                    </td>

                    @endif

                </tr>

                @empty

                <tr>

                    <td
                        colspan="{{ ($canUpdate || $canStatus) ? 5 : 4 }}"
                        class="text-center text-secondary py-5">

                        <i class="ti ti-building-off fs-1 d-block mb-2"></i>

                        No hay sucursales registradas en este negocio.

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