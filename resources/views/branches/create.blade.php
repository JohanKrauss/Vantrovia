@extends('layouts.app')

@section('title', 'Nueva sucursal')

@section('pretitle')
Sucursales
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
                action="{{ route('branches.store') }}"
                method="POST">

                @csrf

                @include('branches._form')

                <div class="card-footer text-end">

                    <a
                        href="{{ route('branches.index') }}"
                        class="btn btn-outline-secondary me-2">
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="ti ti-building-store me-2"></i>
                        Crear sucursal
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection