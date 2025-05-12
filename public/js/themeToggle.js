document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar ambos botones (desktop y mobile)
    const themeToggleDesktop = document.getElementById('theme-toggle');
    const themeToggleMobile = document.getElementById('theme-toggle-mobile');
    const root = document.documentElement;

    // Función para actualizar el tema
    const updateTheme = (isDark) => {
        const icon = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        const theme = isDark ? 'oscuro' : 'claro';
        
        // Actualizar ambos botones
        if (themeToggleDesktop) themeToggleDesktop.innerHTML = icon;
        if (themeToggleMobile) themeToggleMobile.innerHTML = icon;
        
        // Actualizar el tema
        if (isDark) {
            root.classList.add('modo-oscuro');
            root.style.backgroundColor = '#1a202c';
        } else {
            root.classList.remove('modo-oscuro');
            root.style.backgroundColor = '';
        }
        
        localStorage.setItem('tema', theme);
    };

    // Aplicar tema inicial
    const temaGuardado = localStorage.getItem('tema') || 'claro';
    updateTheme(temaGuardado === 'oscuro');

    // Manejar clicks en botón desktop
    if (themeToggleDesktop) {
        themeToggleDesktop.addEventListener('click', function() {
            const isDark = !root.classList.contains('modo-oscuro');
            updateTheme(isDark);
        });
    }

    // Manejar clicks en botón mobile
    if (themeToggleMobile) {
        themeToggleMobile.addEventListener('click', function() {
            const isDark = !root.classList.contains('modo-oscuro');
            updateTheme(isDark);
        });
    }
});
