@extends('layouts.app')

@section('title', 'Proveedores')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Proveedores
@endsection


@php

$canCreate = auth()
->user()
->hasBusinessPermission(
$business,
'suppliers.create'
);

$canUpdate = auth()
->user()
->hasBusinessPermission(
$business,
'suppliers.update'
);

$canStatus = auth()
->user()
->hasBusinessPermission(
$business,
'suppliers.status'
);

@endphp


@section('page-actions')

@if($canCreate)

<a
    href="{{ route(
            'businesses.suppliers.create',
            $business
        ) }}"
    class="btn btn-primary">
    <i class="ti ti-plus me-2"></i>
    Nuevo proveedor
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
        data-bs-dismiss="alert"></button>

</div>

@endif


<div class="card">

    <div class="card-header">

        <div class="row g-2 align-items-center w-100">

            <div class="col">

                <h3 class="card-title">
                    Proveedores
                </h3>

                <div class="text-secondary small">
                    {{ $suppliers->total() }}
                    proveedor(es)
                </div>

            </div>


            <div class="col-auto ms-auto">

                <form
                    action="{{ route(
                        'businesses.suppliers.index',
                        $business
                    ) }}"
                    method="GET">

                    <div class="input-group">

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="Nombre, RFC, correo...">

                        <button
                            type="submit"
                            class="btn btn-outline-secondary">
                            <i class="ti ti-search"></i>
                        </button>


                        @if($search !== '')

                        <a
                            href="{{ route(
                                    'businesses.suppliers.index',
                                    $business
                                ) }}"
                            class="btn btn-outline-secondary">
                            <i class="ti ti-x"></i>
                        </a>

                        @endif

                    </div>

                </form>

            </div>

        </div>

    </div>


    <div class="table-responsive-lg">

        <table class="table table-vcenter card-table">

            <thead>

                <tr>
                    <th>Proveedor</th>
                    <th>RFC / Fiscal</th>
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

                @forelse($suppliers as $supplier)

                <tr>

                    <td>

                        <div class="fw-bold">
                            {{ $supplier->name }}
                        </div>

                        @if($supplier->legal_name)

                        <div class="text-secondary small">
                            {{ $supplier->legal_name }}
                        </div>

                        @endif

                    </td>


                    <td>

                        @if($supplier->tax_id)

                        {{ $supplier->tax_id }}

                        @else

                        <span class="text-secondary">
                            —
                        </span>

                        @endif

                    </td>


                    <td>

                        @if($supplier->email)

                        <div>
                            <i class="ti ti-mail me-1"></i>
                            {{ $supplier->email }}
                        </div>

                        @endif


                        @if($supplier->phone)

                        <div class="text-secondary small">
                            <i class="ti ti-phone me-1"></i>
                            {{ $supplier->phone }}
                        </div>

                        @endif


                        @if(
                        !$supplier->email
                        && !$supplier->phone
                        )

                        <span class="text-secondary">
                            Sin contacto
                        </span>

                        @endif

                    </td>


                    <td>

                        @if($supplier->is_active)

                        <span class="badge bg-green-lt">
                            Activo
                        </span>

                        @else

                        <span class="badge bg-red-lt">
                            Inactivo
                        </span>

                        @endif

                    </td>


                    @if($canUpdate || $canStatus)

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

                                @if($canUpdate)

                                <a
                                    href="{{ route(
                                                    'businesses.suppliers.edit',
                                                    [
                                                        $business,
                                                        $supplier
                                                    ]
                                                ) }}"
                                    class="dropdown-item">
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
                                                    'businesses.suppliers.status',
                                                    [
                                                        $business,
                                                        $supplier
                                                    ]
                                                ) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="is_active"
                                        value="{{ $supplier->is_active ? 0 : 1 }}">

                                    <button
                                        type="submit"
                                        class="dropdown-item {{ $supplier->is_active ? 'text-danger' : 'text-success' }}">

                                        <i
                                            class="ti {{ $supplier->is_active ? 'ti-eye-off' : 'ti-eye' }} me-2"></i>

                                        {{ $supplier->is_active
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

                        <i class="ti ti-truck-delivery fs-1 d-block mb-2"></i>

                        @if($search !== '')

                        No se encontraron proveedores para
                        "{{ $search }}".

                        @else

                        No hay proveedores registrados.

                        @endif

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($suppliers->hasPages())

    <div class="card-footer">
        {{ $suppliers->links() }}
    </div>

    @endif

</div>

@endsection