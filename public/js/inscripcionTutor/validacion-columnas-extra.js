/**
 * Correcciones finales para la validación de Email Tutor y Modalidad en inscripcionExcel.js
 * Este archivo debe incluirse después de validacion-columnas-fix.js
 */

// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🛠️ Aplicando correcciones finales para Excel');
    
    setTimeout(function() {
        // Asegurarse de que la función que detecta la presencia de delegación sea confiable
        window.detectarColumnaDelegacion = function(rowData) {
            // Si no hay datos, no puede haber delegación
            if (!rowData || !rowData.length) return false;
            
            // Verificar si existe la columna delegación (índice 11)
            return rowData.length >= 12 && 
                typeof rowData[11] !== 'undefined' && 
                rowData[11] !== null && 
                rowData[11] !== '';
        };
        
        // Extensión o corrección para validateRow
        const originalValidateRowExt = window.validateRow;
        if (typeof originalValidateRowExt === 'function') {
            window.validateRow = function(row) {
                try {
                    // Llamar a la función original
                    const result = originalValidateRowExt.call(this, row);
                    
                    // Obtener información de la fila
                    const rowIndex = row.data('row');
                    if (typeof window.excelData === 'undefined' || !window.excelData[rowIndex]) {
                        return result;
                    }
                    
                    const rowData = window.excelData[rowIndex];
                    const hasDelegacion = window.detectarColumnaDelegacion(rowData);
                    
                    // Índices para las columnas críticas
                    const emailTutorIdx = hasDelegacion ? 13 : 12;
                    const modalidadIdx = hasDelegacion ? 14 : 13;
                    
                    // Validar Email Tutor
                    if (rowData[emailTutorIdx]) {
                        const emailTutorValue = rowData[emailTutorIdx].toString();
                        const emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
                        
                        if (!emailRegex.test(emailTutorValue)) {
                            row.find(`[data-col="${emailTutorIdx}"]`)
                               .addClass('invalid-cell')
                               .attr('title', 'El email del tutor debe tener formato válido y ser @gmail.com')
                               .attr('data-bs-toggle', 'tooltip');
                        } else {
                            row.find(`[data-col="${emailTutorIdx}"]`)
                               .removeClass('invalid-cell')
                               .removeAttr('title')
                               .removeAttr('data-bs-toggle');
                        }
                    }
                    
                    // Validar Modalidad
                    if (rowData[modalidadIdx]) {
                        const modalidadValue = rowData[modalidadIdx].toString().toLowerCase();
                        
                        if (!['individual', 'duo', 'equipo'].includes(modalidadValue)) {
                            row.find(`[data-col="${modalidadIdx}"]`)
                               .addClass('invalid-cell')
                               .attr('title', 'La modalidad debe ser "Individual", "Duo" o "Equipo"')
                               .attr('data-bs-toggle', 'tooltip');
                        } else {
                            row.find(`[data-col="${modalidadIdx}"]`)
                               .removeClass('invalid-cell')
                               .removeAttr('title')
                               .removeAttr('data-bs-toggle');
                        }
                    }
                    
                    return result;
                } catch (error) {
                    console.error('Error en la validación extendida:', error);
                    // En caso de error, devolver el resultado original si es posible
                    if (typeof originalValidateRowExt === 'function') {
                        try {
                            return originalValidateRowExt.call(this, row);
                        } catch (e) {
                            console.error('Error al llamar a la validación original:', e);
                            return true; // Asumir que la fila es válida como respaldo
                        }
                    }
                    return true; // Asumir que la fila es válida como respaldo
                }
            };
            
            console.log('✅ Función validateRow extendida con validaciones adicionales');
        } else {
            console.warn('⚠️ No se pudo encontrar la función validateRow original para extender');
        }
    }, 2000); // Esperar 2 segundos para asegurar que todo esté cargado
});
