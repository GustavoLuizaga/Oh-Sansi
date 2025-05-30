<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Oh! Sansi</title>
    <link rel="stylesheet" href="{{ asset('CSS/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('CSS/barraNavegacionPrincipal.css') }}">
    <link rel="stylesheet" href="{{ asset('CSS/contentFooter.css') }}">
    <link rel="stylesheet" href="{{ asset('CSS/registerModal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="antialiased">
    @include('layouts.BarraNavegacionPrincipal')
    @include('layouts.registerModal')
    <div class="relative">
        <main class="contenedor">
            <section class="hero">
                 <!-- <div class="wave-top">
                    <img src="{{ asset('img/superior.svg') }}">
                </div> -->
                

                <div class="hero-content">
                    <div class="hero-text">
                        <h1>¡Bienvenido a Oh! SanSi!</h1>
                        <p class="quote">"Participa en las Olimpiadas Oh! SanSi 2025 y demuestra tu talento en Matemáticas, Física, Informática, Robótica y más. ¡Gana premios, reconocimiento y diviértete aprendiendo!</p>
                        @if (Route::has('register'))
                        <div class="hero-buttons">
                            <a href="{{ route('register') }}" class="register-hero-btn">
                                <i class="fas fa-user-plus"></i> ¡Registrarse Ahora!
                            </a>
                        </div>
                        @endif
                    </div>
                    <div class="hero-image">
                        <img src="/img/images/LogoUmss.png" pading-left:50px walt="Trofeo">
                    </div>
                </div>

                <div class="wave-bottom">
                    <svg class="wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path class="wave-path" fill="#ffffff" fill-opacity="1" d="M0,128L120,154.7C240,181,480,235,720,218.7C960,203,1200,117,1320,74.7L1440,32L1440,320L1320,320C1200,320,960,320,720,320C480,320,240,320,120,320L0,320Z"></path>
                    </svg>
                </div>
            </section>

            <!-- About Section -->
            <section class="about-olympiad">
                <h2>¿Qué son las Olimpiadas Oh! SanSi?</h2>
                <p>Las Olimpiadas Oh! SanSi son un evento anual que busca fomentar el conocimiento y 
                    la competencia en diversas áreas académicas.</p>
                
                <div class="areas-grid">
                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <h3>Matemáticas</h3>
                        <p>Desarrolla tu pensamiento lógico y resolución de problemas</p>
                    </div>

                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-atom"></i>
                        </div>
                        <h3>Física</h3>
                        <p>Explora las leyes fundamentales del universo</p>
                    </div>

                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h3>Informática</h3>
                        <p>Programa soluciones innovadoras</p>
                    </div>

                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3>Robótica</h3>
                        <p>Construye y programa robots del futuro</p>
                    </div>

                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h3>Química</h3>
                        <p>Descubre la ciencia de la materia</p>
                    </div>

                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-dna"></i>
                        </div>
                        <h3>Biología</h3>
                        <p>Estudia los misterios de la vida</p>
                    </div>

                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Astronomía</h3>
                        <p>Explora los secretos del cosmos</p>
                    </div>

                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3>Ingeniería</h3>
                        <p>Diseña el mundo del mañana</p>
                    </div>
                </div>
            </section>
            <section class="how-to-participate">
                <h2>¿Cómo participar?</h2>
                <ol class="participation-steps">
                    <li>
                        <i class="fas fa-file-alt fa-3x"></i>
                        <p>Completar el formulario.</p>
                    </li>
                    <li>
                        <i class="fas fa-money-bill-wave fa-3x"></i>
                        <p>Realizar el pago en la Caja UMSS.</p>
                    </li>
                    <li>
                        <i class="fas fa-upload fa-3x"></i>
                        <p>Subir el comprobante.</p>
                    </li>
                    <li>
                        <i class="fas fa-check-circle fa-3x"></i>
                        <p>Recibir la confirmación.</p>
                    </li>
                </ol>
                <a href="#" class="start-registration-btn"><i class="fas fa-pen-to-square"></i> Iniciar Inscripción</a>
            </section>
        </main>
    </div>

    @include('layouts.contentFooter')

    <script src="{{ asset('JS/home.js') }}"></script>
    <script src="{{ asset('JS/themeToggle.js') }}"></script>
    <script src="{{ asset('JS/registerModal.js') }}"></script>
</body>
</html>

