<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Oh! SanSi - Olimpiadas Universitarias</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/customWelcome.css') }}">
</head>

<body>
    <div class="container">
        @if (Route::has('login'))
        <header class="auth-links">
            <nav>
                <div class="logo">OH! <span>SANSI</span></div>
                <div class="nav-links">
                    <a href="{{ url('/') }}">Inicio</a>
                    <a href="#">Comvocatoria</a>
                    <a href="#">Reglamento</a>
                </div>
                <div>
                    @auth
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    @else
                    <a href="{{ route('login') }}">Iniciar Sesion</a>

                    @if (Route::has('register'))
                    <a href="{{ route('register') }}">Registrarse</a>
                    @endif
                    @endauth
                </div>
            </nav>
        </header>
        @endif

        <main class="contenedor">
            <section class="welcome">
                <!-- Hero section remains unchanged -->
                <div class="svg-superior">
                    <svg class="svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
                        <path fill="#cc0000" fill-opacity="1" d="M0,160L60,138.7C120,117,240,75,360,74.7C480,75,600,117,720,144C840,171,960,181,1080,165.3C1200,149,1320,107,1380,85.3L1440,64L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z"></path>
                    </svg>
                </div>
                <div class="umss-mensaje">
                    <div class="umss-text">
                        <h1>¡Bienvenido a Oh! SanSi!</h1>
                        <p class="text">"Participa en las Olimpiadas Oh! SanSi 2025 y demuestra tu talento en Matemáticas, Física, Informática, Robótica y más. ¡Gana premios, reconocimiento y diviértete aprendiendo!</p>
                    </div>
                    <div class="umss-img">
                        <img src="{{ asset('img/albert.png') }}" alt="umss logo">
                    </div>
                </div>

                <div class="svg-inferior">
                    <svg class="wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path class="wave-path" fill="#ffffff" fill-opacity="1" d="M0,128L120,154.7C240,181,480,235,720,218.7C960,203,1200,117,1320,74.7L1440,32L1440,320L1320,320C1200,320,960,320,720,320C480,320,240,320,120,320L0,320Z"></path>
                    </svg>
                </div>
            </section>

            <!-- About Section -->
            <section class="informacion">
                <h2>¿Qué son las Olimpiadas Oh! SanSi?</h2>
                <p>Las Olimpiadas Ohsansi son una iniciativa de la Universidad Mayor de San Simón (UMSS) en Cochabamba, Bolivia. Estas olimpiadas están dirigidas a estudiantes de nivel secundario y abarcan diversas áreas de ciencia y tecnología, incluyendo matemáticas, biología, astronomía y robótica. El objetivo es promover el interés por las ciencias entre los jóvenes estudiantes, ofreciendo un espacio competitivo donde puedan demostrar sus habilidades en resolución de problemas y conocimientos científicos.</p>

            </section>

            <!-- NEW: Areas Section -->
            <section class="areas">
                <h2>Áreas de Competición</h2>
                <div class="areas-grid">
                    <!-- Matemáticas -->
                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <h3>Matemáticas</h3>
                        <p>Desarrolla tu pensamiento lógico y resolución de problemas</p>
                    </div>

                    <!-- Física -->
                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-atom"></i>
                        </div>
                        <h3>Física</h3>
                        <p>Explora las leyes fundamentales del universo</p>
                    </div>

                    <!-- Informática -->
                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h3>Informática</h3>
                        <p>Programa soluciones innovadoras</p>
                    </div>

                    <!-- Robótica -->
                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3>Robótica</h3>
                        <p>Construye y programa robots del futuro</p>
                    </div>

                    <!-- Química -->
                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h3>Química</h3>
                        <p>Descubre la ciencia de la materia</p>
                    </div>

                    <!-- Biología -->
                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-dna"></i>
                        </div>
                        <h3>Biología</h3>
                        <p>Estudia los misterios de la vida</p>
                    </div>

                    <!-- Astronomía -->
                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Astronomía</h3>
                        <p>Explora los secretos del cosmos</p>
                    </div>

                    <!-- Ingeniería -->
                    <div class="area-card">
                        <div class="area-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3>Ingeniería</h3>
                        <p>Diseña el mundo del mañana</p>
                    </div>
                </div>
            </section>

            <!-- How to Participate Section (unchanged) -->
            <section class="como-participar">
                <h2>¿Cómo participar?</h2>
                <ol class="pasos-participar">
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
                <a href="#" class="registro-btn"><i class="fas fa-pen-to-square"></i> Iniciar Inscripción</a>
            </section>
        </main>

        <footer>
            <div class="contenedor footer-content">
                <div class="footer-logo">
                    <div class="logo">OH! <span>SANSI</span></div>
                    <p>Impulsando el talento científico y tecnológico en la nueva generación de estudiantes bolivianos.</p>
                </div>

                <div class="footer-links">
                    <div class="footer-columna">
                        <h3>Olimpiadas</h3>
                        <ul>
                            <li><a href="#">Categorías</a></li>
                            <li><a href="#">Calendario</a></li>
                            <li><a href="#">Premios</a></li>
                            <li><a href="#">Resultados</a></li>
                        </ul>
                    </div>

                    <div class="footer-columna">
                        <h3>Recursos</h3>
                        <ul>
                            <li><a href="#">Material de Estudio</a></li>
                            <li><a href="#">Guía del Participante</a></li>
                            <li><a href="#">Preguntas Frecuentes</a></li>
                        </ul>
                    </div>

                    <div class="footer-columna">
                        <h3>Contacto</h3>
                        <ul class="contacto-info">
                            <li><i class="fas fa-map-marker-alt"></i> Campus UMSS, Cochabamba</li>
                            <li><i class="fas fa-phone"></i> +591 4 4525252</li>
                            <li><i class="fas fa-envelope"></i> olimpiadas@umss.edu</li>
                        </ul>
                        <div class="social-media">
                            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-inferior">
                <p>&copy; 2025 Olimpiadas Oh! SanSi - Universidad Mayor de San Simón</p>
            </div>
        </footer>
    </div>
</body>

</html>