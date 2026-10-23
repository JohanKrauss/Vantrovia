@extends('layouts.app')

@section('title', 'Dashboard')

@section('pretitle')
Resumen general
@endsection

@section('page-title')
Dashboard
@endsection

@section('content')

<div class="row row-deck row-cards">

    <div class="col-sm-6 col-lg-3">

        <div class="card">

            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div class="subheader">
                        Negocios
                    </div>

                </div>

                <div class="h1 mb-0 mt-2">
                    {{ $businessCount }}
                </div>

                <div class="text-secondary mt-2">
                    Negocios registrados
                </div>

            </div>

        </div>

    </div>

    <div class="col-sm-6 col-lg-3">

        <div class="card">

            <div class="card-body">

                <div class="subheader">
                    Usuarios
                </div>

                <div class="h1 mb-0 mt-2">
                    {{ $userCount }}
                </div>

                <div class="text-secondary mt-2">
                    Usuarios registrados
                </div>

            </div>

        </div>

    </div>

    <div class="col-sm-6 col-lg-3">

        <div class="card">

            <div class="card-body">

                <div class="subheader">
                    Sucursales
                </div>

                <div class="h1 mb-0 mt-2">
                    {{ $branchCount }}
                </div>

                <div class="text-secondary mt-2">
                    Sucursales registradas
                </div>

            </div>

        </div>

    </div>

    <div class="col-sm-6 col-lg-3">

        <div class="card">

            <div class="card-body">

                <div class="subheader">
                    Negocios activos
                </div>

                <div class="h1 mb-0 mt-2">
                    {{ $activeBusinessCount }}
                </div>

                <div class="text-secondary mt-2">
                    Clientes activos
                </div>

            </div>

        </div>

    </div>

</div>

@endsection