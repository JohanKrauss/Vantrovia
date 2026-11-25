@extends('layouts.app')

@section('title', 'Nueva compra')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Nueva compra
@endsection


@section('content')

@if($errors->any())

<div
    class="alert alert-danger alert-dismissible"
    role="alert">

    <div class="fw-bold mb-1">
        No se pudo registrar la compra.
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


<form
    action="{{ route(
        'businesses.purchases.store',
        $business
    ) }}"
    method="POST"
    id="purchaseForm">

    @csrf


    <div class="row">

        <div class="col-lg-9">

            <div class="card mb-3">

                <div class="card-header">

                    <h3 class="card-title">
                        Información de la compra
                    </h3>

                </div>


                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">

                            <div class="mb-3">

                                <label class="form-label">
                                    Sucursal
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ $branch->name }}"
                                    readonly>

                                <small class="form-hint">
                                    La compra ingresará existencias a esta sucursal.
                                </small>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="mb-3">

                                <label class="form-label">
                                    Proveedor
                                </label>

                                <select
                                    name="supplier_id"
                                    class="form-select @error('supplier_id') is-invalid @enderror">

                                    <option value="">
                                        Sin proveedor
                                    </option>

                                    @foreach($suppliers as $supplier)

                                    <option
                                        value="{{ $supplier->id }}"
                                        @selected(
                                        old('supplier_id')==$supplier->id
                                        )
                                        >
                                        {{ $supplier->name }}
                                    </option>

                                    @endforeach

                                </select>

                                @error('supplier_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-6">

                            <div class="mb-3">

                                <label class="form-label">
                                    Referencia
                                </label>

                                <input
                                    type="text"
                                    name="reference"
                                    value="{{ old('reference') }}"
                                    class="form-control @error('reference') is-invalid @enderror"
                                    placeholder="Ej. FACT-1025">

                                @error('reference')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="mb-3">

                                <label class="form-label">
                                    Fecha de compra
                                </label>

                                <input
                                    type="datetime-local"
                                    name="purchased_at"
                                    value="{{ old(
                                        'purchased_at',
                                        now()->format('Y-m-d\TH:i')
                                    ) }}"
                                    class="form-control @error('purchased_at') is-invalid @enderror"
                                    required>

                                @error('purchased_at')
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
                            rows="3"
                            class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Información adicional de la compra...">{{ old('notes') }}</textarea>

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
                                Agrega los productos incluidos en la compra.
                            </div>

                        </div>


                        <div class="col-auto">

                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                id="addItemButton">
                                <i class="ti ti-plus me-2"></i>
                                Agregar producto
                            </button>

                        </div>

                    </div>

                </div>


                <div class="card-body">

                    @if($products->isEmpty())

                    <div class="text-center text-secondary py-5">

                        <i class="ti ti-package-off fs-1 d-block mb-2"></i>

                        No hay productos activos disponibles.

                    </div>

                    @else

                    <div
                        id="purchaseItems"
                        class="d-flex flex-column gap-3"></div>

                    @endif

                </div>


                @if($products->isNotEmpty())

                <div class="card-footer">

                    <div class="row align-items-center">

                        <div class="col">

                            <a
                                href="{{ route(
                                        'businesses.purchases.index',
                                        $business
                                    ) }}"
                                class="btn btn-outline-secondary">
                                Cancelar
                            </a>

                        </div>


                        <div class="col-auto">

                            <div class="text-secondary text-end">
                                Total de compra
                            </div>

                            <div
                                class="fs-2 fw-bold text-end"
                                id="purchaseTotal">
                                $0.00
                            </div>

                        </div>


                        <div class="col-auto">

                            <button
                                type="submit"
                                class="btn btn-primary">
                                <i class="ti ti-device-floppy me-2"></i>
                                Registrar compra
                            </button>

                        </div>

                    </div>

                </div>

                @endif

            </div>

        </div>

    </div>

</form>


@if($products->isNotEmpty())

<template id="purchaseItemTemplate">

    <div
        class="border rounded p-3 purchase-item"
        data-index="__INDEX__">

        <div class="row align-items-end">

            <div class="col-md-5">

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

                        <option
                            value="{{ $product->id }}"
                            data-unit="{{ $product->unit?->symbol ?? '' }}">
                            {{ $product->name }}

                            @if($product->sku)
                            - {{ $product->sku }}
                            @endif

                        </option>

                        @endforeach

                    </select>

                </div>

            </div>


            <div class="col-md-2">

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

                    <small class="form-hint unit-hint"></small>

                </div>

            </div>


            <div class="col-md-2">

                <div class="mb-3 mb-md-0">

                    <label class="form-label">
                        Costo unitario
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            $
                        </span>

                        <input
                            type="number"
                            name="items[__INDEX__][unit_cost]"
                            class="form-control cost-input"
                            min="0"
                            step="0.01"
                            required>

                    </div>

                </div>

            </div>


            <div class="col-md-2">

                <div class="mb-3 mb-md-0">

                    <label class="form-label">
                        Subtotal
                    </label>

                    <input
                        type="text"
                        class="form-control subtotal-display"
                        value="$0.00"
                        readonly>

                </div>

            </div>


            <div class="col-md-1 text-end">

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
                    'purchaseItems'
                );

            const template =
                document.getElementById(
                    'purchaseItemTemplate'
                );

            const addButton =
                document.getElementById(
                    'addItemButton'
                );

            const totalDisplay =
                document.getElementById(
                    'purchaseTotal'
                );

            let index = 0;


            function calculateItem(item) {

                const quantity =
                    Number(
                        item
                        .querySelector(
                            '.quantity-input'
                        )
                        .value
                    ) || 0;

                const cost =
                    Number(
                        item
                        .querySelector(
                            '.cost-input'
                        )
                        .value
                    ) || 0;

                const subtotal =
                    quantity * cost;

                item
                    .querySelector(
                        '.subtotal-display'
                    )
                    .value =
                    '$' +
                    subtotal.toFixed(2);

                return subtotal;
            }


            function calculateTotal() {

                let total = 0;

                container
                    .querySelectorAll(
                        '.purchase-item'
                    )
                    .forEach(
                        function(item) {
                            total +=
                                calculateItem(
                                    item
                                );
                        }
                    );

                totalDisplay.textContent =
                    '$' +
                    total.toFixed(2);
            }


            function addItem() {

                const html =
                    template.innerHTML
                    .replaceAll(
                        '__INDEX__',
                        index
                    );

                container.insertAdjacentHTML(
                    'beforeend',
                    html
                );

                index++;

                calculateTotal();
            }


            addButton.addEventListener(
                'click',
                addItem
            );


            container.addEventListener(
                'input',
                function(event) {

                    if (
                        event.target.classList.contains(
                            'quantity-input'
                        ) ||
                        event.target.classList.contains(
                            'cost-input'
                        )
                    ) {
                        calculateTotal();
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
                            '.purchase-item'
                        );

                    const selectedOption =
                        select.options[
                            select.selectedIndex
                        ];

                    const unit =
                        selectedOption.dataset.unit ||
                        '';

                    const hint =
                        item.querySelector(
                            '.unit-hint'
                        );

                    hint.textContent =
                        unit ?
                        'Unidad: ' + unit :
                        '';

                    calculateTotal();
                }
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
                        .closest(
                            '.purchase-item'
                        )
                        .remove();

                    if (
                        container.children.length ===
                        0
                    ) {
                        addItem();
                    }

                    calculateTotal();
                }
            );


            addItem();
        }
    );
</script>

@endif

@endsection