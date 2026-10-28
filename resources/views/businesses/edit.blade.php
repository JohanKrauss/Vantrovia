@extends('layouts.app')

@section('title', 'Editar negocio')

@section('pretitle')
Negocios
@endsection

@section('page-title')
Editar negocio
@endsection

@section('content')

<div class="row">

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">

                <div>

                    <h3 class="card-title">
                        {{ $business->name }}
                    </h3>

                    <div class="text-secondary">
                        {{ $business->slug }}.vantrovia.com
                    </div>

                </div>

            </div>


            <form
                action="{{ route('businesses.update', $business) }}"
                method="POST">

                @csrf
                @method('PUT')

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
                        <i class="ti ti-device-floppy me-2"></i>
                        Guardar cambios
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection