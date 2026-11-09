@extends('layouts.app')

@section('title', 'Configuración del negocio')

@section('pretitle')
{{ $business->name }}
@endsection

@section('page-title')
Configuración
@endsection


@php

$canUpdate = auth()
->user()
->hasBusinessPermission(
$business,
'business.update'
);

$timezones = [
'America/Mexico_City' => 'Ciudad de México / Centro',
'America/Monterrey' => 'Monterrey',
'America/Cancun' => 'Cancún / Quintana Roo',
'America/Chihuahua' => 'Chihuahua',
'America/Hermosillo' => 'Hermosillo / Sonora',
'America/Mazatlan' => 'Mazatlán',
'America/Tijuana' => 'Tijuana',
];

@endphp


@section('content')

@if(session('success'))

<div
    class="alert alert-success alert-dismissible auto-dismiss-alert"
    role="alert">

    <div>
        <i class="ti ti-circle-check me-2"></i>
        {{ session('success') }}
    </div>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert"
        aria-label="Cerrar"></button>

</div>

@endif


<div class="row">

    <div class="col-lg-8">

        <div class="card">

            <div class="card-header">

                <div>

                    <h3 class="card-title">
                        Información del negocio
                    </h3>

                    <div class="text-secondary small">
                        Datos generales de {{ $business->name }}
                    </div>

                </div>

            </div>


            <form
                action="{{ route(
                    'businesses.settings.update',
                    $business
                ) }}"
                method="POST">

                @csrf
                @method('PUT')


                <div class="card-body">

                    {{-- Nombre comercial --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Nombre comercial
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $business->name) }}"
                            class="form-control @error('name') is-invalid @enderror"
                            @disabled(!$canUpdate)
                            required>

                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    {{-- Razón social --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Razón social
                        </label>

                        <input
                            type="text"
                            name="legal_name"
                            value="{{ old('legal_name', $business->legal_name) }}"
                            class="form-control @error('legal_name') is-invalid @enderror"
                            @disabled(!$canUpdate)>

                        @error('legal_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    {{-- Subdominio --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Subdominio
                        </label>

                        <div class="input-group">

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $business->slug }}"
                                disabled>

                            <span class="input-group-text">
                                .vantrovia.com
                            </span>

                        </div>

                        <small class="form-hint">
                            El subdominio solo puede modificarse desde la administración de Vantrovia.
                        </small>

                    </div>


                    <div class="row">

                        {{-- Email --}}
                        <div class="col-md-6">

                            <div class="mb-3">

                                <label class="form-label">
                                    Correo
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email', $business->email) }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    @disabled(!$canUpdate)>

                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                        </div>


                        {{-- Teléfono --}}
                        <div class="col-md-6">

                            <div class="mb-3">

                                <label class="form-label">
                                    Teléfono
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone', $business->phone) }}"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    @disabled(!$canUpdate)>

                                @error('phone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                        </div>

                    </div>


                    <hr>


                    <h3 class="card-title mb-3">
                        Configuración regional
                    </h3>


                    <div class="row">

                        {{-- Zona horaria --}}
                        <div class="col-md-8">

                            <div class="mb-3">

                                <label class="form-label">
                                    Zona horaria
                                </label>

                                <select
                                    name="timezone"
                                    class="form-select @error('timezone') is-invalid @enderror"
                                    @disabled(!$canUpdate)
                                    required>

                                    @foreach($timezones as $value => $label)

                                    <option
                                        value="{{ $value }}"
                                        @selected(
                                        old( 'timezone' ,
                                        $business->timezone
                                        ) === $value
                                        )
                                        >
                                        {{ $label }}
                                    </option>

                                    @endforeach

                                </select>

                                @error('timezone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                        </div>


                        {{-- Moneda --}}
                        <div class="col-md-4">

                            <div class="mb-3">

                                <label class="form-label">
                                    Moneda
                                </label>

                                <select
                                    name="currency"
                                    class="form-select @error('currency') is-invalid @enderror"
                                    @disabled(!$canUpdate)
                                    required>

                                    <option
                                        value="MXN"
                                        @selected(
                                        old( 'currency' ,
                                        $business->currency
                                        ) === 'MXN'
                                        )
                                        >
                                        MXN - Peso mexicano
                                    </option>

                                    <option
                                        value="USD"
                                        @selected(
                                        old( 'currency' ,
                                        $business->currency
                                        ) === 'USD'
                                        )
                                        >
                                        USD - Dólar estadounidense
                                    </option>

                                </select>

                                @error('currency')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                        </div>

                    </div>


                    {{-- Estado --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Estado del negocio
                        </label>

                        <div>

                            @switch($business->status)

                            @case('active')

                            <span class="badge bg-green-lt">
                                Activo
                            </span>

                            @break


                            @case('pending')

                            <span class="badge bg-yellow-lt">
                                Pendiente
                            </span>

                            @break


                            @case('suspended')

                            <span class="badge bg-orange-lt">
                                Suspendido
                            </span>

                            @break


                            @case('cancelled')

                            <span class="badge bg-red-lt">
                                Cancelado
                            </span>

                            @break

                            @endswitch

                        </div>

                        <small class="form-hint">
                            El estado del negocio es administrado por Vantrovia.
                        </small>

                    </div>

                </div>


                @if($canUpdate)

                <div class="card-footer text-end">

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="ti ti-device-floppy me-2"></i>

                        Guardar cambios

                    </button>

                </div>

                @endif

            </form>

        </div>

    </div>


    {{-- Información lateral --}}
    <div class="col-lg-4">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    Información
                </h3>

            </div>

            <div class="card-body">

                <div class="mb-3">

                    <div class="text-secondary small">
                        Nombre
                    </div>

                    <div class="fw-bold">
                        {{ $business->name }}
                    </div>

                </div>


                <div class="mb-3">

                    <div class="text-secondary small">
                        Identificador
                    </div>

                    <div>
                        #{{ $business->id }}
                    </div>

                </div>


                <div class="mb-3">

                    <div class="text-secondary small">
                        Subdominio
                    </div>

                    <div>
                        {{ $business->slug }}.vantrovia.com
                    </div>

                </div>


                <div>

                    <div class="text-secondary small">
                        Registrado
                    </div>

                    <div>
                        {{ $business->created_at->format('d/m/Y') }}
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection