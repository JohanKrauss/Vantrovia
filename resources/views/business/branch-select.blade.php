@extends('layouts.app')

@section('title', 'Seleccionar sucursal')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Seleccionar sucursal
@endsection


@section('content')

<div class="row justify-content-center">

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">

                <div>

                    <h3 class="card-title">
                        ¿En qué sucursal deseas trabajar?
                    </h3>

                    <div class="text-secondary small">
                        Selecciona una sucursal para continuar.
                    </div>

                </div>

            </div>


            <div class="card-body">

                @if($branches->isEmpty())

                <div class="text-center py-5">

                    <i
                        class="ti ti-building-off fs-1 text-secondary"></i>

                    <h3 class="mt-3">
                        Sin sucursales disponibles
                    </h3>

                    <p class="text-secondary">
                        No tienes ninguna sucursal activa
                        asignada dentro de este negocio.
                    </p>

                </div>

                @else

                <div class="row row-cards">

                    @foreach($branches as $branch)

                    <div class="col-md-6">

                        <form
                            action="{{ route(
                                        'business.branch.switch',
                                        $business
                                    ) }}"
                            method="POST">

                            @csrf

                            <input
                                type="hidden"
                                name="branch_id"
                                value="{{ $branch->id }}">


                            <button
                                type="submit"
                                class="card card-link card-link-pop w-100 text-start border-0">

                                <div class="card-body">

                                    <div class="d-flex align-items-center">

                                        <span class="avatar me-3">

                                            <i
                                                class="ti ti-building-store"></i>

                                        </span>


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


                                            @if(
                                            $branch->city ||
                                            $branch->state
                                            )

                                            <div class="text-secondary small mt-1">

                                                <i
                                                    class="ti ti-map-pin me-1"></i>

                                                {{ $branch->city }}

                                                @if(
                                                $branch->city &&
                                                $branch->state
                                                )
                                                ,
                                                @endif

                                                {{ $branch->state }}

                                            </div>

                                            @endif

                                        </div>


                                        <i
                                            class="ti ti-chevron-right"></i>

                                    </div>

                                </div>

                            </button>

                        </form>

                    </div>

                    @endforeach

                </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endsection