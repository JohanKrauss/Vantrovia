@extends('layouts.app')

@section('title', 'Sucursales del usuario')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Sucursales del usuario
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

    <div class="col-lg-4">

        <div class="card">
            <div class="card-body">

                <div class="mb-3">
                    <div class="text-secondary small">
                        Usuario
                    </div>

                    <div class="fw-bold">
                        {{ $user->name }}
                    </div>

                    <div class="text-secondary">
                        {{ $user->email }}
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-secondary small">
                        Negocio
                    </div>

                    <div class="fw-bold">
                        {{ $business->name }}
                    </div>
                </div>

                <div>
                    <div class="text-secondary small">
                        Estado
                    </div>

                    @if($membership->is_active)
                    <span class="badge bg-green-lt">
                        Activo
                    </span>
                    @else
                    <span class="badge bg-red-lt">
                        Inactivo
                    </span>
                    @endif
                </div>

            </div>
        </div>

    </div>


    <div class="col-lg-8">

        <form
            action="{{ route(
                'businesses.users.branches.update',
                [
                    $business,
                    $user
                ]
            ) }}"
            method="POST">

            @csrf
            @method('PUT')

            <div class="card">

                <div class="card-header">
                    <div>
                        <h3 class="card-title">
                            Sucursales permitidas
                        </h3>

                        <div class="text-secondary small">
                            Selecciona las sucursales a las que este usuario podrá acceder.
                        </div>
                    </div>
                </div>


                <div class="card-body">

                    @if($branches->isEmpty())

                    <div class="text-center text-secondary py-4">

                        <i class="ti ti-building-off fs-1 d-block mb-2"></i>

                        Este negocio todavía no tiene sucursales.

                    </div>

                    @else

                    <div class="row row-cards">

                        @foreach($branches as $branch)

                        <div class="col-md-6">

                            <label class="card card-sm">

                                <div class="card-body">

                                    <div class="d-flex align-items-start">

                                        <input
                                            type="checkbox"
                                            name="branches[]"
                                            value="{{ $branch->id }}"
                                            class="form-check-input me-3 mt-1"
                                            @checked(
                                            in_array(
                                            $branch->id,
                                        old(
                                        'branches',
                                        $selectedBranchIds
                                        )
                                        )
                                        )
                                        >

                                        <div class="flex-fill">

                                            <div class="fw-bold">
                                                {{ $branch->name }}

                                                @if($branch->is_main)
                                                <span class="badge bg-blue-lt ms-1">
                                                    Principal
                                                </span>
                                                @endif
                                            </div>

                                            <div class="text-secondary small">
                                                {{ $branch->code }}
                                            </div>

                                            <div class="mt-2">

                                                @if($branch->status === 'active')

                                                <span class="badge bg-green-lt">
                                                    Activa
                                                </span>

                                                @else

                                                <span class="badge bg-red-lt">
                                                    Inactiva
                                                </span>

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </label>

                        </div>

                        @endforeach

                    </div>

                    @endif

                </div>


                <div class="card-footer d-flex justify-content-between">

                    <a
                        href="{{ route(
                            'businesses.users.index',
                            $business
                        ) }}"
                        class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>
                        Regresar
                    </a>


                    @if(!$branches->isEmpty())

                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="ti ti-device-floppy me-2"></i>
                        Guardar sucursales
                    </button>

                    @endif

                </div>

            </div>

        </form>

    </div>

</div>

@endsection