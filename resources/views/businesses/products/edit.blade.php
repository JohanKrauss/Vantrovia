@extends('layouts.app')

@section('title', 'Editar producto')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Editar producto
@endsection


@section('content')

<div class="row">

    <div class="col-lg-9">

        <div class="card">

            <div class="card-header">

                <div>

                    <h3 class="card-title">
                        {{ $product->name }}
                    </h3>

                    @if($product->sku)

                        <div class="text-secondary small">
                            SKU: {{ $product->sku }}
                        </div>

                    @endif

                </div>

            </div>


            <form
                action="{{ route(
                    'businesses.products.update',
                    [
                        $business,
                        $product
                    ]
                ) }}"
                method="POST"
            >

                @csrf
                @method('PUT')

                @include(
                    'businesses.products._form'
                )


                <div class="card-footer text-end">

                    <a
                        href="{{ route(
                            'businesses.products.index',
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