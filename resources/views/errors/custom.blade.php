<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Главная'))</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            @include('layouts.navbar.main_menu')
        </div>
    </nav>

    <main class="py-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="errorPage">
                                <div class="errorPage__topBlock">
                                    <div class="errorPage__image"></div>
                                    <div class="errorPage__title">@yield('message')</div>
                                    <div class="errorPage__subtitle">{{ __('Error') }} @yield('code')</div>
                                    <a href="/" class="btn btn-primary errorPage__btnMain">{{ __('To the main page') }}</a>
                                </div>
                                <div class="errorPage__bottom-block">
                                    <div class="errorPage__message">
                                        <span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
