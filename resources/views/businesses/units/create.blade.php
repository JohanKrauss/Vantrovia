@extends('layouts.app')

@section('title', 'Nueva unidad')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Nueva unidad de medida
@endsection


@section('content')

<div class="row">

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    Información de la unidad
                </h3>
            </div>

            <form
                action="{{ route(
                    'businesses.units.store',
                    $business
                ) }}"
                method="POST"
            >

                @csrf

                @include(
                    'businesses.units._form'
                )

                <div class="card-footer text-end">

                    <a
                        href="{{ route(
                            'businesses.units.index',
                            $business
                        ) }}"
                        class="btn btn-outline-secondary me-2"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="ti ti-plus me-2"></i>
                        Crear unidad
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection