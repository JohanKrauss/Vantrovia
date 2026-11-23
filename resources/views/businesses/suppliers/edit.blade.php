@extends('layouts.app')

@section('title', 'Editar proveedor')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Editar proveedor
@endsection


@section('content')

<div class="row">

    <div class="col-lg-9">

        <div class="card">

            <div class="card-header">

                <div>

                    <h3 class="card-title">
                        {{ $supplier->name }}
                    </h3>

                    @if($supplier->tax_id)

                    <div class="text-secondary small">
                        RFC / Identificador:
                        {{ $supplier->tax_id }}
                    </div>

                    @endif

                </div>

            </div>


            <form
                action="{{ route(
                    'businesses.suppliers.update',
                    [
                        $business,
                        $supplier
                    ]
                ) }}"
                method="POST">

                @csrf
                @method('PUT')

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
                        <i class="ti ti-device-floppy me-2"></i>
                        Guardar cambios
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection