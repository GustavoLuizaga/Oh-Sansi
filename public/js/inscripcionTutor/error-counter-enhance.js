/**
 * Script para mejorar la visualización del contador de errores
 * Muestra un desglose detallado de los errores por tipo
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando mejoras del contador de errores');
    
    // Función para mejorar el contador de errores
    function enhanceErrorCounter() {
        const errorCounter = document.getElementById('errorCounter');
        if (!errorCounter) return;
        
        // Añadir contenedor para detalles de errores si no existe
        if (!document.getElementById('errorDetails')) {
            const errorDetailsContainer = document.createElement('div');
            errorDetailsContainer.id = 'errorDetails';
            errorDetailsContainer.className = 'error-details-container';
            errorCounter.appendChild(errorDetailsContainer);
        }
        
        // Estilizar el contador de errores
        errorCounter.style.borderRadius = '4px';
        errorCounter.style.padding = '10px 15px';
        errorCounter.style.marginBottom = '15px';
        errorCounter.style.fontWeight = '500';
        
        // Observer para actualizar los detalles cuando cambie el número de errores
        const errorTextObserver = new MutationObserver(function(mutations) {
            updateErrorDetails();
        });
        
        const errorCountText = document.getElementById('errorCountText');
        if (errorCountText) {
            errorTextObserver.observe(errorCountText, {
                childList: true,
                characterData: true,
                subtree: true
            });
        }
        
        // Función para actualizar los detalles de errores
        function updateErrorDetails() {
            const errorDetailsContainer = document.getElementById('errorDetails');
            if (!errorDetailsContainer) return;
            
            // Obtener todas las celdas con errores
            const invalidCells = document.querySelectorAll('.invalid-cell');
            if (invalidCells.length === 0) {
                errorDetailsContainer.innerHTML = '';
                return;
            }
            
            // Categorizar errores por tipo
            const errorsByType = {};
            const errorMessages = {};
            
            invalidCells.forEach(function(cell) {
                const errorMsg = cell.getAttribute('title') || '';
                const rowIndex = cell.closest('tr')?.rowIndex || 0;
                let errorType = 'Otro';
                
                // Determinar tipo de error
                if (errorMsg.includes('email')) errorType = 'Email';
                else if (errorMsg.includes('modalidad')) errorType = 'Modalidad';
                else if (errorMsg.includes('código')) errorType = 'Código';
                else if (errorMsg.includes('nombre')) errorType = 'Nombre';
                
                // Contar errores por tipo
                errorsByType[errorType] = (errorsByType[errorType] || 0) + 1;
                
                // Guardar ejemplos de mensajes de error
                if (!errorMessages[errorType] || errorMessages[errorType].count < 3) {
                    errorMessages[errorType] = errorMessages[errorType] || { examples: [], count: 0 };
                    errorMessages[errorType].examples.push(`Fila ${rowIndex}: ${errorMsg}`);
                    errorMessages[errorType].count++;
                }
            });
            
            // Crear resumen visual
            let detailsHTML = '<div class="error-summary">';
            for (const [type, count] of Object.entries(errorsByType)) {
                let badgeColor = '#ffc107'; // amarillo por defecto
                if (type === 'Email') badgeColor = '#dc3545'; // rojo
                else if (type === 'Modalidad') badgeColor = '#fd7e14'; // naranja
                else if (type === 'Código') badgeColor = '#0dcaf0'; // azul claro
                
                detailsHTML += `
                    <div class="error-type-badge" style="background-color: ${badgeColor}20; border-left: 3px solid ${badgeColor}">
                        <span class="error-type-name">${type}</span>
                        <span class="error-type-count">${count}</span>
                        <div class="error-examples">
                            ${errorMessages[type]?.examples.map(msg => `<div class="error-example">${msg}</div>`).join('') || ''}
                            ${errorMessages[type]?.count > 3 ? `<div class="more-errors">+${errorsByType[type] - 3} errores más...</div>` : ''}
                        </div>
                    </div>
                `;
            }
            detailsHTML += '</div>';
            
            // Actualizar el contenedor
            errorDetailsContainer.innerHTML = detailsHTML;
        }
        
        // Actualizar inicialmente
        updateErrorDetails();
    }
    
    // Inicializar cuando se muestra el modal
    $('#previewModal').on('shown.bs.modal', function() {
        setTimeout(enhanceErrorCounter, 500);
        setTimeout(enhanceErrorCounter, 1500); // Volver a intentar después
    });
    
    // Actualizar cuando se valida una celda
    $(document).on('blur', '.editable', function() {
        setTimeout(enhanceErrorCounter, 200);
    });
    
    // Añadir estilos para el contador mejorado
    const style = document.createElement('style');
    style.textContent = `
        .error-details-container {
            margin-top: 10px;
            max-height: 150px;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .error-summary {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .error-type-badge {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            padding: 6px 10px;
            border-radius: 4px;
            position: relative;
        }
        
        .error-type-name {
            font-weight: 500;
            margin-right: 10px;
        }
        
        .error-type-count {
            background: rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 1px 8px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .error-examples {
            flex-basis: 100%;
            margin-top: 5px;
            font-size: 12px;
            color: #666;
            padding-left: 5px;
            border-left: 1px dashed rgba(0,0,0,0.1);
        }
        
        .error-example {
            margin: 3px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        
        .more-errors {
            font-style: italic;
            font-size: 11px;
            color: #888;
            margin-top: 2px;
        }
        
        #errorCounter.has-errors {
            animation: pulse-error 2s infinite;
        }
        
        @keyframes pulse-error {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
            70% { box-shadow: 0 0 0 5px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
    `;
    document.head.appendChild(style);
    
    console.log('Mejoras del contador de errores inicializadas');
});
