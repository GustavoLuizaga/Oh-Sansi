// Script para el carrusel automático de áreas de competición
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar el contenedor de áreas
    const areasGrid = document.querySelector('.areas-grid');
    
    if (!areasGrid) return;
    
    // Variables para el carrusel
    let scrollAmount = 0;
    const cardWidth = 130; // Ancho aproximado de cada tarjeta + margen
    const scrollSpeed = 10000; // 4 segundos entre cada deslizamiento
    
    // Función para desplazar el carrusel
    function scrollCarousel() {
        // Si llegamos al final, volvemos al inicio
        if (scrollAmount >= areasGrid.scrollWidth - areasGrid.clientWidth) {
            scrollAmount = 0;
        } else {
            // Desplazamos el equivalente a una tarjeta
            scrollAmount += cardWidth;
        }
        
        // Aplicamos el desplazamiento con animación suave
        areasGrid.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
        });
    }
    
    // Iniciar el carrusel automático
    let carouselInterval = setInterval(scrollCarousel, scrollSpeed);
    
    // Detener el carrusel cuando el usuario interactúa con él
    areasGrid.addEventListener('mouseenter', function() {
        clearInterval(carouselInterval);
    });
    
    // Reanudar el carrusel cuando el usuario deja de interactuar
    areasGrid.addEventListener('mouseleave', function() {
        clearInterval(carouselInterval);
        // Crear un nuevo intervalo y asignarlo a la variable
        carouselInterval = setInterval(scrollCarousel, scrollSpeed);
    });
    
    // Agregar navegación con botones
    const areasSection = document.querySelector('.about-olympiad');
    
    // Crear botones de navegación
    const navButtons = document.createElement('div');
    navButtons.className = 'carousel-nav-buttons';
    navButtons.innerHTML = `
        <button class="nav-button prev" aria-label="Anterior">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="nav-button next" aria-label="Siguiente">
            <i class="fas fa-chevron-right"></i>
        </button>
    `;
    
    // Insertar botones después del grid
    areasSection.appendChild(navButtons);
    
    // Funcionalidad de los botones
    const prevButton = navButtons.querySelector('.prev');
    const nextButton = navButtons.querySelector('.next');
    
    prevButton.addEventListener('click', function() {
        scrollAmount = Math.max(0, scrollAmount - cardWidth);
        areasGrid.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
        });
    });
    
    nextButton.addEventListener('click', function() {
        scrollAmount = Math.min(
            areasGrid.scrollWidth - areasGrid.clientWidth,
            scrollAmount + cardWidth
        );
        areasGrid.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
        });
    });
});