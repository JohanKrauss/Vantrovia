@extends('layouts.app')

@section('title', 'Nuevo negocio')

@section('pretitle')
Negocios
@endsection

@section('page-title')
Nuevo negocio
@endsection

@section('content')

<div class="row">

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    Información del negocio
                </h3>
            </div>

            <form
                action="{{ route('businesses.store') }}"
                method="POST">

                @csrf

                @include('businesses._form')


                <div class="card-footer text-end">

                    <a
                        href="{{ route('businesses.index') }}"
                        class="btn btn-outline-secondary me-2">
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="ti ti-building-plus me-2"></i>
                        Crear negocio
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection