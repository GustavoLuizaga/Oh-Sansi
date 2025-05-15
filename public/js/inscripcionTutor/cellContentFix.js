/**
 * Script para corregir el desbordamiento de celdas en la tabla de Excel
 */
$(document).ready(function() {
    // Función para aplicar restricciones de texto a las celdas editables
    function applyTextConstraints() {
        // Para celdas existentes
        $('.editable').each(function() {
            const $this = $(this);
            
            // Añadir clases específicas según el tipo de columna
            const col = $this.data('col');
            const title = $this.attr('title') || '';
            
            if (col == 0 || col == 1 || col == 2 || col == 12) { // Nombres y apellidos
                $this.addClass('name-cell');
            } else if (col == 4 || col == 13) { // Emails
                $this.addClass('email-cell');
            } else if (col == 3 || col == 10) { // CI y número de contacto
                $this.addClass('number-cell');
            } else if (col == 6 || col == 14) { // Género y modalidad
                $this.addClass('short-cell');
            }
            
            // Limitar longitud visible si es necesario
            const text = $this.text();
            if (text.length > 100) {
                $this.attr('data-full-text', text);
                $this.text(text.substring(0, 97) + '...');
            }
            
            // Configura tooltip con texto completo
            $this.attr('title', title + (text.length > 0 ? ': ' + text : ''));
        });
    }
    
    // Aplicar cuando se muestra el modal
    $('#previewModal').on('shown.bs.modal', function() {
        setTimeout(applyTextConstraints, 500);
    });
    
    // Aplicar cuando se cambia de página o se agregan filas
    $('#previewTable').on('draw.dt', function() {
        setTimeout(applyTextConstraints, 200);
    });
    
    // Observar cambios en el tbody para detectar nuevas filas
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                applyTextConstraints();
            }
        });
    });
    
    // Iniciar observación del tbody cuando esté disponible
    $(document).on('DOMNodeInserted', '#previewTableBody', function() {
        observer.observe(document.getElementById('previewTableBody'), {
            childList: true,
            subtree: true
        });
    });
    
    // Mostrar texto completo al hacer clic en una celda truncada
    $(document).on('focus', '.editable', function() {
        const fullText = $(this).attr('data-full-text');
        if (fullText) {
            $(this).text(fullText);
        }
    });
    
    // Restaurar texto truncado al perder el foco si sigue siendo largo
    $(document).on('blur', '.editable', function() {
        const text = $(this).text();
        if (text.length > 100) {
            $(this).attr('data-full-text', text);
            $(this).text(text.substring(0, 97) + '...');
        } else {
            $(this).removeAttr('data-full-text');
        }
    });
});
