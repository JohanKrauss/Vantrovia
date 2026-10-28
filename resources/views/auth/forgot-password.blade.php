    <!doctype html>
    <html lang="es" data-bs-theme="light">

    <head>
        <meta charset="utf-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, viewport-fit=cover">

        <title>Recuperar contraseña | Vantrovia</title>

        <script>
            (() => {
                const savedTheme = localStorage.getItem('theme');

                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ?
                    'dark' :
                    'light';

                const theme = savedTheme || systemTheme;
                document.documentElement.setAttribute('data-bs-theme', theme);
            })();
        </script>

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

                        <h2 class="h2 text-center mb-3">
                            Recuperar contraseña
                        </h2>

                        <p class="text-secondary text-center mb-4">
                            Ingresa el correo asociado a tu cuenta y te enviaremos
                            un enlace para crear una nueva contraseña.
                        </p>

                        @if (session('status'))
                        <div class="alert alert-success">
                            <i class="ti ti-circle-check me-2"></i>
                            {{ session('status') }}
                        </div>
                        @endif

                        <form
                            action="{{ route('password.email') }}"
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

                            <button
                                type="submit"
                                class="btn btn-primary w-100">
                                <i class="ti ti-mail me-2"></i>
                                Enviar enlace de recuperación
                            </button>

                        </form>

                    </div>

                </div>

                <div class="text-center mt-3">

                    <a href="{{ route('login') }}">
                        <i class="ti ti-arrow-left me-1"></i>
                        Volver al inicio de sesión
                    </a>

                </div>

            </div>

        </div>

    </body>

    </html>