/**
 * Script para ajustar dinámicamente las columnas de la tabla de previsualización de Excel
 */
$(document).ready(function() {
    // Función para ajustar los anchos de las columnas
    function adjustColumnWidths() {
        // Solo proceder si la tabla es visible
        if ($('#previewTable').is(':visible')) {
            $('#previewTable').find('th').each(function(index) {
                let maxWidth = $(this).width();
                
                // Buscar el ancho máximo en las celdas de esta columna
                const cellsInColumn = $('#previewTable tbody td:nth-child(' + (index + 1) + ')');
                cellsInColumn.each(function() {
                    const cellWidth = $(this).width();
                    if (cellWidth > maxWidth) {
                        maxWidth = cellWidth;
                    }
                });
                
                // Establecer un ancho mínimo y máximo según el tipo de columna
                let minWidth = 100;
                let finalWidth = Math.max(minWidth, maxWidth);
                
                // Si es columna de acción o número de fila, establecer anchos fijos
                if (index === 0) {
                    finalWidth = 60;
                } else if (index === 1) {
                    finalWidth = 50;
                }
                
                // Aplicar el ancho a la columna y sus celdas
                $(this).width(finalWidth);
                cellsInColumn.width(finalWidth);
            });
        }
    }
    
    // Ajustar cuando se muestra el modal
    $('#previewModal').on('shown.bs.modal', function() {
        setTimeout(adjustColumnWidths, 500);
    });
    
    // Ajustar al cambiar el tamaño de la ventana
    $(window).resize(function() {
        if ($('#previewModal').hasClass('show')) {
            adjustColumnWidths();
        }
    });
    
    // Ajustar después de realizar búsquedas o cambiar páginas
    $('#previewTable').on('draw.dt', function() {
        setTimeout(adjustColumnWidths, 200);
    });
});
