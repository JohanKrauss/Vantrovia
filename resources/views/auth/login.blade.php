<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title>Iniciar sesión | Vantrovia</title>

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

                <div class="text-secondary">
                    Plataforma de administración para negocios
                </div>

            </div>

            <div class="card card-md">

                <div class="card-body">

                    <h2 class="h2 text-center mb-4">
                        Iniciar sesión
                    </h2>

                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form
                        action="{{ route('login.authenticate') }}"
                        method="POST">

                        @csrf

                        <div class="mb-3">

                            <label class="form-label">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="correo@ejemplo.com"
                                autocomplete="email"
                                autofocus
                                required>

                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                        <div class="mb-2">

                            <div class="d-flex justify-content-between">

                                <label class="form-label">
                                    Contraseña
                                </label>

                                <a
                                    href="{{ route('password.request') }}"
                                    class="small">
                                    ¿Olvidaste tu contraseña?
                                </a>

                            </div>

                            <div class="input-group input-group-flat">

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Contraseña"
                                    autocomplete="current-password"
                                    required>

                                <span class="input-group-text">

                                    <button
                                        type="button"
                                        class="btn btn-link p-0 border-0"
                                        id="togglePassword"
                                        aria-label="Mostrar contraseña">
                                        <i
                                            id="passwordIcon"
                                            class="ti ti-eye"></i>
                                    </button>

                                </span>

                            </div>

                            @error('password')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                        <div class="mb-3">

                            <label class="form-check">

                                <input
                                    type="checkbox"
                                    name="remember"
                                    value="1"
                                    class="form-check-input">

                                <span class="form-check-label">
                                    Mantener sesión iniciada
                                </span>

                            </label>

                        </div>

                        <div class="form-footer">

                            <button
                                type="submit"
                                class="btn btn-primary w-100">
                                <i class="ti ti-login me-2"></i>
                                Iniciar sesión
                            </button>

                        </div>

                    </form>

                </div>

            </div>

            <div class="text-center text-secondary mt-3">
                © {{ date('Y') }} Vantrovia
            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const password = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');
            const passwordIcon = document.getElementById('passwordIcon');

            togglePassword.addEventListener('click', function() {

                const hidden = password.type === 'password';

                password.type = hidden ? 'text' : 'password';

                passwordIcon.classList.toggle('ti-eye', !hidden);
                passwordIcon.classList.toggle('ti-eye-off', hidden);

            });

        });
    </script>

</body>

</html>