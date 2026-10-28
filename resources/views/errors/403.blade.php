@extends('layouts.app')

@section('title', 'Acceso denegado')

@section('page-title')
Acceso denegado
@endsection

@section('content')

<div class="container-tight py-5">

    <div class="empty">

        <div class="empty-header">
            403
        </div>

        <p class="empty-title">
            No tienes permiso para acceder
        </p>

        <p class="empty-subtitle text-secondary">
            Tu cuenta no tiene los permisos necesarios
            para realizar esta acción.
        </p>

        <div class="empty-action">

            <a
                href="{{ url()->previous() }}"
                class="btn btn-primary">
                <i class="ti ti-arrow-left me-2"></i>
                Regresar
            </a>

        </div>

    </div>

</div>

@endsection