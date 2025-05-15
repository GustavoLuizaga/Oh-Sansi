/**
 * Script para mejorar la interacción con celdas que tienen errores
 * Permite la edición mientras mantiene visible la indicación de error
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando mejoras de interacción para celdas con error');
    
    // Función para manejar la edición de celdas con error
    function handleErrorCellsEditing() {
        // Detectar cuando se hace clic en una celda con error
        $(document).on('click', '.invalid-cell', function(e) {
            const cell = this;
            
            // Guardar temporalmente el atributo title
            const errorMessage = cell.getAttribute('title');
            if (errorMessage) {
                // Guardar el mensaje de error en un atributo de datos
                cell.setAttribute('data-error-message', errorMessage);
                
                // Temporalmente eliminar el title para evitar que aparezca el tooltip nativo
                cell.removeAttribute('title');
                
                // Restaurar la clase para mantener el estilo visual pero sin tooltip
                setTimeout(function() {
                    // Permitir la edición normal
                    console.log('Permitiendo edición en celda con error');
                }, 10);
            }
        });
        
        // Restaurar el tooltip cuando la celda pierde el foco
        $(document).on('blur', '.invalid-cell', function() {
            const cell = this;
            const errorMessage = cell.getAttribute('data-error-message');
            
            // Si teníamos un mensaje de error guardado, restaurarlo
            if (errorMessage && !cell.hasAttribute('title')) {
                setTimeout(function() {
                    cell.setAttribute('title', errorMessage);
                }, 100);
            }
        });
    }
    
    // Función para hacer que la edición de celdas con error sea clara
    function improveErrorCellFeedback() {
        // Estilizar la celda cuando entra en modo edición
        $(document).on('focus', '.invalid-cell', function() {
            this.style.backgroundColor = '#fff'; // Fondo blanco para editar mejor
            this.style.boxShadow = '0 0 0 1px #ff6666'; // Mantener indicación de error
        });
        
        // Restaurar el estilo cuando pierde el foco
        $(document).on('blur', '.invalid-cell', function() {
            this.style.backgroundColor = '#ffdddd';
            this.style.boxShadow = '0 0 5px rgba(255, 0, 0, 0.3)';
            
            // Volver a validar la celda después de la edición
            setTimeout(function() {
                if (typeof validateCell === 'function') {
                    try {
                        validateCell($(this));
                    } catch (e) {
                        console.error('Error al revalidar celda:', e);
                    }
                }
            }.bind(this), 100);
        });
    }
    
    // Observar el DOM para detectar cuando se muestra el modal
    $('#previewModal').on('shown.bs.modal', function() {
        handleErrorCellsEditing();
        improveErrorCellFeedback();
    });
    
    // Inicializar las mejoras
    handleErrorCellsEditing();
    improveErrorCellFeedback();
    
    console.log('Mejoras de interacción para celdas con error inicializadas');
});
