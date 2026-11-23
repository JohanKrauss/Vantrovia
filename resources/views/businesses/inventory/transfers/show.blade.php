@extends('layouts.app')

@section('title', 'Detalle del traspaso')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Traspaso #{{ $transfer->id }}
@endsection


@section('page-actions')

<a
    href="{{ route(
        'businesses.inventory.transfers.index',
        $business
    ) }}"
    class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i>
    Volver
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

    <div class="col-lg-8">

        <div class="card mb-3">

            <div class="card-header">

                <h3 class="card-title">
                    Información del traspaso
                </h3>

            </div>


            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="text-secondary small">
                            Sucursal origen
                        </div>

                        <div class="fw-bold fs-3">

                            <i class="ti ti-building-store me-1"></i>

                            {{ $transfer->sourceBranch->name }}

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="text-secondary small">
                            Sucursal destino
                        </div>

                        <div class="fw-bold fs-3">

                            <i class="ti ti-building-store me-1"></i>

                            {{ $transfer->destinationBranch->name }}

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="text-secondary small">
                            Fecha
                        </div>

                        <div class="fw-bold">
                            {{ $transfer->transferred_at->format('d/m/Y H:i') }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="text-secondary small">
                            Realizado por
                        </div>

                        <div class="fw-bold">
                            {{ $transfer->user?->name ?? 'Sistema' }}
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="text-secondary small">
                            Estado
                        </div>

                        <div>

                            @if($transfer->status === 'completed')

                            <span class="badge bg-green-lt">
                                Completado
                            </span>

                            @else

                            <span class="badge bg-yellow-lt">
                                {{ ucfirst($transfer->status) }}
                            </span>

                            @endif

                        </div>

                    </div>

                </div>


                @if($transfer->notes)

                <hr>

                <div>

                    <div class="text-secondary small mb-1">
                        Notas
                    </div>

                    <div>
                        {{ $transfer->notes }}
                    </div>

                </div>

                @endif

            </div>

        </div>


        <div class="card">

            <div class="card-header">

                <div>

                    <h3 class="card-title">
                        Productos transferidos
                    </h3>

                    <div class="text-secondary small">
                        {{ $transfer->items->count() }}
                        producto(s)
                    </div>

                </div>

            </div>


            <div class="table-responsive-lg">

                <table class="table table-vcenter card-table">

                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>SKU</th>
                            <th>Unidad</th>
                            <th class="text-end">
                                Cantidad
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($transfer->items as $item)

                        <tr>

                            <td class="fw-bold">
                                {{ $item->product->name }}
                            </td>

                            <td>

                                @if($item->product->sku)

                                {{ $item->product->sku }}

                                @else

                                <span class="text-secondary">
                                    —
                                </span>

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

                            <td class="text-end fw-bold">

                                {{ number_format(
                                        (float) $item->quantity,
                                        3
                                    ) }}

                                @if($item->product->unit?->symbol)

                                {{ $item->product->unit->symbol }}

                                @endif

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection