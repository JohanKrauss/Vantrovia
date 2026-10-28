@extends('layouts.app')

@section('title', 'Seleccionar negocio')

@section('pretitle')
Vantrovia
@endsection

@section('page-title')
Seleccionar negocio
@endsection


@section('content')

@if(session('warning'))

<div
    class="alert alert-warning alert-dismissible auto-dismiss-alert"
    role="alert">
    <div>
        <i class="ti ti-alert-triangle me-2"></i>
        {{ session('warning') }}
    </div>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Cerrar"></button>
</div>

@endif


<div class="row justify-content-center">

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">

                <div>

                    <h3 class="card-title">
                        ¿Con qué negocio quieres trabajar?
                    </h3>

                    <div class="text-secondary">
                        Selecciona un negocio para continuar.
                    </div>

                </div>

            </div>


            @if($businesses->isEmpty())

            <div class="card-body text-center py-5">

                <i class="ti ti-building-off fs-1 text-secondary"></i>

                <h3 class="mt-3">
                    No tienes negocios disponibles
                </h3>

                <p class="text-secondary mb-0">
                    Tu usuario no está relacionado con ningún
                    negocio activo.
                </p>

            </div>

            @else

            <div class="list-group list-group-flush">

                @foreach($businesses as $business)

                <form
                    action="{{ route('business.switch') }}"
                    method="POST">

                    @csrf

                    <input
                        type="hidden"
                        name="business_id"
                        value="{{ $business->id }}">


                    <button
                        type="submit"
                        class="list-group-item list-group-item-action d-flex align-items-center py-3 w-100 text-start">

                        <span class="avatar me-3">

                            {{ strtoupper(
                                        substr(
                                            $business->name,
                                            0,
                                            2
                                        )
                                    ) }}

                        </span>


                        <div class="flex-fill">

                            <div class="fw-bold">
                                {{ $business->name }}
                            </div>

                            <div class="text-secondary small">
                                {{ $business->slug }}.vantrovia.com
                            </div>

                        </div>


                        <div>
                            <i class="ti ti-chevron-right"></i>
                        </div>

                    </button>

                </form>

                @endforeach

            </div>

            @endif

        </div>

    </div>

</div>

@endsection