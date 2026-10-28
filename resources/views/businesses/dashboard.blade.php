@extends('layouts.app')

@section('title', $business->name)

@section('pretitle')
Negocio
@endsection

@section('page-title')
{{ $business->name }}
@endsection


@section('content')

<div class="row row-cards">

    {{-- Sucursales --}}
    <div class="col-sm-6 col-lg-3">

        <div class="card card-sm">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col-auto">

                        <span class="bg-primary text-white avatar">
                            <i class="ti ti-building-store"></i>
                        </span>

                    </div>

                    <div class="col">

                        <div class="font-weight-medium">
                            {{ $branchCount }}
                        </div>

                        <div class="text-secondary">
                            Sucursales
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Sucursales activas --}}
    <div class="col-sm-6 col-lg-3">

        <div class="card card-sm">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col-auto">

                        <span class="bg-success text-white avatar">
                            <i class="ti ti-building-check"></i>
                        </span>

                    </div>

                    <div class="col">

                        <div class="font-weight-medium">
                            {{ $activeBranchCount }}
                        </div>

                        <div class="text-secondary">
                            Sucursales activas
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Usuarios --}}
    <div class="col-sm-6 col-lg-3">

        <div class="card card-sm">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col-auto">

                        <span class="bg-blue text-white avatar">
                            <i class="ti ti-users"></i>
                        </span>

                    </div>

                    <div class="col">

                        <div class="font-weight-medium">
                            {{ $userCount }}
                        </div>

                        <div class="text-secondary">
                            Usuarios activos
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Rol --}}
    <div class="col-sm-6 col-lg-3">

        <div class="card card-sm">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col-auto">

                        <span class="bg-purple text-white avatar">
                            <i class="ti ti-shield"></i>
                        </span>

                    </div>

                    <div class="col">

                        <div class="font-weight-medium">
                            {{ $roleName ?? 'Plataforma' }}
                        </div>

                        <div class="text-secondary">
                            Tu rol
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="card mt-3">

    <div class="card-body">

        <h3 class="card-title">
            Bienvenido a {{ $business->name }}
        </h3>

        <p class="text-secondary mb-0">
            Desde este panel administrarás las operaciones
            correspondientes a este negocio.
        </p>

    </div>

</div>

@endsection