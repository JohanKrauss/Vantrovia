@extends('layouts.app')

@section('title', 'Editar rol')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Editar rol
@endsection

@section('content')

<form
    action="{{ route(
        'businesses.roles.update',
        [
            $business,
            $role
        ]
    ) }}"
    method="POST">

    @csrf
    @method('PUT')

    <div class="card">

        <div class="card-header">

            <div>

                <h3 class="card-title">
                    {{ $role->name }}
                </h3>

                @if($role->is_system)

                <div class="text-secondary small">
                    Rol del sistema
                </div>

                @endif

            </div>

        </div>

        @include('businesses.roles._form')

        <div class="card-footer text-end">

            <a
                href="{{ route('businesses.roles.index', $business) }}"
                class="btn btn-outline-secondary me-2">
                Cancelar
            </a>

            <button class="btn btn-primary">

                <i class="ti ti-device-floppy me-2"></i>
                Guardar cambios

            </button>

        </div>

    </div>

</form>

@endsection