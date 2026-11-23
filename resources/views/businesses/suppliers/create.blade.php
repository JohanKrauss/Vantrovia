@extends('layouts.app')

@section('title', 'Nuevo proveedor')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Nuevo proveedor
@endsection


@section('content')

<div class="row">

    <div class="col-lg-9">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    Información del proveedor
                </h3>

            </div>


            <form
                action="{{ route(
                    'businesses.suppliers.store',
                    $business
                ) }}"
                method="POST">

                @csrf

                @include(
                'businesses.suppliers._form'
                )


                <div class="card-footer text-end">

                    <a
                        href="{{ route(
                            'businesses.suppliers.index',
                            $business
                        ) }}"
                        class="btn btn-outline-secondary me-2">
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>
                        Crear proveedor
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection