@extends('layouts.app')

@section('title', 'Nuevo traspaso')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Nuevo traspaso
@endsection


@section('content')

@if($errors->any())

<div
    class="alert alert-danger alert-dismissible"
    role="alert">

    <div class="fw-bold mb-1">
        No se pudo realizar el traspaso.
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


<div class="row">

    <div class="col-lg-10">

        <form
            action="{{ route(
                'businesses.inventory.transfers.store',
                $business
            ) }}"
            method="POST"
            id="transferForm">

            @csrf


            <div class="card mb-3">

                <div class="card-header">

                    <h3 class="card-title">
                        Información del traspaso
                    </h3>

                </div>


                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">

                            <div class="mb-3">

                                <label class="form-label">
                                    Sucursal origen
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ $branch->name }}"
                                    readonly>

                                <small class="form-hint">
                                    Se utiliza automáticamente la sucursal activa.
                                </small>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="mb-3">

                                <label class="form-label">
                                    Sucursal destino
                                </label>

                                <select
                                    name="destination_branch_id"
                                    class="form-select @error('destination_branch_id') is-invalid @enderror"
                                    required>

                                    <option value="">
                                        Selecciona una sucursal
                                    </option>

                                    @foreach($destinationBranches as $destinationBranch)

                                    <option
                                        value="{{ $destinationBranch->id }}"
                                        @selected(
                                        old('destination_branch_id')==$destinationBranch->id
                                        )
                                        >
                                        {{ $destinationBranch->name }}
                                    </option>

                                    @endforeach

                                </select>

                                @error('destination_branch_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                        </div>

                    </div>


                    <div class="mb-0">

                        <label class="form-label">
                            Notas
                        </label>

                        <textarea
                            name="notes"
                            class="form-control @error('notes') is-invalid @enderror"
                            rows="3"
                            placeholder="Ej. Reposición de inventario para sucursal centro">{{ old('notes') }}</textarea>

                        @error('notes')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                </div>

            </div>


            <div class="card">

                <div class="card-header">

                    <div class="row align-items-center w-100">

                        <div class="col">

                            <h3 class="card-title">
                                Productos
                            </h3>

                            <div class="text-secondary small">
                                Selecciona los productos y cantidades a transferir.
                            </div>

                        </div>

                        <div class="col-auto">

                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                id="addProductButton">
                                <i class="ti ti-plus me-2"></i>
                                Agregar producto
                            </button>

                        </div>

                    </div>

                </div>


                <div class="card-body">

                    @if($products->isEmpty())

                    <div class="text-center text-secondary py-4">

                        <i class="ti ti-package-off fs-1 d-block mb-2"></i>

                        No hay productos con existencia disponible en
                        {{ $branch->name }}.

                    </div>

                    @else

                    <div
                        id="transferItems"
                        class="d-flex flex-column gap-3"></div>

                    @endif

                </div>


                @if($products->isNotEmpty())

                <div class="card-footer text-end">

                    <a
                        href="{{ route(
                                'businesses.inventory.transfers.index',
                                $business
                            ) }}"
                        class="btn btn-outline-secondary me-2">
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="ti ti-arrows-exchange me-2"></i>
                        Realizar traspaso
                    </button>

                </div>

                @endif

            </div>

        </form>

    </div>

</div>


@if($products->isNotEmpty())

<template id="transferItemTemplate">

    <div
        class="border rounded p-3 transfer-item"
        data-index="__INDEX__">

        <div class="row align-items-end">

            <div class="col-md-7">

                <div class="mb-3 mb-md-0">

                    <label class="form-label">
                        Producto
                    </label>

                    <select
                        name="items[__INDEX__][product_id]"
                        class="form-select product-select"
                        required>

                        <option value="">
                            Selecciona un producto
                        </option>

                        @foreach($products as $product)

                        @php
                        $inventory = $product
                        ->inventories
                        ->first();

                        $available = (float) (
                        $inventory?->quantity
                        ?? 0
                        );
                        @endphp

                        <option
                            value="{{ $product->id }}"
                            data-stock="{{ $available }}"
                            data-unit="{{ $product->unit?->symbol ?? '' }}">
                            {{ $product->name }}

                            @if($product->sku)
                            - {{ $product->sku }}
                            @endif

                            (Disponible:
                            {{ number_format($available, 3) }}

                            @if($product->unit?->symbol)
                            {{ $product->unit->symbol }}
                            @endif
                            )
                        </option>

                        @endforeach

                    </select>

                </div>

            </div>


            <div class="col-md-3">

                <div class="mb-3 mb-md-0">

                    <label class="form-label">
                        Cantidad
                    </label>

                    <input
                        type="number"
                        name="items[__INDEX__][quantity]"
                        class="form-control quantity-input"
                        min="0.001"
                        step="0.001"
                        required>

                    <small class="form-hint stock-hint">
                        Selecciona un producto.
                    </small>

                </div>

            </div>


            <div class="col-md-2 text-end">

                <button
                    type="button"
                    class="btn btn-outline-danger remove-item"
                    title="Quitar producto">
                    <i class="ti ti-trash"></i>
                </button>

            </div>

        </div>

    </div>

</template>


<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const container =
                document.getElementById(
                    'transferItems'
                );

            const template =
                document.getElementById(
                    'transferItemTemplate'
                );

            const addButton =
                document.getElementById(
                    'addProductButton'
                );

            let index = 0;


            function addItem() {

                const html = template.innerHTML
                    .replaceAll(
                        '__INDEX__',
                        index
                    );

                container.insertAdjacentHTML(
                    'beforeend',
                    html
                );

                index++;
            }


            addButton.addEventListener(
                'click',
                addItem
            );


            container.addEventListener(
                'click',
                function(event) {

                    const button =
                        event.target.closest(
                            '.remove-item'
                        );

                    if (!button) {
                        return;
                    }

                    button
                        .closest('.transfer-item')
                        .remove();

                    if (
                        container.children.length === 0
                    ) {
                        addItem();
                    }
                }
            );


            container.addEventListener(
                'change',
                function(event) {

                    if (
                        !event.target.classList.contains(
                            'product-select'
                        )
                    ) {
                        return;
                    }

                    const select =
                        event.target;

                    const item =
                        select.closest(
                            '.transfer-item'
                        );

                    const quantityInput =
                        item.querySelector(
                            '.quantity-input'
                        );

                    const hint =
                        item.querySelector(
                            '.stock-hint'
                        );

                    const selectedOption =
                        select.options[
                            select.selectedIndex
                        ];

                    const stock =
                        selectedOption.dataset.stock;

                    const unit =
                        selectedOption.dataset.unit;

                    if (!stock) {

                        quantityInput.removeAttribute(
                            'max'
                        );

                        hint.textContent =
                            'Selecciona un producto.';

                        return;
                    }

                    quantityInput.max = stock;

                    hint.textContent =
                        'Disponible: ' +
                        Number(stock)
                        .toFixed(3) +
                        (
                            unit ?
                            ' ' + unit :
                            ''
                        );
                }
            );


            /*
             * Al abrir la pantalla agregamos
             * automáticamente la primera fila.
             */
            addItem();
        }
    );
</script>

@endif

@endsection