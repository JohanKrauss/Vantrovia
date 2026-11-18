@extends('layouts.app')

@section('title', 'Editar unidad')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Editar unidad de medida
@endsection


@section('content')

<div class="row">

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    {{ $unit->name }}
                </h3>

            </div>

            <form
                action="{{ route(
                    'businesses.units.update',
                    [
                        $business,
                        $unit
                    ]
                ) }}"
                method="POST"
            >

                @csrf
                @method('PUT')

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
                        <i class="ti ti-device-floppy me-2"></i>
                        Guardar cambios
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection