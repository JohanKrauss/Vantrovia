@extends('layouts.app')

@section('title', 'Editar sucursal')

@section('pretitle')
Sucursales
@endsection

@section('page-title')
Editar sucursal
@endsection

@section('content')

<div class="row">

    <div class="col-lg-9">

        <div class="card">

            <div class="card-header">

                <div>

                    <h3 class="card-title">
                        {{ $branch->name }}
                    </h3>

                    <div class="text-secondary">
                        {{ $branch->business->name }}
                    </div>

                </div>

            </div>

            <form
                action="{{ route('branches.update', $branch) }}"
                method="POST">

                @csrf
                @method('PUT')

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
                        <i class="ti ti-device-floppy me-2"></i>
                        Guardar cambios
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection