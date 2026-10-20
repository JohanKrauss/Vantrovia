<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title>Nueva contraseña | Vantrovia</title>

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])
</head>

<body class="d-flex flex-column bg-body-tertiary">

    <div class="page page-center">

        <div class="container container-tight py-4">

            <div class="text-center mb-4">

                <h1 class="mb-1">
                    Vantrovia
                </h1>

            </div>

            <div class="card card-md">

                <div class="card-body">

                    <h2 class="h2 text-center mb-3">
                        Crear nueva contraseña
                    </h2>

                    <form
                        action="{{ route('password.update') }}"
                        method="POST">

                        @csrf

                        <input
                            type="hidden"
                            name="token"
                            value="{{ $token }}">

                        <div class="mb-3">

                            <label class="form-label">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $email) }}"
                                class="form-control @error('email') is-invalid @enderror"
                                readonly
                                required>

                            @error('email')
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
                                autocomplete="new-password"
                                required>

                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Confirmar contraseña
                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                autocomplete="new-password"
                                required>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100">
                            <i class="ti ti-lock me-2"></i>
                            Actualizar contraseña
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</body>

</html>