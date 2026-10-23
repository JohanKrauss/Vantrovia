@extends('layouts.app')

@section('title', 'Editar usuario')

@section('pretitle')
Usuarios
@endsection

@section('page-title')
Editar usuario
@endsection


@section('content')

<div class="row">

    <div class="col-lg-7">

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    Información del usuario
                </h3>
            </div>

            <form
                action="{{ route('users.update', $user) }}"
                method="POST">

                @csrf
                @method('PUT')


                <div class="card-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Nombre
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="form-control @error('name') is-invalid @enderror"
                            required>

                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="form-control @error('email') is-invalid @enderror"
                            required>

                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    <div class="mb-3">

                        <label class="form-check">

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                class="form-check-input"
                                @checked(old('is_active', $user->is_active))
                            >

                            <span class="form-check-label">
                                Usuario activo
                            </span>

                        </label>

                        @error('is_active')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                </div>


                <div class="card-footer text-end">

                    <a
                        href="{{ route('users.index') }}"
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