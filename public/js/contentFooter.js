document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.footer-toggle');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            // Toggle la clase active en el botón
            this.classList.toggle('active');
            
            // Encuentra el siguiente elemento (el contenido colapsable)
            const content = this.nextElementSibling;
            if (content.classList.contains('show')) {
                content.classList.remove('show');
            } else {
                content.classList.add('show');
            }
        });
    });
});