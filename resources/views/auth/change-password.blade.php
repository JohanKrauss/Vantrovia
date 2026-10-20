@extends('layouts.app')

@section('title', 'Cambiar contraseña')

@section('pretitle')
Mi cuenta
@endsection

@section('page-title')
Cambiar contraseña
@endsection

@section('content')

<div class="row">

    <div class="col-lg-6">

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    Seguridad de la cuenta
                </h3>
            </div>

            <div class="card-body">

                @if (session('status'))

                <div class="alert alert-success">
                    <i class="ti ti-circle-check me-2"></i>
                    {{ session('status') }}
                </div>

                @endif

                <form
                    action="{{ route('password.change') }}"
                    method="POST">

                    @csrf
                    @method('PUT')

                    <div class="mb-3">

                        <label class="form-label">
                            Contraseña actual
                        </label>

                        <input
                            type="password"
                            name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            required>

                        @error('current_password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Nueva contraseña
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            required>

                        @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Confirmar nueva contraseña
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            required>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary">
                        <i class="ti ti-lock me-2"></i>
                        Cambiar contraseña
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection