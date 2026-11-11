@extends('layouts.app')

@section('title', 'Categorías')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Categorías
@endsection


@php

$canCreate = auth()
->user()
->hasBusinessPermission(
$business,
'categories.create'
);

$canUpdate = auth()
->user()
->hasBusinessPermission(
$business,
'categories.update'
);

$canStatus = auth()
->user()
->hasBusinessPermission(
$business,
'categories.status'
);

@endphp


@section('page-actions')

@if($canCreate)

<a
    href="{{ route(
            'businesses.categories.create',
            $business
        ) }}"
    class="btn btn-primary">
    <i class="ti ti-plus me-2"></i>
    Nueva categoría
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

        <div>

            <h3 class="card-title">
                Categorías de productos
            </h3>

            <div class="text-secondary small">
                {{ $categories->total() }}
                categoría(s)
            </div>

        </div>

    </div>


    <div class="table-responsive-lg">

        <table class="table table-vcenter card-table">

            <thead>

                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Estado</th>

                    @if($canUpdate || $canStatus)
                    <th class="w-1">
                        Acciones
                    </th>
                    @endif
                </tr>

            </thead>


            <tbody>

                @forelse($categories as $category)

                <tr>

                    <td>

                        <div class="fw-bold">
                            {{ $category->name }}
                        </div>

                        <div class="text-secondary small">
                            {{ $category->slug }}
                        </div>

                    </td>


                    <td>

                        @if($category->description)

                        {{ \Illuminate\Support\Str::limit(
                                    $category->description,
                                    80
                                ) }}

                        @else

                        <span class="text-secondary">
                            Sin descripción
                        </span>

                        @endif

                    </td>


                    <td>

                        @if($category->is_active)

                        <span class="badge bg-green-lt">
                            Activa
                        </span>

                        @else

                        <span class="badge bg-red-lt">
                            Inactiva
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
                                                    'businesses.categories.edit',
                                                    [
                                                        $business,
                                                        $category
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
                                                    'businesses.categories.status',
                                                    [
                                                        $business,
                                                        $category
                                                    ]
                                                ) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="is_active"
                                        value="{{ $category->is_active ? 0 : 1 }}">

                                    <button
                                        type="submit"
                                        class="dropdown-item {{ $category->is_active ? 'text-danger' : 'text-success' }}">

                                        <i
                                            class="ti {{ $category->is_active ? 'ti-eye-off' : 'ti-eye' }} me-2"></i>

                                        {{ $category->is_active
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
                        colspan="{{ ($canUpdate || $canStatus) ? 4 : 3 }}"
                        class="text-center text-secondary py-5">

                        <i class="ti ti-category fs-1 d-block mb-2"></i>

                        No hay categorías registradas.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    @if($categories->hasPages())

    <div class="card-footer">
        {{ $categories->links() }}
    </div>

    @endif

</div>

@endsection