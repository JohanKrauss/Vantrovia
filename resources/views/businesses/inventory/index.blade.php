@extends('layouts.app')

@section('title', 'Inventario')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Inventario
@endsection


@php

$canAdjust = auth()
->user()
->hasBusinessPermission(
$business,
'inventory.adjust'
);

$canMinimumStock = auth()
->user()
->hasBusinessPermission(
$business,
'inventory.minimum-stock'
);

$canMovements = auth()
->user()
->hasBusinessPermission(
$business,
'inventory.movements'
);

@endphp


@section('page-actions')

<div class="d-flex gap-2">

    @if($canMovements)

    <a
        href="{{ route(
                'businesses.inventory.movements',
                $business
            ) }}"
        class="btn btn-outline-secondary">
        <i class="ti ti-history me-2"></i>
        Movimientos
    </a>

    @endif


    @if(
    auth()
    ->user()
    ->hasBusinessPermission(
    $business,
    'inventory.transfers.view'
    )
    )

    <a
        href="{{ route(
                'businesses.inventory.transfers.index',
                $business
            ) }}"
        class="btn btn-outline-primary">
        <i class="ti ti-arrows-exchange me-2"></i>
        Traspasos
    </a>

    @endif

</div>

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


@if($errors->any())

<div
    class="alert alert-danger alert-dismissible"
    role="alert">

    <div class="fw-bold mb-1">
        No se pudo realizar la operación.
    </div>

    <ul class="mb-0">

        @foreach($errors->all() as $error)

        <li>
            {{ $error }}
        </li>

        @endforeach

    </ul>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"></button>

</div>

@endif


<div class="mb-3">

    <div class="d-flex align-items-center gap-2">

        <span class="text-secondary">
            Sucursal:
        </span>

        <span class="badge bg-blue-lt">
            <i class="ti ti-building-store me-1"></i>
            {{ $branch->name }}
        </span>

    </div>

</div>


<div class="card">

    <div class="card-header">

        <div class="row g-2 align-items-center w-100">

            <div class="col">

                <h3 class="card-title">
                    Existencias
                </h3>

                <div class="text-secondary small">
                    {{ $products->total() }}
                    producto(s)
                </div>

            </div>


            <div class="col-auto ms-auto">

                <form
                    action="{{ route(
                        'businesses.inventory.index',
                        $business
                    ) }}"
                    method="GET">

                    <div class="input-group">

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="Nombre, SKU o código...">

                        <button
                            type="submit"
                            class="btn btn-outline-secondary">
                            <i class="ti ti-search"></i>
                        </button>


                        @if($search !== '')

                        <a
                            href="{{ route(
                                    'businesses.inventory.index',
                                    $business
                                ) }}"
                            class="btn btn-outline-secondary"
                            title="Limpiar búsqueda">
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
                    <th>Unidad</th>
                    <th>Existencia</th>
                    <th>Stock mínimo</th>
                    <th>Estado</th>

                    @if($canAdjust || $canMinimumStock)
                    <th class="w-1">
                        Acciones
                    </th>
                    @endif
                </tr>

            </thead>


            <tbody>

                @forelse($products as $product)

                @php

                $inventory = $product
                ->inventories
                ->first();

                $quantity = (float) (
                $inventory?->quantity
                ?? 0
                );

                $minimumStock = (float) (
                $inventory?->minimum_stock
                ?? 0
                );

                $isLowStock =
                $minimumStock > 0
                && $quantity <= $minimumStock;

                    @endphp


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


                        @if(!$product->is_active)

                        <div class="mt-1">

                            <span class="badge bg-red-lt">
                                Producto inactivo
                            </span>

                        </div>

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

                        <div class="fw-bold fs-3">

                            {{ number_format(
                                    $quantity,
                                    3
                                ) }}

                            @if($product->unit?->symbol)

                            <span class="fs-5 text-secondary">
                                {{ $product->unit->symbol }}
                            </span>

                            @endif

                        </div>

                    </td>


                    <td>

                        {{ number_format(
                                $minimumStock,
                                3
                            ) }}

                        @if($product->unit?->symbol)

                        <span class="text-secondary">
                            {{ $product->unit->symbol }}
                        </span>

                        @endif

                    </td>


                    <td>

                        @if($quantity <= 0)

                            <span class="badge bg-red-lt">
                            Sin existencia
                            </span>

                            @elseif($isLowStock)

                            <span class="badge bg-yellow-lt">
                                <i class="ti ti-alert-triangle me-1"></i>
                                Stock bajo
                            </span>

                            @else

                            <span class="badge bg-green-lt">
                                Disponible
                            </span>

                            @endif

                    </td>


                    @if($canAdjust || $canMinimumStock)

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

                                @if(
                                $canAdjust
                                && $product->is_active
                                )

                                <button
                                    type="button"
                                    class="dropdown-item text-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#adjustInventoryModal"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-adjustment-type="adjustment_in">
                                    <i class="ti ti-plus me-2"></i>
                                    Registrar entrada
                                </button>


                                <button
                                    type="button"
                                    class="dropdown-item text-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#adjustInventoryModal"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-adjustment-type="adjustment_out">
                                    <i class="ti ti-minus me-2"></i>
                                    Registrar salida
                                </button>

                                @endif


                                @if(
                                $canAdjust
                                && $canMinimumStock
                                && $product->is_active
                                )

                                <div class="dropdown-divider"></div>

                                @endif


                                @if($canMinimumStock)

                                <button
                                    type="button"
                                    class="dropdown-item"
                                    data-bs-toggle="modal"
                                    data-bs-target="#minimumStockModal"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-minimum-stock="{{ $minimumStock }}">
                                    <i class="ti ti-alert-triangle me-2"></i>
                                    Stock mínimo
                                </button>

                                @endif

                            </div>

                        </div>

                    </td>

                    @endif

                    </tr>


                    @empty

                    <tr>

                        <td
                            colspan="{{ ($canAdjust || $canMinimumStock) ? 6 : 5 }}"
                            class="text-center text-secondary py-5">

                            <i class="ti ti-package-off fs-1 d-block mb-2"></i>

                            @if($search !== '')

                            No se encontraron productos para
                            "{{ $search }}".

                            @else

                            No hay productos disponibles.

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


@if($canAdjust)

<div
    class="modal modal-blur fade"
    id="adjustInventoryModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                id="adjustInventoryForm"
                method="POST">

                @csrf

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="adjustInventoryTitle">
                        Ajustar inventario
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>


                <div class="modal-body">

                    <input
                        type="hidden"
                        name="type"
                        id="adjustmentType">


                    <div class="mb-3">

                        <label class="form-label">
                            Producto
                        </label>

                        <input
                            type="text"
                            id="adjustProductName"
                            class="form-control"
                            readonly>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Cantidad
                        </label>

                        <input
                            type="number"
                            name="quantity"
                            class="form-control"
                            min="0.001"
                            step="0.001"
                            required>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Nota
                        </label>

                        <textarea
                            name="notes"
                            class="form-control"
                            rows="3"
                            placeholder="Motivo del ajuste..."></textarea>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="adjustInventorySubmit">
                        Guardar
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endif


@if($canMinimumStock)

<div
    class="modal modal-blur fade"
    id="minimumStockModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form
                id="minimumStockForm"
                method="POST">

                @csrf
                @method('PATCH')


                <div class="modal-header">

                    <h5 class="modal-title">
                        Configurar stock mínimo
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>


                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Producto
                        </label>

                        <input
                            type="text"
                            id="minimumProductName"
                            class="form-control"
                            readonly>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Stock mínimo
                        </label>

                        <input
                            type="number"
                            name="minimum_stock"
                            id="minimumStockValue"
                            class="form-control"
                            min="0"
                            step="0.001"
                            required>

                        <small class="form-hint">
                            Se mostrará una alerta cuando la existencia sea igual o menor a este valor.
                        </small>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        Guardar
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endif


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const adjustModal = document.getElementById(
            'adjustInventoryModal'
        );

        if (adjustModal) {

            adjustModal.addEventListener(
                'show.bs.modal',
                function(event) {

                    const button = event.relatedTarget;

                    const productId =
                        button.getAttribute(
                            'data-product-id'
                        );

                    const productName =
                        button.getAttribute(
                            'data-product-name'
                        );

                    const type =
                        button.getAttribute(
                            'data-adjustment-type'
                        );

                    const form =
                        document.getElementById(
                            'adjustInventoryForm'
                        );

                    form.action =
                        "{{ url('/businesses/'.$business->id.'/inventory') }}" +
                        '/' +
                        productId +
                        '/adjust';

                    document.getElementById(
                        'adjustProductName'
                    ).value = productName;

                    document.getElementById(
                        'adjustmentType'
                    ).value = type;

                    const title =
                        document.getElementById(
                            'adjustInventoryTitle'
                        );

                    const submit =
                        document.getElementById(
                            'adjustInventorySubmit'
                        );

                    if (type === 'adjustment_in') {

                        title.textContent =
                            'Registrar entrada';

                        submit.textContent =
                            'Registrar entrada';

                    } else {

                        title.textContent =
                            'Registrar salida';

                        submit.textContent =
                            'Registrar salida';

                    }
                }
            );
        }


        const minimumModal = document.getElementById(
            'minimumStockModal'
        );

        if (minimumModal) {

            minimumModal.addEventListener(
                'show.bs.modal',
                function(event) {

                    const button = event.relatedTarget;

                    const productId =
                        button.getAttribute(
                            'data-product-id'
                        );

                    const productName =
                        button.getAttribute(
                            'data-product-name'
                        );

                    const minimumStock =
                        button.getAttribute(
                            'data-minimum-stock'
                        );

                    const form =
                        document.getElementById(
                            'minimumStockForm'
                        );

                    form.action =
                        "{{ url('/businesses/'.$business->id.'/inventory') }}" +
                        '/' +
                        productId +
                        '/minimum-stock';

                    document.getElementById(
                        'minimumProductName'
                    ).value = productName;

                    document.getElementById(
                        'minimumStockValue'
                    ).value = minimumStock;
                }
            );
        }

    });
</script>

@endsection