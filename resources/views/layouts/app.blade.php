<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Oh! Sansi') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    @stack('styles')

</head>

<body class="font-sans antialiased">
    <!-- Navigation -->
    @include('layouts.navigation')
    
    <!-- Contenedor principal flex -->
    <div class="min-h-screen bg-gray-100 flex flex-col">
        <!-- Contenido principal + sidebars -->
        <div class="flex flex-1">
            <!-- Sidebar izquierdo -->
            @include('layouts.sidebar')
            
            <!-- Contenido principal que se expande -->
            <main class="flex-1 p-4 overflow-auto">
                <!-- Header del contenido -->
                @if (isset($header))
                <header class="area-header">
                    {{ $header }}
                </header>
                @endif
                {{ $slot }}
            </main>
            
            <!-- Right Sidebar -->
            @include('layouts.rigthbar')
        </div>
        
        <!-- Footer -->
        @include('layouts.footer')
    </div>
</body>

</html>


{{-- Codigo anterior del body, porsiacaso, eliminar el Title header si les da error--}}

{{-- <body class="font-sans antialiased">
    <!-- Navigation -->
    @include('layouts.navigation')
    <!-- Contenido principal -->
    <div class="min-h-screen bg-gray-100 main-content">
        <!-- Sidebar first to ensure it's below other elements -->
        @include('layouts.sidebar')
        <!-- Page Content -->
        <main>
            <!-- Header del contenido -->
            @if (isset($header))
            <header class="area-header">
                {{ $header }}
            </header>
            @endif
            {{ $slot }}
        </main>
        <!-- Right Sidebar -->
        @include('layouts.rigthbar')
    </div>
    <!-- Footer -->
    @include('layouts.footer')
</body> --}}