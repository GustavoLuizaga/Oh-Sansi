<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            :root[class='modo-oscuro'] {
                color-scheme: dark;
            }
            @media (prefers-color-scheme: dark) {
                :root {
                    color-scheme: dark;
                }
            }
        </style>
        <script>
            // Aplicar tema antes de que se cargue la página
            const tema = (() => {
                const guardado = localStorage.getItem('tema');
                if (guardado) return guardado;
                
                return window.matchMedia('(prefers-color-scheme: dark)').matches 
                    ? 'oscuro' 
                    : 'claro';
            })();
            
            if (tema === 'oscuro') {
                document.documentElement.classList.add('modo-oscuro');
            }
        </script>
    <title>Oh! Sansi</title>
    <link rel="stylesheet" href="/css/welcome.css">
    <link rel="stylesheet" href="/css/barraNavegacionPrincipal.css">
    <link rel="stylesheet" href="/css/contentFooter.css">
    <link rel="stylesheet" href="/css/registerModal.css">
    <link rel="stylesheet" href="/css/dashboard.css">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="antialiased">
    @include('layouts/BarraNavegacionPrincipal')
    @include('layouts/registerModal')
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
                        <img src="/img/images/UmssLogo.png" pading-left:50px walt="Trofeo">
                    </div>
                </div>

                <div class="wave-bottom">
                    <svg class="wave-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path class="wave-path" d="M0,128L120,154.7C240,181,480,235,720,218.7C960,203,1200,117,1320,74.7L1440,32L1440,320L1320,320C1200,320,960,320,720,320C480,320,240,320,120,320L0,320Z"></path>
                    </svg>
                </div>
            </section>

            <!-- About Section -->
            <section class="que-son-olmpiadas">
                <h2>¿Qué son las Olimpiadas Oh! SanSi?</h2>
                <p>Las Olimpiadas Oh! SanSi son un evento anual que busca fomentar el conocimiento y 
                    la competencia en diversas áreas académicas.</p>
            </section>
            @if($hayAreasDisponibles)
            <section class="about-olympiad">
                <h2>¿Areas de competicion?</h2>
                <div class="areas-container">
                    <div class="areas-grid">
                        @foreach($areas as $area)
                        <div class="area-card" style="cursor:pointer;" onclick="window.location='{{ route('convocatoria.redirigirPorArea', $area->idArea) }}'">
                            <div class="area-icon">
                                <i class="{{ $area->icono }}"></i>
                            </div>
                            <h3>{{ $area->nombre }}</h3>
                        </div>
                        @endforeach
                    </div>
                    <div class="areas-navigation">
                        <button class="nav-btn scroll-left">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="nav-btn scroll-right">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </section>
            @endif
            <section class="how-to-participate">
                <h2>¿Cómo participar?</h2>
                <ol class="participation-steps">
                    <li class="step-item" data-step="1">
                        <i class="fas fa-user-graduate fa-3x"></i>
                        <p>Registrarse como estudiante.</p>
                    </li>
                    <li class="step-item" data-step="2">
                        <i class="fas fa-file-alt fa-3x"></i>
                        <p>Completar el formulario.</p>
                    </li>
                    <li class="step-item" data-step="3">
                        <i class="fas fa-money-bill-wave fa-3x"></i>
                        <p>Realizar el pago en la Caja FCYT.</p>
                    </li>
                    <li class="step-item" data-step="4">
                        <i class="fas fa-upload fa-3x"></i>
                        <p>Subir el comprobante.</p>
                    </li>
                    <li class="step-item" data-step="5">
                        <i class="fas fa-check-circle fa-3x"></i>
                        <p>Recibir la confirmación.</p>
                    </li>
                </ol>
                @if (Route::has('register'))
                <a href="{{ route('register') }}" class="start-registration-btn">
                    <i class="fas fa-user-graduate"></i> Registrarse como Estudiante
                </a>
                @endif

                <!-- Modales para cada paso -->
                <div class="step-modals">
                    <!-- Modal Paso 1 -->
                    <div class="step-modal" id="modal-step-1">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3><i class="fas fa-user-graduate"></i> Registro de Estudiante</h3>
                                <button class="close-modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="step-details">
                                    <div class="step-icon">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div class="step-info">
                                        <h4>Paso 1: Registro como Estudiante</h4>
                                        <p>Para comenzar tu participación en las Olimpiadas Oh! SanSi, necesitas:</p>
                                        <ul>
                                            <li>Crear una cuenta de estudiante</li>
                                            <li>Proporcionar tus datos personales</li>
                                            <li>Verificar tu correo electrónico</li>
                                        </ul>
                                        <div class="step-action">
                                            <a href="{{ route('register') }}" class="modal-action-btn">
                                                <i class="fas fa-user-plus"></i> Crear Cuenta
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Paso 2 -->
                    <div class="step-modal" id="modal-step-2">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3><i class="fas fa-file-alt"></i> Formulario de Inscripción</h3>
                                <button class="close-modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="step-details">
                                    <div class="step-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="step-info">
                                        <h4>Paso 2: Completar el Formulario</h4>
                                        <p>Una vez registrado, deberás:</p>
                                        <ul>
                                            <li>Seleccionar las áreas de tu interés</li>
                                            <li>Elegir la categoría correspondiente</li>
                                            <li>Indicar tu colegio y grado</li>
                                            <li>Seleccionar modalidad de participación</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Paso 3 -->
                    <div class="step-modal" id="modal-step-3">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3><i class="fas fa-money-bill-wave"></i> Pago de Inscripción</h3>
                                <button class="close-modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="step-details">
                                    <div class="step-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="step-info">
                                        <h4>Paso 3: Realizar el Pago</h4>
                                        <p>Para confirmar tu inscripción:</p>
                                        <ul>
                                            <li>Dirígete a la Caja de la FCYT</li>
                                            <li>Presenta tu CI o carnet de identidad</li>
                                            <li>Realiza el pago correspondiente</li>
                                            <li>Guarda tu comprobante de pago</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Paso 4 -->
                    <div class="step-modal" id="modal-step-4">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3><i class="fas fa-upload"></i> Subir Comprobante</h3>
                                <button class="close-modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="step-details">
                                    <div class="step-icon">
                                        <i class="fas fa-upload"></i>
                                    </div>
                                    <div class="step-info">
                                        <h4>Paso 4: Subir Comprobante</h4>
                                        <p>Después de realizar el pago:</p>
                                        <ul>
                                            <li>Accede a tu cuenta</li>
                                            <li>Sube una imagen clara del comprobante</li>
                                            <li>Asegúrate que el monto y fecha sean visibles</li>
                                            <li>Espera la verificación del pago</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Paso 5 -->
                    <div class="step-modal" id="modal-step-5">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3><i class="fas fa-check-circle"></i> Confirmación</h3>
                                <button class="close-modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="step-details">
                                    <div class="step-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="step-info">
                                        <h4>Paso 5: Confirmación Final</h4>
                                        <p>Una vez verificado tu pago:</p>
                                        <ul>
                                            <li>Recibirás una notificación por correo</li>
                                            <li>Tu estado cambiará a "Inscrito"</li>
                                            <li>Podrás acceder a los materiales de estudio</li>
                                            <li>Te mantendremos informado sobre las fechas importantes</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    @include('layouts/contentFooter')

    <script src="/js/home.js"></script>
    <script src="/js/themeToggle.js"></script>
    <script src="/js/registerModal.js"></script>
    <script src="/js/mobileMenu.js"></script>
    <script src="/js/areasCarousel.js"></script>
    <script src="/js/contentFooter.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionar elementos
        const stepItems = document.querySelectorAll('.step-item');
        const modals = document.querySelectorAll('.step-modal');
        const closeButtons = document.querySelectorAll('.close-modal');

        // Función para abrir modal
        function openModal(modalId) {
            console.log('Abriendo modal:', modalId); // Debug
            const modal = document.getElementById(modalId);
            if (modal) {
                // Remover clase show-modal de todos los modales
                modals.forEach(m => {
                    m.classList.remove('show-modal');
                    m.style.display = 'none';
                });
                
                // Mostrar y animar el modal actual
                modal.style.display = 'flex';
                // Pequeño retraso para permitir la transición
                requestAnimationFrame(() => {
                    modal.classList.add('show-modal');
                });
                
                // Prevenir scroll del body
                document.body.style.overflow = 'hidden';
            } else {
                console.error('Modal no encontrado:', modalId); // Debug
            }
        }

        // Función para cerrar modal
        function closeModal(modal) {
            console.log('Cerrando modal'); // Debug
            if (modal) {
                modal.classList.remove('show-modal');
                // Esperar a que termine la animación antes de ocultar
                setTimeout(() => {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }, 300);
            }
        }

        // Event listeners para abrir modales
        stepItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const stepNumber = item.getAttribute('data-step');
                console.log('Click en paso:', stepNumber); // Debug
                openModal(`modal-step-${stepNumber}`);
            });
        });

        // Event listeners para cerrar modales
        closeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const modal = button.closest('.step-modal');
                closeModal(modal);
            });
        });

        // Cerrar modal al hacer clic fuera
        modals.forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal(modal);
                }
            });
        });

        // Cerrar modal con tecla ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                modals.forEach(modal => {
                    if (modal.classList.contains('show-modal')) {
                        closeModal(modal);
                    }
                });
            }
        });

        // Debug: Verificar que los elementos existen
        console.log('Step items:', stepItems.length);
        console.log('Modals:', modals.length);
        console.log('Close buttons:', closeButtons.length);
    });
    </script>

</body>
</html>

