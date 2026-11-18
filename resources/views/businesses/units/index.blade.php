@extends('layouts.app')

@section('title', 'Unidades de medida')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Unidades de medida
@endsection


@php

    $canCreate = auth()
        ->user()
        ->hasBusinessPermission(
            $business,
            'units.create'
        );

    $canUpdate = auth()
        ->user()
        ->hasBusinessPermission(
            $business,
            'units.update'
        );

    $canStatus = auth()
        ->user()
        ->hasBusinessPermission(
            $business,
            'units.status'
        );

@endphp


@section('page-actions')

@if($canCreate)

    <a
        href="{{ route(
            'businesses.units.create',
            $business
        ) }}"
        class="btn btn-primary"
    >
        <i class="ti ti-plus me-2"></i>
        Nueva unidad
    </a>

@endif

@endsection


@section('content')

@if(session('success'))

    <div
        class="alert alert-success alert-dismissible auto-dismiss-alert"
        role="alert"
    >

        <div>
            <i class="ti ti-circle-check me-2"></i>
            {{ session('success') }}
        </div>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Cerrar"
        ></button>

    </div>

@endif


<div class="card">

    <div class="card-header">

        <div>

            <h3 class="card-title">
                Unidades de medida
            </h3>

            <div class="text-secondary small">
                {{ $units->total() }}
                unidad(es)
            </div>

        </div>

    </div>


    <div class="table-responsive-lg">

        <table class="table table-vcenter card-table">

            <thead>

                <tr>
                    <th>Nombre</th>
                    <th>Símbolo</th>
                    <th>Estado</th>

                    @if($canUpdate || $canStatus)
                        <th class="w-1">
                            Acciones
                        </th>
                    @endif
                </tr>

            </thead>


            <tbody>

                @forelse($units as $unit)

                    <tr>

                        <td>

                            <div class="fw-bold">
                                {{ $unit->name }}
                            </div>

                            <div class="text-secondary small">
                                {{ $unit->slug }}
                            </div>

                        </td>


                        <td>

                            @if($unit->symbol)

                                <span class="badge bg-blue-lt">
                                    {{ $unit->symbol }}
                                </span>

                            @else

                                <span class="text-secondary">
                                    Sin símbolo
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($unit->is_active)

                                <span class="badge bg-green-lt">
                                    Activa
                                </span>

                            @else

                                <span class="badge bg-red-lt">
                                    Inactiva
                                </span>

                            @endif

                        </td>


                        @if($canUpdate || $canStatus)

                            <td>

                                <div
                                    class="dropdown {{ $loop->last ? 'dropup' : '' }}"
                                >

                                    <button
                                        type="button"
                                        class="btn btn-icon"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>


                                    <div class="dropdown-menu dropdown-menu-end">

                                        @if($canUpdate)

                                            <a
                                                href="{{ route(
                                                    'businesses.units.edit',
                                                    [
                                                        $business,
                                                        $unit
                                                    ]
                                                ) }}"
                                                class="dropdown-item"
                                            >
                                                <i class="ti ti-edit me-2"></i>
                                                Editar
                                            </a>

                                        @endif


                                        @if($canStatus)

                                            @if($canUpdate)
                                                <div class="dropdown-divider"></div>
                                            @endif

                                            <form
                                                action="{{ route(
                                                    'businesses.units.status',
                                                    [
                                                        $business,
                                                        $unit
                                                    ]
                                                ) }}"
                                                method="POST"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <input
                                                    type="hidden"
                                                    name="is_active"
                                                    value="{{ $unit->is_active ? 0 : 1 }}"
                                                >

                                                <button
                                                    type="submit"
                                                    class="dropdown-item {{ $unit->is_active ? 'text-danger' : 'text-success' }}"
                                                >

                                                    <i
                                                        class="ti {{ $unit->is_active ? 'ti-eye-off' : 'ti-eye' }} me-2"
                                                    ></i>

                                                    {{ $unit->is_active
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
                            colspan="{{ ($canUpdate || $canStatus) ? 4 : 3 }}"
                            class="text-center text-secondary py-5"
                        >

                            <i class="ti ti-ruler fs-1 d-block mb-2"></i>

                            No hay unidades registradas.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($units->hasPages())

        <div class="card-footer">
            {{ $units->links() }}
        </div>

    @endif

</div>

@endsection