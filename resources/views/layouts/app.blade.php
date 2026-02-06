<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="layout-menu-fixed layout-compact"
    data-assets-path="{{ asset('assets/') }}" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @guest
        <title>{{ config('', 'Login Page') }}</title>
    @else
        <title>{{ config('app.name', 'Dashboard') }}</title>
    @endguest

    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    @include('layouts.partial.links')
    @stack('styles')
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
</head>

<body>
    @guest
        @yield('content')
    @else
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                @include('layouts.partial.menu')
                <div class="layout-page">
                    @include('layouts.partial.nav')
                    <!-- Content wrapper -->
                    <div class="content-wrapper">
                        @yield('content')
                        @include('layouts.partial.footer')
                        <div class="content-backdrop fade"></div>

                    </div>
                </div>

            </div>

            <div class="layout-overlay layout-menu-toggle"></div>

        </div>
    @endguest
    @include('layouts.partial.scripts')
    @stack('scripts')
</body>

</html>
