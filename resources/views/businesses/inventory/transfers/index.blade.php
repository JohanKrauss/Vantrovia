@extends('layouts.app')

@section('title', 'Traspasos')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Traspasos de inventario
@endsection


@php
$canCreate = auth()
->user()
->hasBusinessPermission(
$business,
'inventory.transfers.create'
);
@endphp


@section('page-actions')

<div class="d-flex gap-2">

    <a
        href="{{ route(
            'businesses.inventory.index',
            $business
        ) }}"
        class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-2"></i>
        Inventario
    </a>

    @if($canCreate)

    <a
        href="{{ route(
                'businesses.inventory.transfers.create',
                $business
            ) }}"
        class="btn btn-primary">
        <i class="ti ti-arrows-exchange me-2"></i>
        Nuevo traspaso
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
        data-bs-dismiss="alert"></button>
</div>

@endif


<div class="mb-3">

    <span class="text-secondary">
        Sucursal activa:
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
                Historial de traspasos
            </h3>

            <div class="text-secondary small">
                {{ $transfers->total() }}
                traspaso(s)
            </div>
        </div>

    </div>


    <div class="table-responsive-lg">

        <table class="table table-vcenter card-table">

            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Productos</th>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th class="w-1">Detalle</th>
                </tr>
            </thead>

            <tbody>

                @forelse($transfers as $transfer)

                <tr>

                    <td class="fw-bold">
                        #{{ $transfer->id }}
                    </td>

                    <td class="text-nowrap">
                        {{ $transfer->transferred_at->format('d/m/Y H:i') }}
                    </td>

                    <td>

                        <span
                            class="{{ $transfer->source_branch_id === $branch->id ? 'fw-bold' : '' }}">
                            {{ $transfer->sourceBranch->name }}
                        </span>

                        @if($transfer->source_branch_id === $branch->id)
                        <div class="text-secondary small">
                            Sucursal actual
                        </div>
                        @endif

                    </td>

                    <td>

                        <span
                            class="{{ $transfer->destination_branch_id === $branch->id ? 'fw-bold' : '' }}">
                            {{ $transfer->destinationBranch->name }}
                        </span>

                        @if($transfer->destination_branch_id === $branch->id)
                        <div class="text-secondary small">
                            Sucursal actual
                        </div>
                        @endif

                    </td>

                    <td>
                        {{ $transfer->items_count }}
                    </td>

                    <td>
                        {{ $transfer->user?->name ?? 'Sistema' }}
                    </td>

                    <td>

                        @if($transfer->status === 'completed')

                        <span class="badge bg-green-lt">
                            Completado
                        </span>

                        @else

                        <span class="badge bg-yellow-lt">
                            {{ ucfirst($transfer->status) }}
                        </span>

                        @endif

                    </td>

                    <td>

                        <a
                            href="{{ route(
                                    'businesses.inventory.transfers.show',
                                    [
                                        $business,
                                        $transfer
                                    ]
                                ) }}"
                            class="btn btn-icon btn-outline-secondary"
                            title="Ver traspaso">
                            <i class="ti ti-eye"></i>
                        </a>

                    </td>

                </tr>

                @empty

                <tr>
                    <td
                        colspan="8"
                        class="text-center text-secondary py-5">
                        <i class="ti ti-arrows-exchange fs-1 d-block mb-2"></i>

                        No hay traspasos relacionados con esta sucursal.
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($transfers->hasPages())

    <div class="card-footer">
        {{ $transfers->links() }}
    </div>

    @endif

</div>

@endsection