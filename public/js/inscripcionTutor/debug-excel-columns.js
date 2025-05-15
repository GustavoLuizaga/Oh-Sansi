/**
 * Script de depuración para el mapeo de columnas en la vista previa de Excel
 * Este archivo proporciona utilidades para diagnosticar problemas con el mapeo de columnas
 * en la funcionalidad de inscripción mediante Excel
 */

// Esta función se ejecutará cuando el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    console.log("⏱️ Inicializando herramientas de depuración de columnas Excel...");
    
    // Agregar función global para depurar columnas y filas
    window.debugExcelData = function() {
        // Crear un objeto para almacenar información de diagnóstico
        const diagnostics = {
            headers: {},
            firstRow: {},
            columnMatch: {}
        };
        
        // Obtener encabezados de la tabla
        const headers = [];
        $('#previewTable thead th').each(function(index) {
            const text = $(this).text().trim();
            headers[index] = text;
            diagnostics.headers[index] = text;
        });
        
        // Verificar primera fila de datos 
        if (typeof window.excelData !== 'undefined' && window.excelData.length > 0) {
            diagnostics.firstRow = window.excelData[0];
            
            // Intentar hacer coincidir encabezados con datos
            headers.forEach((header, index) => {
                if (index > 1) { // Saltar columnas de acción y número
                    const dataIndex = index - 2;
                    diagnostics.columnMatch[header] = {
                        expected: dataIndex,
                        value: window.excelData[0][dataIndex]
                    };
                }
            });
        }
        
        // Encontrar columnas críticas
        const criticalColumns = ['Email Tutor', 'Modalidad', 'Código Invitación'];
        criticalColumns.forEach(colName => {
            let foundIndex = -1;
            headers.forEach((header, index) => {
                if (header === colName) {
                    foundIndex = index;
                }
            });
            
            if (foundIndex > -1) {
                diagnostics.criticalColumns = diagnostics.criticalColumns || {};
                diagnostics.criticalColumns[colName] = {
                    headerIndex: foundIndex,
                    dataIndex: foundIndex - 2
                };
            }
        });
        
        console.log("📊 Diagnóstico de columnas Excel:", diagnostics);
        return diagnostics;
    };
    
    // Adjuntar a evento cuando se muestre el modal de previsualización
    $('#previewModal').on('shown.bs.modal', function() {
        setTimeout(function() {
            if (typeof window.debugExcelData === 'function') {                window.debugExcelData();
                
                // Capturar y registrar errores durante la validación
                const originalConsoleError = console.error;
                console.error = function() {
                    // Llamar a la función original
                    originalConsoleError.apply(console, arguments);
                    
                    // Registrar el error con información adicional de contexto
                    const errorInfo = {
                        timestamp: new Date().toISOString(),
                        arguments: Array.from(arguments),
                        excelDataLength: window.excelData ? window.excelData.length : 'undefined',
                        tableHeaders: [],
                        stackTrace: new Error().stack
                    };
                    
                    try {
                        $('#previewTable thead th').each(function() {
                            errorInfo.tableHeaders.push($(this).text().trim());
                        });
                    } catch (e) {
                        errorInfo.headerError = e.message;
                    }
                    
                    console.log('📝 Error contextualizado:', errorInfo);
                };
            }
        }, 1000);
    });
    
    // Monitorizar cambios en la tabla para detectar problemas
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Si se agregan nodos a la tabla, verificar nuevamente
                setTimeout(function() {
                    if (typeof window.debugExcelData === 'function') {
                        window.debugExcelData();
                    }
                }, 500);
            }
        });
    });
    
    // Iniciar observación cuando se muestre el modal
    $('#previewModal').on('shown.bs.modal', function() {
        const previewTable = document.getElementById('previewTable');
        if (previewTable) {
            observer.observe(previewTable, {
                childList: true,
                subtree: true
            });
        }
    });
    
    // Detener observación cuando se cierre el modal
    $('#previewModal').on('hidden.bs.modal', function() {
        observer.disconnect();
    });
    
    console.log("✅ Herramientas de depuración de columnas Excel inicializadas.");
});
