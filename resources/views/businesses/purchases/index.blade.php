@extends('layouts.app')

@section('title', 'Compras')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Compras
@endsection


@php
$canCreate = auth()
->user()
->hasBusinessPermission(
$business,
'purchases.create'
);
@endphp


@section('page-actions')

@if($canCreate)

<a
    href="{{ route(
            'businesses.purchases.create',
            $business
        ) }}"
    class="btn btn-primary">
    <i class="ti ti-plus me-2"></i>
    Nueva compra
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


<div class="mb-3">

    <span class="text-secondary">
        Sucursal:
    </span>

    <span class="badge bg-blue-lt">
        <i class="ti ti-building-store me-1"></i>
        {{ $branch->name }}
    </span>

</div>


<div class="card">

    <div class="card-header">

        <div class="row g-2 align-items-center w-100">

            <div class="col">

                <h3 class="card-title">
                    Historial de compras
                </h3>

                <div class="text-secondary small">
                    {{ $purchases->total() }}
                    compra(s)
                </div>

            </div>


            <div class="col-auto ms-auto">

                <form
                    action="{{ route(
                        'businesses.purchases.index',
                        $business
                    ) }}"
                    method="GET">

                    <div class="input-group">

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="Proveedor, referencia, producto...">

                        <button
                            type="submit"
                            class="btn btn-outline-secondary">
                            <i class="ti ti-search"></i>
                        </button>

                        @if($search !== '')

                        <a
                            href="{{ route(
                                    'businesses.purchases.index',
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
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Proveedor</th>
                    <th>Referencia</th>
                    <th>Productos</th>
                    <th>Total</th>
                    <th>Usuario</th>
                    <th class="w-1">Detalle</th>
                </tr>

            </thead>


            <tbody>

                @forelse($purchases as $purchase)

                <tr>

                    <td class="fw-bold">
                        #{{ $purchase->id }}
                    </td>


                    <td class="text-nowrap">

                        {{ $purchase
                                ->purchased_at
                                ->format('d/m/Y H:i')
                            }}

                    </td>


                    <td>

                        @if($purchase->supplier)

                        {{ $purchase->supplier->name }}

                        @else

                        <span class="text-secondary">
                            Sin proveedor
                        </span>

                        @endif

                    </td>


                    <td>

                        @if($purchase->reference)

                        {{ $purchase->reference }}

                        @else

                        <span class="text-secondary">
                            —
                        </span>

                        @endif

                    </td>


                    <td>
                        {{ $purchase->items_count }}
                    </td>


                    <td class="fw-bold">

                        ${{ number_format(
                                (float) $purchase->total,
                                2
                            ) }}

                    </td>


                    <td>
                        {{ $purchase->user?->name ?? 'Sistema' }}
                    </td>


                    <td>

                        <a
                            href="{{ route(
                                    'businesses.purchases.show',
                                    [
                                        $business,
                                        $purchase
                                    ]
                                ) }}"
                            class="btn btn-icon btn-outline-secondary"
                            title="Ver compra">
                            <i class="ti ti-eye"></i>
                        </a>

                    </td>

                </tr>

                @empty

                <tr>

                    <td
                        colspan="8"
                        class="text-center text-secondary py-5">

                        <i class="ti ti-shopping-cart-off fs-1 d-block mb-2"></i>

                        @if($search !== '')

                        No se encontraron compras para
                        "{{ $search }}".

                        @else

                        No hay compras registradas en esta sucursal.

                        @endif

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($purchases->hasPages())

    <div class="card-footer">
        {{ $purchases->links() }}
    </div>

    @endif

</div>

@endsection