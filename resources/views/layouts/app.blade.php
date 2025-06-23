<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
{{-- ESTE ES EL CODIGO DE LA VISTA APP.BLADE.PHP --}}
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <style>
        :root[class='modo-oscuro'] {
            color-scheme: dark;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                color-scheme: dark;
            }
        }
        
        /* Estilos base del layout */
        .main-content-container {
            display: flex;
            flex: 1;
            position: relative;
        }
        
        main {
            flex: 1;
            padding: 1rem;
            margin-left: 250px; /* Ancho inicial del sidebar */
            transition: margin-left 0.3s ease;
        }
        
        .sidebar-collapsed main {
            margin-left: 60px; /* Ancho del sidebar colapsado */
        }
        
        /* Asegurar que el contenido no se salga */
        html, body {
            overflow-x: hidden;
        }

        /* ESTILOS RESPONSIVE MEJORADOS */
        
        /* Botón de menú móvil */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: #0056b3;
            transform: scale(1.05);
        }

        .mobile-menu-toggle:active {
            transform: scale(0.95);
        }

        /* Overlay para móvil */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
        }

        /* Responsive para tablets */
        @media (max-width: 1024px) {
            .sidebar-derecho {
                width: 200px !important;
                min-width: 200px !important;
            }
            
            main {
                margin-left: 200px;
                margin-right: 200px;
            }
            
            .sidebar-collapsed main {
                margin-left: 50px;
                margin-right: 200px;
            }
        }

        /* Responsive para móviles */
        @media (max-width: 768px) {
            /* Mostrar botón de menú móvil */
            .mobile-menu-toggle {
                display: block;
            }

            /* Ajustar main content para móvil */
            main {
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding-top: 70px; /* Espacio para el botón móvil */
            }

            /* Sidebar en móvil */
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh;
                width: 280px !important;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            /* Ocultar rightbar en móvil */
            .sidebar-derecho {
                display: none !important;
            }

            /* Ajustar el botón toggle del sidebar en móvil */
            .btn-toggle-sidebar {
                display: none !important;
            }

            /* Asegurar que el contenido principal ocupe todo el ancho */
            .main-content-container {
                flex-direction: column;
            }

            /* Header responsive */
            .area-header {
                margin-top: 0;
                padding-top: 0;
            }
        }

        /* Responsive para móviles pequeños */
        @media (max-width: 480px) {
            .mobile-menu-toggle {
                top: 10px;
                left: 10px;
                padding: 10px 12px;
                font-size: 16px;
            }

            main {
                padding: 0.5rem;
                padding-top: 60px;
            }

            .sidebar {
                width: 100% !important;
            }
        }

        /* Animaciones suaves */
        .sidebar, .sidebar-derecho, main {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Estado cuando el sidebar móvil está abierto */
        body.sidebar-mobile-open {
            overflow: hidden;
        }

        /* Mejoras visuales para el overlay */
        .sidebar-overlay.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Rightbar responsive adicional */
        @media (max-width: 1200px) {
            .sidebar-derecho {
                width: 180px;
                min-width: 180px;
            }
        }

        @media (max-width: 992px) {
            .sidebar-derecho {
                position: fixed;
                right: -300px;
                top: 0;
                height: 100vh;
                background: white;
                border-left: 1px solid #e0e0e0;
                transition: right 0.3s ease;
                width: 300px;
                box-shadow: -2px 0 10px rgba(0,0,0,0.1);
            }

            .sidebar-derecho.active {
                right: 0;
            }

            main {
                margin-right: 0 !important;
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Oh! Sansi') }}</title>

    {{-- Link de estilos de Boostrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="/css/custom.css">
    <link rel="stylesheet" href="/css/custom/footer.css">
    <link rel="stylesheet" href="/css/custom/navegation.css">
    <link rel="stylesheet" href="/css/custom/rigbar.css">
    <link rel="stylesheet" href="/css/custom/sidebar.css">
    <!-- Scripts -->
    <script src="/js/app.js"></script>
    <script src="/js/themeToggle.js"></script>

    @stack('styles')
</head>

<body class="font-sans antialiased">
    <!-- Overlay para móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Botón de menú para móviles -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Navigation -->
    @include('layouts/navigation')
    
    <!-- Contenedor principal flex -->
    <div class="min-h-screen bg-gray-100 flex flex-col">
        <!-- Sidebar izquierdo (fuera del flujo normal) -->
        @include('layouts/sidebar')
        
        <!-- Contenedor del contenido principal -->
        <div class="main-content-container">
            <!-- Contenido principal que se expande -->
            <main class="flex-1 p-4 overflow-auto">
                @if (isset($header))
                <header class="area-header">
                    {{ $header }}
                </header>
                @endif
                {{ $slot }}
            </main>
            
            <!-- Right Sidebar -->
            @include('layouts/rigthbar')
        </div>
        
        <!-- Footer -->
        @include('layouts/footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- Script del sidebar expandible -->
    <script src="/js/sidebar.js"></script>
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mobileToggle = document.getElementById('mobileMenuToggle');
            const toggleBtn = document.getElementById('btn-toggle-sidebar');
            const body = document.body;

            // Función para detectar si estamos en móvil
            function isMobile() {
                return window.innerWidth <= 768;
            }

            // Función para detectar si estamos en tablet
            function isTablet() {
                return window.innerWidth > 768 && window.innerWidth <= 1024;
            }

            // Manejar el menú móvil
            if (mobileToggle && sidebar && sidebarOverlay) {
                mobileToggle.addEventListener('click', function() {
                    if (isMobile()) {
                        sidebar.classList.toggle('mobile-open');
                        sidebarOverlay.classList.toggle('active');
                        body.classList.toggle('sidebar-mobile-open');
                        
                        // Cambiar ícono del botón
                        const icon = this.querySelector('i');
                        if (sidebar.classList.contains('mobile-open')) {
                            icon.className = 'fas fa-times';
                        } else {
                            icon.className = 'fas fa-bars';
                        }
                    }
                });

                // Cerrar menú al hacer clic en el overlay
                sidebarOverlay.addEventListener('click', function() {
                    if (isMobile()) {
                        sidebar.classList.remove('mobile-open');
                        sidebarOverlay.classList.remove('active');
                        body.classList.remove('sidebar-mobile-open');
                        
                        // Restaurar ícono
                        const icon = mobileToggle.querySelector('i');
                        icon.className = 'fas fa-bars';
                    }
                });
            }
            
            // Cerrar menú móvil al hacer clic en un enlace
            const menuLinks = document.querySelectorAll('.sidebar a');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (isMobile() && sidebar.classList.contains('mobile-open')) {
                        sidebar.classList.remove('mobile-open');
                        sidebarOverlay.classList.remove('active');
                        body.classList.remove('sidebar-mobile-open');
                        
                        // Restaurar ícono
                        const icon = mobileToggle.querySelector('i');
                        icon.className = 'fas fa-bars';
                    }
                });
            });
            
            // Manejar el toggle del sidebar en desktop
            if (toggleBtn) {
                // Verificar el estado inicial solo en desktop
                if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
                    body.classList.add('sidebar-collapsed');
                }
                
                toggleBtn.addEventListener('click', function() {
                    if (!isMobile()) {
                        body.classList.toggle('sidebar-collapsed');
                        
                        // Guardar el estado solo en desktop
                        localStorage.setItem('sidebarCollapsed', 
                            body.classList.contains('sidebar-collapsed'));
                    }
                });
            }
            
            // Manejar cambios de tamaño de pantalla
            function handleResponsive() {
                if (isMobile()) {
                    // En móvil: limpiar estados de desktop
                    body.classList.remove('sidebar-collapsed');
                    sidebar.classList.remove('collapsed');
                    
                    // Asegurar que el overlay esté oculto si cambiamos a móvil
                    if (!sidebar.classList.contains('mobile-open')) {
                        sidebarOverlay.classList.remove('active');
                        body.classList.remove('sidebar-mobile-open');
                        
                        // Restaurar ícono del botón móvil
                        const icon = mobileToggle?.querySelector('i');
                        if (icon) icon.className = 'fas fa-bars';
                    }
                } else {
                    // En desktop: limpiar estados de móvil
                    sidebar.classList.remove('mobile-open');
                    sidebarOverlay.classList.remove('active');
                    body.classList.remove('sidebar-mobile-open');
                    
                    // Restaurar ícono del botón móvil
                    const icon = mobileToggle?.querySelector('i');
                    if (icon) icon.className = 'fas fa-bars';
                    
                    // Restaurar estado colapsado en desktop si estaba guardado
                    if (localStorage.getItem('sidebarCollapsed') === 'true') {
                        body.classList.add('sidebar-collapsed');
                    }
                }
            }
            
            // Ejecutar al cambiar tamaño de ventana
            window.addEventListener('resize', handleResponsive);
            
            // Ejecutar al cargar la página
            handleResponsive();

            // Manejar el rightbar en tablets (opcional)
            const rightbarToggle = document.createElement('button');
            rightbarToggle.innerHTML = '<i class="fas fa-info-circle"></i>';
            rightbarToggle.className = 'rightbar-toggle';
            rightbarToggle.style.cssText = `
                display: none;
                position: fixed;
                top: 15px;
                right: 15px;
                background: #28a745;
                color: white;
                border: none;
                border-radius: 8px;
                padding: 12px 15px;
                font-size: 16px;
                cursor: pointer;
                box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            `;

            // Solo mostrar toggle del rightbar en tablets si es necesario
            if (window.innerWidth > 768 && window.innerWidth <= 992) {
                document.body.appendChild(rightbarToggle);
                rightbarToggle.style.display = 'block';
                
                rightbarToggle.addEventListener('click', function() {
                    const rightbar = document.querySelector('.sidebar-derecho');
                    if (rightbar) {
                        rightbar.classList.toggle('active');
                    }
                });
            }

            // Prevenir scroll del body cuando el menú móvil está abierto
            function preventBodyScroll() {
                if (body.classList.contains('sidebar-mobile-open')) {
                    const scrollY = window.scrollY;
                    body.style.position = 'fixed';
                    body.style.top = `-${scrollY}px`;
                    body.style.width = '100%';
                } else {
                    const scrollY = body.style.top;
                    body.style.position = '';
                    body.style.top = '';
                    body.style.width = '';
                    window.scrollTo(0, parseInt(scrollY || '0') * -1);
                }
            }

            // Observer para cambios en la clase del body
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        preventBodyScroll();
                    }
                });
            });

            observer.observe(body, { attributes: true });
        });
    </script>
</body>
</html>