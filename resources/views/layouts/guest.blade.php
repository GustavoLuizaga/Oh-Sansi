<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="/css/welcome.css">
        <link rel="stylesheet" href="{{ asset('css/register.css') }}">
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">
        <link rel="stylesheet" href="{{ asset('css/forgot-password.css') }}">
        <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
        <link rel="stylesheet" href="{{ asset('css/reset-password.css') }}">
        <link rel="stylesheet" href="{{ asset('css/barraNavegacionPrincipal.css') }}">
        
        <!-- Scripts -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script src="/js/app.js" defer></script>
        <script src="{{ asset('js/themeToggle.js') }}" defer></script>
        <script src="{{ asset('js/mobileMenu.js') }}"></script>
        <script src="{{ asset('js/togglePassword.js') }}" defer></script>


    </head>
    <body>
    @include('layouts/BarraNavegacionPrincipal')
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>
    </body>
</html>
