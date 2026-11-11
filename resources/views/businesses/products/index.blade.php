@extends('layouts.app')

@section('title', 'Productos')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Productos
@endsection


@php

    $canCreate = auth()
        ->user()
        ->hasBusinessPermission(
            $business,
            'products.create'
        );

    $canUpdate = auth()
        ->user()
        ->hasBusinessPermission(
            $business,
            'products.update'
        );

    $canStatus = auth()
        ->user()
        ->hasBusinessPermission(
            $business,
            'products.status'
        );

@endphp


@section('page-actions')

@if($canCreate)

    <a
        href="{{ route(
            'businesses.products.create',
            $business
        ) }}"
        class="btn btn-primary"
    >
        <i class="ti ti-plus me-2"></i>
        Nuevo producto
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

        <div class="row g-2 align-items-center w-100">

            <div class="col">

                <div>

                    <h3 class="card-title">
                        Catálogo de productos
                    </h3>

                    <div class="text-secondary small">
                        {{ $products->total() }}
                        producto(s)
                    </div>

                </div>

            </div>


            <div class="col-auto ms-auto">

                <form
                    action="{{ route(
                        'businesses.products.index',
                        $business
                    ) }}"
                    method="GET"
                >

                    <div class="input-group">

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="Nombre, SKU o código..."
                        >

                        <button
                            type="submit"
                            class="btn btn-outline-secondary"
                        >
                            <i class="ti ti-search"></i>
                        </button>

                        @if($search !== '')

                            <a
                                href="{{ route(
                                    'businesses.products.index',
                                    $business
                                ) }}"
                                class="btn btn-outline-secondary"
                                title="Limpiar búsqueda"
                            >
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
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Unidad</th>
                    <th>Costo</th>
                    <th>Precio</th>
                    <th>Estado</th>

                    @if($canUpdate || $canStatus)
                        <th class="w-1">
                            Acciones
                        </th>
                    @endif
                </tr>

            </thead>


            <tbody>

                @forelse($products as $product)

                    <tr>

                        <td>

                            <div class="fw-bold">
                                {{ $product->name }}
                            </div>


                            @if($product->sku)

                                <div class="text-secondary small">
                                    SKU:
                                    {{ $product->sku }}
                                </div>

                            @endif


                            @if($product->barcode)

                                <div class="text-secondary small">
                                    <i class="ti ti-barcode me-1"></i>
                                    {{ $product->barcode }}
                                </div>

                            @endif

                        </td>


                        <td>

                            @if($product->category)

                                <span class="badge bg-blue-lt">
                                    {{ $product->category->name }}
                                </span>

                            @else

                                <span class="text-secondary">
                                    Sin categoría
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($product->unit)

                                {{ $product->unit->name }}

                                @if($product->unit->symbol)

                                    <span class="text-secondary">
                                        ({{ $product->unit->symbol }})
                                    </span>

                                @endif

                            @else

                                <span class="text-secondary">
                                    Sin unidad
                                </span>

                            @endif

                        </td>


                        <td>

                            ${{ number_format(
                                (float) $product->cost,
                                2
                            ) }}

                        </td>


                        <td>

                            <div class="fw-bold">
                                ${{ number_format(
                                    (float) $product->sale_price,
                                    2
                                ) }}
                            </div>

                        </td>


                        <td>

                            @if($product->is_active)

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
                                                    'businesses.products.edit',
                                                    [
                                                        $business,
                                                        $product
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
                                                    'businesses.products.status',
                                                    [
                                                        $business,
                                                        $product
                                                    ]
                                                ) }}"
                                                method="POST"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <input
                                                    type="hidden"
                                                    name="is_active"
                                                    value="{{ $product->is_active ? 0 : 1 }}"
                                                >

                                                <button
                                                    type="submit"
                                                    class="dropdown-item {{ $product->is_active ? 'text-danger' : 'text-success' }}"
                                                >

                                                    <i
                                                        class="ti {{ $product->is_active ? 'ti-eye-off' : 'ti-eye' }} me-2"
                                                    ></i>

                                                    {{ $product->is_active
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
                            colspan="{{ ($canUpdate || $canStatus) ? 7 : 6 }}"
                            class="text-center text-secondary py-5"
                        >

                            <i class="ti ti-package-off fs-1 d-block mb-2"></i>

                            @if($search !== '')

                                No se encontraron productos para
                                "{{ $search }}".

                            @else

                                No hay productos registrados.

                            @endif

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($products->hasPages())

        <div class="card-footer">
            {{ $products->links() }}
        </div>

    @endif

</div>

@endsection