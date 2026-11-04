@extends('layouts.app')

@section('title', 'Nueva sucursal')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Nueva sucursal
@endsection


@section('content')

<div class="row">

    <div class="col-lg-9">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    Información de la sucursal
                </h3>

            </div>


            <form
                action="{{ route(
                    'businesses.branches.store',
                    $business
                ) }}"
                method="POST">

                @csrf

                @include(
                'businesses.branches._form'
                )


                <div class="card-footer text-end">

                    <a
                        href="{{ route(
                            'businesses.branches.index',
                            $business
                        ) }}"
                        class="btn btn-outline-secondary me-2">
                        Cancelar
                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="ti ti-building-plus me-2"></i>
                        Crear sucursal
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection