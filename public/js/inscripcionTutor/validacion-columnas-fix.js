/**
 * Correcciones para la validación de Email Tutor y Modalidad en inscripcionExcel.js
 * Este archivo debe incluirse después del archivo principal inscripcionExcel.js
 */

// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando correcciones para el Excel de inscripciones');
    
    // Corregir la función _getColumnIndex si se está tratando de usar
    window._getColumnIndex = function(columnName) {
        let index = -1;
        $('#previewTable thead th').each(function(i) {
            if ($(this).text().trim() === columnName) {
                // Restar 2 por la columna de acciones y el número de fila
                index = i - 2;
                return false; // Romper el bucle cuando se encuentra
            }
        });
        
        // Si no se encuentra, usar índices de respaldo
        if (index === -1) {
            console.warn(`No se encontró la columna "${columnName}", usando índice de respaldo`);
            if (columnName === 'Email Tutor') {
                // Determinar si hay columna de delegación
                const hasDelegacion = (function() {
                    if (typeof window.excelData === 'undefined' || !window.excelData.length) {
                        return false;
                    }
                    
                    for (let i = 0; i < window.excelData.length; i++) {
                        if (window.excelData[i] && window.excelData[i].length >= 12 && 
                            typeof window.excelData[i][11] !== 'undefined' && 
                            window.excelData[i][11] !== null && window.excelData[i][11] !== '') {
                            return true;
                        }
                    }
                    return false;
                })();
                
                return hasDelegacion ? 13 : 12; // Índice respaldo para Email Tutor
            } else if (columnName === 'Modalidad') {
                const hasDelegacion = (function() {
                    if (typeof window.excelData === 'undefined' || !window.excelData.length) {
                        return false;
                    }
                    
                    for (let i = 0; i < window.excelData.length; i++) {
                        if (window.excelData[i] && window.excelData[i].length >= 12 && 
                            typeof window.excelData[i][11] !== 'undefined' && 
                            window.excelData[i][11] !== null && window.excelData[i][11] !== '') {
                            return true;
                        }
                    }
                    return false;
                })();
                
                return hasDelegacion ? 14 : 13; // Índice respaldo para Modalidad
            } else if (columnName === 'Código Invitación') {
                const hasDelegacion = (function() {
                    if (typeof window.excelData === 'undefined' || !window.excelData.length) {
                        return false;
                    }
                    
                    for (let i = 0; i < window.excelData.length; i++) {
                        if (window.excelData[i] && window.excelData[i].length >= 12 && 
                            typeof window.excelData[i][11] !== 'undefined' && 
                            window.excelData[i][11] !== null && window.excelData[i][11] !== '') {
                            return true;
                        }
                    }
                    return false;
                })();
                
                return hasDelegacion ? 15 : 14; // Índice respaldo para Código Invitación
            }
        }
        
        return index;
    };
    
    // Esperar un poco para asegurarnos que jQuery esté disponible
    setTimeout(function() {
        if (typeof $ !== 'undefined') {
            // Agregar función de depuración para mostrar mapeo de columnas
            window.debugColumnMapping = function() {
                console.log('Mapeando columnas de la tabla...');
                const columnMap = {};
                
                $('#previewTable thead th').each(function(index) {
                    const headerText = $(this).text().trim();
                    columnMap[headerText] = index - 2; // Restar 2 por columnas de acciones y número
                });
                
                console.log('Mapa de columnas:', columnMap);
                return columnMap;
            };
            
            // Agregar evento cuando se muestre el modal de previsualización
            $(document).on('shown.bs.modal', '#previewModal', function() {
                setTimeout(function() {
                    // Depurar columnas después que se muestre el modal
                    if (typeof window.debugColumnMapping === 'function') {
                        window.debugColumnMapping();
                    }
                    
                    // Conectar un observador para detectar cuándo se ha completado la validación
                    const observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                                // Verificar si hay celdas con error relacionadas con email o modalidad
                                $('#previewTableBody .invalid-cell').each(function() {
                                    const errorText = $(this).attr('title') || '';
                                    if (errorText.includes('email del tutor') || errorText.includes('modalidad')) {
                                        console.log('Celda con error encontrada:', errorText);
                                    }
                                });
                            }
                        });
                    });
                    
                    // Iniciar observación de cambios en las celdas
                    const config = { attributes: true, childList: false, characterData: false, subtree: true };
                    observer.observe(document.querySelector('#previewTableBody'), config);
                    
                }, 1000);
            });
            
            // Sobreescribir la función que valida filas si está disponible
            const originalValidateRow = window.validateRow || null;
            
            if (originalValidateRow) {
                console.log('Sobreescribiendo función de validación para corregir detección de columnas');
                
                window.validateRow = function(row) {
                    // Obtener los datos de la fila
                    const rowIndex = row.data('row');
                    const rowData = window.excelData[rowIndex] || [];
                    
                    // Usar la función original con los mismos parámetros
                    const result = originalValidateRow.call(this, row);
                    
                    // Corregir explícitamente cualquier clasificación errónea
                    const columnMap = window.debugColumnMapping();
                    
                    if (columnMap['Email Tutor'] !== undefined && rowData[columnMap['Email Tutor']]) {
                        const emailTutorValue = rowData[columnMap['Email Tutor']];
                        const emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
                        
                        if (!emailRegex.test(emailTutorValue)) {
                            row.find(`[data-col="${columnMap['Email Tutor']}"]`)
                               .addClass('invalid-cell')
                               .attr('title', 'El email del tutor debe tener formato válido y ser @gmail.com');
                        } else {
                            row.find(`[data-col="${columnMap['Email Tutor']}"]`)
                               .removeClass('invalid-cell')
                               .attr('title', '');
                        }
                    }
                    
                    if (columnMap['Modalidad'] !== undefined && rowData[columnMap['Modalidad']]) {
                        const modalidadValue = rowData[columnMap['Modalidad']].toString().toLowerCase();
                        
                        if (!['individual', 'duo', 'equipo'].includes(modalidadValue)) {
                            row.find(`[data-col="${columnMap['Modalidad']}"]`)
                               .addClass('invalid-cell')
                               .attr('title', 'La modalidad debe ser "Individual", "Duo" o "Equipo"');
                        } else {
                            row.find(`[data-col="${columnMap['Modalidad']}"]`)
                               .removeClass('invalid-cell')
                               .attr('title', '');
                        }
                    }
                    
                    return result;
                };
            } else {
                console.warn('No se encontró la función validateRow para sobreescribir');
            }
            
            console.log('Correcciones inicializadas');
        } else {
            console.error('jQuery no está disponible para aplicar correcciones');
        }
    }, 1000);
});
