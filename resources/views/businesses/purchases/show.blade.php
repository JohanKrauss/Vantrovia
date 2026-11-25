@extends('layouts.app')

@section('title', 'Detalle de compra')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Compra #{{ $purchase->id }}
@endsection


@section('page-actions')

<a
    href="{{ route(
        'businesses.purchases.index',
        $business
    ) }}"
    class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i>
    Volver a compras
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


<div class="row">

    <div class="col-lg-9">

        <div class="card mb-3">

            <div class="card-header">

                <div>

                    <h3 class="card-title">
                        Información de la compra
                    </h3>

                    <div class="text-secondary small">
                        Folio interno #{{ $purchase->id }}
                    </div>

                </div>

            </div>


            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-4">

                        <div class="text-secondary small">
                            Sucursal
                        </div>

                        <div class="fw-bold">
                            <i class="ti ti-building-store me-1"></i>
                            {{ $purchase->branch->name }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="text-secondary small">
                            Proveedor
                        </div>

                        <div class="fw-bold">
                            {{ $purchase->supplier?->name ?? 'Sin proveedor' }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="text-secondary small">
                            Referencia
                        </div>

                        <div class="fw-bold">
                            {{ $purchase->reference ?? '—' }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="text-secondary small">
                            Fecha de compra
                        </div>

                        <div class="fw-bold">
                            {{ $purchase
                                ->purchased_at
                                ->format('d/m/Y H:i')
                            }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="text-secondary small">
                            Registrado por
                        </div>

                        <div class="fw-bold">
                            {{ $purchase->user?->name ?? 'Sistema' }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="text-secondary small">
                            Estado
                        </div>

                        <span class="badge bg-green-lt">
                            Completada
                        </span>

                    </div>

                </div>


                @if($purchase->notes)

                <hr>

                <div>

                    <div class="text-secondary small mb-1">
                        Notas
                    </div>

                    <div>
                        {{ $purchase->notes }}
                    </div>

                </div>

                @endif

            </div>

        </div>


        <div class="card">

            <div class="card-header">

                <div>

                    <h3 class="card-title">
                        Productos
                    </h3>

                    <div class="text-secondary small">
                        {{ $purchase->items->count() }}
                        producto(s)
                    </div>

                </div>

            </div>


            <div class="table-responsive-lg">

                <table class="table table-vcenter card-table">

                    <thead>

                        <tr>
                            <th>Producto</th>
                            <th>Unidad</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Costo unitario</th>
                            <th class="text-end">Subtotal</th>
                        </tr>

                    </thead>


                    <tbody>

                        @foreach($purchase->items as $item)

                        <tr>

                            <td>

                                <div class="fw-bold">
                                    {{ $item->product->name }}
                                </div>

                                @if($item->product->sku)

                                <div class="text-secondary small">
                                    SKU:
                                    {{ $item->product->sku }}
                                </div>

                                @endif

                            </td>


                            <td>

                                @if($item->product->unit)

                                {{ $item->product->unit->name }}

                                @if($item->product->unit->symbol)

                                <span class="text-secondary">
                                    ({{ $item->product->unit->symbol }})
                                </span>

                                @endif

                                @else

                                <span class="text-secondary">
                                    —
                                </span>

                                @endif

                            </td>


                            <td class="text-end">

                                {{ number_format(
                                        (float) $item->quantity,
                                        3
                                    ) }}

                            </td>


                            <td class="text-end">

                                ${{ number_format(
                                        (float) $item->unit_cost,
                                        2
                                    ) }}

                            </td>


                            <td class="text-end fw-bold">

                                ${{ number_format(
                                        (float) $item->subtotal,
                                        2
                                    ) }}

                            </td>

                        </tr>

                        @endforeach

                    </tbody>


                    <tfoot>

                        <tr>

                            <td
                                colspan="4"
                                class="text-end fw-bold">
                                Total
                            </td>

                            <td class="text-end fs-3 fw-bold">

                                ${{ number_format(
                                    (float) $purchase->total,
                                    2
                                ) }}

                            </td>

                        </tr>

                    </tfoot>

                </table>

            </div>  

        </div>

    </div>

</div>

@endsection