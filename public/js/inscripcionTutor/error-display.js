/**
 * Script para mejorar la visualización de errores en la tabla Excel
 * Muestra indicadores de error sin bloquear la edición de celdas
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando sistema de visualización de errores mejorado');

    // Función para mejorar la visualización de los errores
    function enhanceErrorDisplay() {
        // Seleccionar todas las celdas con error
        const invalidCells = document.querySelectorAll('.invalid-cell');
        
        invalidCells.forEach(function(cell) {
            // Obtener el mensaje de error
            const errorMessage = cell.getAttribute('title') || 'Error de validación';
            
            // Asegurarse de que el atributo title existe
            if (!cell.hasAttribute('title')) {
                cell.setAttribute('title', 'Error de validación');
            }
            
            // Identificar el tipo de error para el estilo visual
            const errorType = errorMessage.toLowerCase();
            let errorClass = 'general-error';
            
            // Categorizar el error para diferenciación visual
            if (errorType.includes('email')) {
                errorClass = 'email-error';
            } else if (errorType.includes('modalidad')) {
                errorClass = 'modalidad-error';
            } else if (errorType.includes('código')) {
                errorClass = 'codigo-error';
            } else if (errorType.includes('nombre')) {
                errorClass = 'nombre-error';
            }
            
            // Usar data-attributes en lugar de elementos DOM que puedan interferir
            cell.setAttribute('data-error-type', errorClass);
            
            // Añadir clase para estilizar con CSS sin interferir con la edición
            cell.classList.add('error-enhanced');
        });
        
        // Actualizar contador de errores con información más detallada
        const errorCounter = document.getElementById('errorCounter');
        if (errorCounter) {
            const totalErrors = invalidCells.length;
            
            // Contar errores por tipo
            const errorsByType = {};
            invalidCells.forEach(function(cell) {
                const errorMsg = cell.getAttribute('title') || '';
                
                // Categorizar el error
                let errorType = 'Otro';
                if (errorMsg.includes('email')) errorType = 'Email';
                else if (errorMsg.includes('modalidad')) errorType = 'Modalidad';
                else if (errorMsg.includes('código')) errorType = 'Código';
                else if (errorMsg.includes('nombre')) errorType = 'Nombre';
                
                errorsByType[errorType] = (errorsByType[errorType] || 0) + 1;
            });
            
            // Crear una descripción detallada de los errores
            let errorDetails = '';
            for (const [type, count] of Object.entries(errorsByType)) {
                errorDetails += `<span class="error-type">${type}: ${count}</span> `;
            }
            
            // Actualizar el contador con los detalles
            const errorCountText = document.getElementById('errorCountText');
            if (errorCountText && totalErrors > 0) {
                errorCountText.innerHTML = `Errores encontrados: ${totalErrors} 
                    <span class="error-details">${errorDetails}</span>`;
            }
        }
    }
    
    // Observar cambios en la tabla para actualizar los errores automáticamente
    const observer = new MutationObserver(function(mutations) {
        // Comprobar si alguna mutación afecta a las clases (indica validación)
        const needsUpdate = mutations.some(function(mutation) {
            return mutation.type === 'attributes' && 
                  (mutation.attributeName === 'class' || 
                   mutation.attributeName === 'title');
        });
        
        if (needsUpdate) {
            setTimeout(enhanceErrorDisplay, 10);
        }
    });
    
    // Iniciar observación cuando se muestre el modal
    $('#previewModal').on('shown.bs.modal', function() {
        const tableBody = document.getElementById('previewTableBody');
        if (tableBody) {
            // Configurar el observador para detectar cambios en atributos de clase y título
            const config = { 
                attributes: true, 
                childList: true, 
                subtree: true, 
                attributeFilter: ['class', 'title'] 
            };
            
            observer.observe(tableBody, config);
            
            // Aplicar mejoras iniciales después de un corto retraso
            setTimeout(enhanceErrorDisplay, 300);
            
            // También aplicar cuando se valida toda la tabla
            setTimeout(enhanceErrorDisplay, 1000);
        }
    });
    
    // También aplicar después de cada validación de fila
    $(document).on('blur', '.editable', function() {
        setTimeout(enhanceErrorDisplay, 100);
    });
    
    // Desconectar el observador cuando se cierre el modal
    $('#previewModal').on('hidden.bs.modal', function() {
        observer.disconnect();
    });
    
    // Agregar estilos adicionales para los errores
    const style = document.createElement('style');
    style.textContent = `
        /* Estilos para el contador de errores mejorado */
        #errorCounter {
            padding: 10px 15px !important;
        }
        
        .error-details {
            margin-left: 10px;
            font-size: 0.9em;
        }
        
        .error-type {
            display: inline-block;
            margin-right: 8px;
            padding: 2px 6px;
            border-radius: 3px;
            background-color: rgba(255,0,0,0.1);
        }
        
        /* Sombra pulsante para errores graves */
        .email-error, .modalidad-error, .codigo-error {
            animation: pulsate 1.5s infinite alternate;
        }
        
        @keyframes pulsate {
            0% { box-shadow: 0 0 5px rgba(255, 0, 0, 0.6); }
            100% { box-shadow: 0 0 10px rgba(255, 0, 0, 0.8); }
        }
        
        /* Hacer que los mensajes de error sean visibles inmediatamente */
        .invalid-cell:after {
            opacity: 1;
            visibility: visible;
        }
        
        /* Estilo para las filas con error */
        tr.error-row td {
            border-left: 3px solid #ff8888 !important;
        }
    `;
    document.head.appendChild(style);
    
    console.log('Sistema de visualización de errores mejorado inicializado');
});
