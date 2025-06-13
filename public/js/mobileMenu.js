// Script para manejar el menú móvil y la barra de navegación
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const navLinks = document.getElementById('nav-links');
    const themeToggle = document.getElementById('theme-toggle');
    const themeToggleMobile = document.getElementById('theme-toggle-mobile');
    const header = document.querySelector('header');
    
    // Variables para controlar el scroll
    let lastScrollTop = 0;
    
    // Función para controlar la visibilidad de la barra de navegación al hacer scroll
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Si estamos en la parte superior de la página, siempre mostrar la barra
        if (scrollTop <= 10) {
            header.classList.remove('nav-hidden');
        } else {
            // Determinar dirección del scroll
            if (scrollTop > lastScrollTop) {
                // Scroll hacia abajo - ocultar la barra
                header.classList.add('nav-hidden');
            } else {
                // Scroll hacia arriba - mostrar la barra
                header.classList.remove('nav-hidden');
            }
        }
        
        lastScrollTop = scrollTop;
    });
    
    if (menuToggle && navLinks) {
        // Función para alternar el menú
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            
            // Cambiar el ícono del botón
            const icon = menuToggle.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Cerrar el menú cuando se hace clic en un enlace
        const links = navLinks.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', function() {
                navLinks.classList.remove('active');
                
                // Restaurar el ícono
                const icon = menuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            });
        });

        // Sincronizar el tema entre los botones móvil y desktop
        if (themeToggle && themeToggleMobile) {
            themeToggleMobile.addEventListener('click', function() {
                document.body.classList.toggle('dark-theme');
                updateThemeIcons();
            });
        }
        
        // Cerrar el menú cuando se hace clic fuera de él
        document.addEventListener('click', function(event) {
            if (!navLinks.contains(event.target) && !menuToggle.contains(event.target) && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                
                // Restaurar el ícono
                const icon = menuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }

    // Función para actualizar los iconos del tema
    function updateThemeIcons() {
        const isDark = document.body.classList.contains('dark-theme');
        const icons = document.querySelectorAll('.theme-toggle i');
        
        icons.forEach(icon => {
            if (isDark) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });
    }
});