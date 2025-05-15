/**
 * Script para mostrar información de convocatoria y delegación en el modal de previsualización
 */
$(document).ready(function() {
    // Nombre de delegación almacenado
    let delegacionNombreCache = '';
    
    // Función para actualizar información de convocatoria y delegación en el modal
    function updatePreviewInfo() {
        // Obtener el ID de la convocatoria seleccionada
        const convocatoriaId = $('#excel-convocatoria-dropdown').val();
        if (!convocatoriaId) {
            $('#convocatoria-nombre').text('No se ha seleccionado ninguna convocatoria');
            return;
        }

        // Obtener nombre de la convocatoria del dropdown
        const convocatoriaNombre = $('#excel-convocatoria-dropdown option:selected').text().trim();
        $('#convocatoria-nombre').text(convocatoriaNombre);
        
        // Si ya tenemos el nombre de delegación en caché, usarlo
        if (delegacionNombreCache) {
            $('#delegacion-nombre').text(delegacionNombreCache);
            return;
        }
        
        // Obtener y mostrar el nombre de la delegación (colegio) del delegado
        try {
            // Intentar obtener de la página directamente del atributo data
            const delegacionNombre = $('body').attr('data-delegacion-nombre');
            
            if (delegacionNombre && delegacionNombre.trim() !== '') {
                delegacionNombreCache = delegacionNombre.trim();
                $('#delegacion-nombre').text(delegacionNombreCache);
            } else {
                // Si no está disponible directamente, intentar obtenerlo mediante AJAX
                $.ajax({
                    url: '/delegacion/info',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data && data.nombre) {
                            delegacionNombreCache = data.nombre;
                            $('#delegacion-nombre').text(delegacionNombreCache);
                        } else {
                            $('#delegacion-nombre').text('Colegio del delegado');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al obtener información de delegación:', error);
                        $('#delegacion-nombre').text('Colegio del delegado');
                    }
                });
            }
        } catch (e) {
            console.error('Error al procesar información de delegación:', e);
            $('#delegacion-nombre').text('Colegio del delegado');
        }
    }
    
    // Inicializar al cargar la página
    setTimeout(function() {
        // Intentar obtener de la página directamente del atributo data
        const delegacionNombre = $('body').attr('data-delegacion-nombre');
        if (delegacionNombre && delegacionNombre.trim() !== '') {
            delegacionNombreCache = delegacionNombre.trim();
        }
    }, 500);
    
    // Actualizar cuando se cambia la convocatoria
    $('#excel-convocatoria-dropdown').on('change', function() {
        updatePreviewInfo();
    });
    
    // Actualizar cuando se muestra el modal
    $('#previewModal').on('shown.bs.modal', function() {
        updatePreviewInfo();
    });
    
    // Si el botón de previsualizar es clickeado, actualizar también
    $(document).on('click', '#previewBtn', function() {
        // La actualización se hará cuando se muestre el modal
    });
});
