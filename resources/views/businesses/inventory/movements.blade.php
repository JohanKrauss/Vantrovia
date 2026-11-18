@extends('layouts.app')

@section('title', 'Movimientos de inventario')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Movimientos de inventario
@endsection


@section('page-actions')

<a
    href="{{ route(
        'businesses.inventory.index',
        $business
    ) }}"
    class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-2"></i>
    Volver al inventario
</a>

@endsection


@section('content')

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

        <div>

            <h3 class="card-title">
                Historial de movimientos
            </h3>

            <div class="text-secondary small">
                {{ $movements->total() }}
                movimiento(s)
            </div>

        </div>

    </div>


    <div class="table-responsive-lg">

        <table class="table table-vcenter card-table">

            <thead>

                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Anterior</th>
                    <th>Nueva</th>
                    <th>Usuario</th>
                    <th>Nota</th>
                </tr>

            </thead>


            <tbody>

                @forelse($movements as $movement)

                @php

                $isEntry = in_array(
                $movement->type,
                [
                'initial',
                'adjustment_in',
                'purchase',
                'return_in',
                'transfer_in',
                ]
                );

                $typeLabels = [
                'initial' => 'Inicial',
                'adjustment_in' => 'Entrada',
                'adjustment_out' => 'Salida',
                'purchase' => 'Compra',
                'sale' => 'Venta',
                'return_in' => 'Devolución entrada',
                'return_out' => 'Devolución salida',
                'transfer_in' => 'Traspaso entrada',
                'transfer_out' => 'Traspaso salida',
                ];

                @endphp


                <tr>

                    <td class="text-nowrap">

                        {{ $movement
                                ->created_at
                                ->format('d/m/Y H:i')
                            }}

                    </td>


                    <td>

                        <div class="fw-bold">
                            {{ $movement
                                    ->inventory
                                    ->product
                                    ->name
                                }}
                        </div>


                        @if(
                        $movement
                        ->inventory
                        ->product
                        ->sku
                        )

                        <div class="text-secondary small">
                            SKU:
                            {{ $movement
                                        ->inventory
                                        ->product
                                        ->sku
                                    }}
                        </div>

                        @endif

                    </td>


                    <td>

                        <span
                            class="badge {{ $isEntry ? 'bg-green-lt' : 'bg-red-lt' }}">

                            {{ $typeLabels[
                                    $movement->type
                                ] ?? $movement->type }}

                        </span>

                    </td>


                    <td>

                        <span
                            class="{{ $isEntry ? 'text-success' : 'text-danger' }} fw-bold">

                            {{ $isEntry ? '+' : '-' }}

                            {{ number_format(
                                    (float) $movement->quantity,
                                    3
                                ) }}

                            @if(
                            $movement
                            ->inventory
                            ->product
                            ->unit?->symbol
                            )

                            {{ $movement
                                        ->inventory
                                        ->product
                                        ->unit
                                        ->symbol
                                    }}

                            @endif

                        </span>

                    </td>


                    <td>

                        {{ number_format(
                                (float) $movement->previous_quantity,
                                3
                            ) }}

                    </td>


                    <td class="fw-bold">

                        {{ number_format(
                                (float) $movement->new_quantity,
                                3
                            ) }}

                    </td>


                    <td>

                        {{ $movement->user?->name
                                ?? 'Sistema'
                            }}

                    </td>


                    <td>

                        @if($movement->notes)

                        {{ $movement->notes }}

                        @else

                        <span class="text-secondary">
                            —
                        </span>

                        @endif

                    </td>

                </tr>

                @empty

                <tr>

                    <td
                        colspan="8"
                        class="text-center text-secondary py-5">

                        <i class="ti ti-history-off fs-1 d-block mb-2"></i>

                        No hay movimientos registrados en esta sucursal.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($movements->hasPages())

    <div class="card-footer">
        {{ $movements->links() }}
    </div>

    @endif

</div>

@endsection