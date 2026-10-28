@extends('layouts.app')

@section('title', 'Nuevo rol')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Nuevo rol
@endsection

@section('content')

<form
    action="{{ route('businesses.roles.store', $business) }}"
    method="POST">

    @csrf

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Configuración del rol
            </h3>
        </div>

        @include('businesses.roles._form')

        <div class="card-footer text-end">

            <a
                href="{{ route('businesses.roles.index', $business) }}"
                class="btn btn-outline-secondary me-2">
                Cancelar
            </a>

            <button class="btn btn-primary">

                <i class="ti ti-shield-plus me-2"></i>
                Crear rol

            </button>

        </div>

    </div>

</form>

@endsection