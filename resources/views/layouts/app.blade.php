<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title>
        @yield('title', 'Vantrovia')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body>

    <div class="page">

        @include('partials.sidebar')

        <div class="page-wrapper">

            @include('partials.navbar')

            <div class="page-header d-print-none">
                <div class="container-xl">

                    <div class="row g-2 align-items-center">

                        <div class="col">

                            @hasSection('pretitle')
                                <div class="page-pretitle">
                                    @yield('pretitle')
                                </div>
                            @endif

                            <h2 class="page-title">
                                @yield('page-title', 'Dashboard')
                            </h2>

                        </div>

                        <div class="col-auto ms-auto d-print-none">
                            @yield('page-actions')
                        </div>

                    </div>

                </div>
            </div>

            <main class="page-body">
                <div class="container-xl">

                    @yield('content')

                </div>
            </main>

            @include('partials.footer')

        </div>

    </div>

    @stack('scripts')

</body>

</html>