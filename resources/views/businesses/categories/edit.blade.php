@extends('layouts.app')

@section('title', 'Editar categoría')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Editar categoría
@endsection


@section('content')

<div class="row">

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    {{ $category->name }}
                </h3>

            </div>


            <form
                action="{{ route(
                    'businesses.categories.update',
                    [
                        $business,
                        $category
                    ]
                ) }}"
                method="POST">

                @csrf
                @method('PUT')

                @include(
                'businesses.categories._form'
                )


                <div class="card-footer text-end">

                    <a
                        href="{{ route(
                            'businesses.categories.index',
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