/**
 * Script para mostrar tooltips mejorados después de la validación
 * Asegura que los mensajes de error sean visibles INMEDIATAMENTE sin necesidad de hover
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando sistema de tooltips mejorado');
    
    // Función para inicializar tooltips tradicionales (bootstrap)
    function initBootstrapTooltips() {
        try {
            // Primero eliminar tooltips anteriores para evitar duplicados
            if (typeof $ !== 'undefined' && typeof $.fn.tooltip !== 'undefined') {
                $('[data-bs-toggle="tooltip"]').tooltip('dispose');
            }
            
            // Reinicializar tooltips para elementos con el atributo correspondiente
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl, {
                        placement: 'top',
                        trigger: 'hover focus',
                        container: '#previewModal'
                    });
                });
            }
        } catch (error) {
            console.error('Error al inicializar tooltips de Bootstrap:', error);
        }
    }
      // Función para mostrar tooltips mejorados automáticamente sin bloquear la edición
    function showEnhancedTooltips() {
        // Seleccionar todas las celdas con error
        const invalidCells = document.querySelectorAll('.invalid-cell');
        
        invalidCells.forEach(function(cell) {
            // Obtener el mensaje de error desde el atributo title
            const errorMessage = cell.getAttribute('title');
            if (!errorMessage) return;
            
            // Eliminar tooltips antiguos para evitar superposición
            const existingTooltips = cell.querySelectorAll('.visible-tooltip');
            existingTooltips.forEach(tooltip => tooltip.remove());
            
            // No añadir tooltips permanentes para permitir edición de celdas
            // Solo vamos a mejorar visualmente la indicación del error
            
            // Añadir un icono de error pequeño que no interfiera
            if (!cell.querySelector('.error-indicator')) {
                const errorIndicator = document.createElement('span');
                errorIndicator.className = 'error-indicator';
                errorIndicator.textContent = '⚠️';
                errorIndicator.style.position = 'absolute';
                errorIndicator.style.top = '1px';
                errorIndicator.style.right = '2px';
                errorIndicator.style.fontSize = '10px';
                errorIndicator.style.color = '#cc0000';
                errorIndicator.style.pointerEvents = 'none'; // Para no interferir con clicks
                
                // Asegurarse de que la celda tiene posición relativa
                const cellPosition = window.getComputedStyle(cell).position;
                if (cellPosition === 'static') {
                    cell.style.position = 'relative';
                }
                
                // Añadir el indicador a la celda
                cell.appendChild(errorIndicator);
            }
            
            // Destacar visualmente la celda con error
            cell.style.border = '2px solid #ff6666';
            cell.style.backgroundColor = '#ffdddd';
            
            // Marcar la fila para mayor visibilidad
            const parentRow = cell.closest('tr');
            if (parentRow) {
                parentRow.classList.add('error-row');
                parentRow.style.backgroundColor = '#fff8f8';
            }
        });
        
        // Actualizar contador de errores con detalles
        updateErrorCounter(invalidCells);
    }
    
    // Función para actualizar el contador de errores
    function updateErrorCounter(invalidCells) {
        const errorCounter = document.getElementById('errorCounter');
        if (!errorCounter) return;
        
        const totalErrors = invalidCells.length;
        
        // Categorizar errores por tipo
        const errorsByType = {};
        invalidCells.forEach(cell => {
            const errorMsg = cell.getAttribute('title') || '';
            let category = 'Otro';
            
            // Determinar categoría del error basado en el mensaje
            if (errorMsg.includes('email')) category = 'Email';
            else if (errorMsg.includes('modalidad')) category = 'Modalidad';
            else if (errorMsg.includes('código')) category = 'Código';
            else if (errorMsg.includes('nombre')) category = 'Nombre';
            
            errorsByType[category] = (errorsByType[category] || 0) + 1;
        });
        
        // Crear un texto detallado del error
        let errorDetails = '';
        for (const [type, count] of Object.entries(errorsByType)) {
            errorDetails += `<span class="error-badge">${type}: ${count}</span> `;
        }
        
        // Actualizar contador
        const errorCountText = document.getElementById('errorCountText');
        if (errorCountText && totalErrors > 0) {
            errorCountText.innerHTML = `Errores encontrados: ${totalErrors} <div class="error-details">${errorDetails}</div>`;
        }
    }
    
    // Observar cambios en las celdas
    const observer = new MutationObserver(function(mutations) {
        let shouldUpdate = false;
        
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && 
                (mutation.attributeName === 'class' || mutation.attributeName === 'title')) {
                shouldUpdate = true;
            } else if (mutation.type === 'childList') {
                // Verificar si se han añadido nodos que podrían ser celdas inválidas
                const addedNodes = mutation.addedNodes;
                for (let i = 0; i < addedNodes.length; i++) {
                    const node = addedNodes[i];
                    if (node.nodeType === 1 && (
                        node.classList?.contains('invalid-cell') || 
                        node.querySelector?.('.invalid-cell')
                    )) {
                        shouldUpdate = true;
                        break;
                    }
                }
            }
        });
        
        if (shouldUpdate) {
            setTimeout(showEnhancedTooltips, 10);
            setTimeout(initBootstrapTooltips, 50);
        }
    });
    
    // Conectar el observador cuando se muestre el modal
    $('#previewModal').on('shown.bs.modal', function() {
        const tableBody = document.querySelector('#previewTableBody');
        if (tableBody) {
            observer.observe(tableBody, {
                attributes: true,
                childList: true,
                subtree: true,
                attributeFilter: ['class', 'title']
            });
            
            // Mostrar tooltips mejorados al abrir el modal
            setTimeout(showEnhancedTooltips, 300);
            setTimeout(showEnhancedTooltips, 1000); // Volver a intentarlo más tarde por seguridad
        }
    });
    
    // También mostrar tooltips después de cada validación
    $(document).on('blur', '.editable', function() {
        setTimeout(showEnhancedTooltips, 100);
    });
    
    // Desconectar el observador cuando se oculte el modal
    $('#previewModal').on('hidden.bs.modal', function() {
        observer.disconnect();
    });
      // Añadir estilos adicionales para los tooltips visibles
    const style = document.createElement('style');
    style.textContent = `
        /* Solo mostrar tooltip al hacer hover para no interferir con edición */
        .visible-tooltip {
            position: absolute !important;
            display: none !important; /* Inicialmente oculto */
            opacity: 0.9 !important;
            visibility: hidden !important; /* Oculto hasta hover */
            pointer-events: none; /* Permite interactuar con la celda */
            z-index: 1090;
        }
        
        /* Mostrar tooltip solo al pasar el cursor */
        .invalid-cell:hover .visible-tooltip,
        .invalid-cell:hover .error-indicator {
            display: block !important;
            visibility: visible !important;
        }
        
        .error-badge {
            display: inline-block;
            margin-right: 5px;
            padding: 1px 5px;
            border-radius: 3px;
            background-color: rgba(255,0,0,0.1);
            font-size: 0.85em;
        }
        
        .error-details {
            margin-top: 5px;
            padding: 3px;
            border-top: 1px solid #eee;
        }
        
        .error-row {
            background-color: #fff8f8 !important;
        }
        
        #errorCounter {
            padding: 8px 12px !important;
            margin-top: 10px !important;
        }
        
        /* Mejorar experiencia de edición */
        .invalid-cell.editing {
            background-color: #fff !important; /* Fondo blanco al editar */
        }
        
        /* Asegurarse que el cursor de texto aparece al editar */
        .invalid-cell.editable {
            cursor: text;
        }
        
        /* El indicador de error no interfiere con el clic */
        .error-indicator {
            pointer-events: none;
        }
    `;
    document.head.appendChild(style);
    
    console.log('Sistema de tooltips mejorado inicializado');
});
