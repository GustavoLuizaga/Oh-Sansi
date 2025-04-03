<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/customWelcome.css') }}"> <!-- Tu archivo CSS personalizado -->

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body>
        <div class="container">
            @if (Route::has('login'))
                <div class="auth-links">
                    @auth
                        <a href="{{ url('/dashboard') }}">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="logo">
                    <svg viewBox="0 0 651 192" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- SVG content -->
                    </svg>
                </div>

                <div class="features">
                    <div class="feature">
                        <h2>Documentation</h2>
                        <p>Laravel has wonderful, thorough documentation covering every aspect of the framework.</p>
                    </div>
                    <div class="feature">
                        <h2>Laracasts</h2>
                        <p>Laracasts offers thousands of video tutorials on Laravel, PHP, and JavaScript development.</p>
                    </div>
                    <div class="feature">
                        <h2>Laravel News</h2>
                        <p>Laravel News is a community-driven portal and newsletter aggregating all of the latest news.</p>
                    </div>
                    <div class="feature">
                        <h2>Vibrant Ecosystem</h2>
                        <p>Laravel's robust library of first-party tools and libraries helps you take your projects to the next level.</p>
                    </div>
                </div>
            </div>

            <footer>
                <p>Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</p>
            </footer>
        </div>
    </body>
</html>