// Utilidad para mostrar y ocultar el overlay de carga
window.ModalOverlay = {
    show: function(message = 'Procesando inscripción...') {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.style.display = 'flex';
            overlay.style.opacity = '1';
            // Si hay mensaje personalizado
            const msg = overlay.querySelector('.loading-text');
            if (msg) msg.textContent = message;
        }
    },
    hide: function() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 300);
        }
    }
};