document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const btnToggle = document.getElementById('btn-toggle-sidebar');
    const mainContent = document.querySelector('main');
    const toggleIcon = document.getElementById('toggle-icon');
    
    // Verificar si existen los elementos necesarios
    if (!sidebar || !btnToggle || !mainContent) {
        console.warn('Elementos del sidebar no encontrados');
        return;
    }

    // Estado inicial del sidebar (expandido por defecto)
    let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    
    // Aplicar estado inicial
    if (sidebarCollapsed) {
        collapseSidebar();
    }

    // Event listener para el botón toggle
    btnToggle.addEventListener('click', function() {
        if (sidebar.classList.contains('collapsed')) {
            expandSidebar();
        } else {
            collapseSidebar();
        }
    });

    // Función para colapsar el sidebar
    function collapseSidebar() {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
        
        // Cambiar el ícono a >>
        if (toggleIcon) {
            toggleIcon.className = 'fas fa-angle-double-right';
        }
        
        // Guardar estado en localStorage
        localStorage.setItem('sidebarCollapsed', 'true');
        sidebarCollapsed = true;
    }

    // Función para expandir el sidebar
    function expandSidebar() {
        sidebar.classList.remove('collapsed');
        mainContent.classList.remove('expanded');
        
        // Cambiar el ícono a <<
        if (toggleIcon) {
            toggleIcon.className = 'fas fa-angle-double-left';
        }
        
        // Guardar estado en localStorage
        localStorage.setItem('sidebarCollapsed', 'false');
        sidebarCollapsed = false;
    }

    // Función para agregar tooltips
    function addTooltips() {
        // Agregar data-title a los títulos de sección si no los tienen
        const titulosMenu = sidebar.querySelectorAll('.titulo-menu');
        titulosMenu.forEach(titulo => {
            const texto = titulo.querySelector('.menu-text');
            if (texto && !titulo.hasAttribute('data-title')) {
                titulo.setAttribute('data-title', texto.textContent.trim());
            }
        });

        // Agregar data-title a los items del menú si no los tienen
        const menuItems = sidebar.querySelectorAll('.menu ul li');
        menuItems.forEach(item => {
            const link = item.querySelector('a');
            if (link && !item.hasAttribute('data-title')) {
                const existingTitle = link.getAttribute('data-title');
                if (existingTitle) {
                    item.setAttribute('data-title', existingTitle);
                } else {
                    const texto = link.querySelector('.menu-text');
                    if (texto) {
                        item.setAttribute('data-title', texto.textContent.trim());
                    }
                }
            }
        });
    }

    // Función para remover tooltips (opcional, los CSS ya los ocultan)
    function removeTooltips() {
        // Los tooltips se ocultan automáticamente con CSS cuando se expande
        // Esta función está aquí por si necesitas lógica adicional
    }

    // Manejar cambios de tamaño de ventana para responsive
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768) {
            // En móvil, usar comportamiento diferente
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        } else if (sidebarCollapsed) {
            // Restaurar estado colapsado en desktop
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }
    });

    // Funcionalidad adicional para móvil (opcional)
    function initializeMobileMenu() {
        if (window.innerWidth <= 768) {
            // Agregar overlay para cerrar menú en móvil
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 899;
                display: none;
            `;
            document.body.appendChild(overlay);

            // Toggle móvil
            btnToggle.addEventListener('click', function() {
                if (sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                    overlay.style.display = 'none';
                } else {
                    sidebar.classList.add('mobile-open');
                    overlay.style.display = 'block';
                }
            });

            // Cerrar al hacer clic en overlay
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-open');
                overlay.style.display = 'none';
            });
        }
    }

    // Inicializar menú móvil si es necesario
    initializeMobileMenu();
});

// Función global para toggle programático (opcional)
window.toggleSidebar = function() {
    const btnToggle = document.getElementById('btn-toggle-sidebar');
    if (btnToggle) {
        btnToggle.click();
    }
};
