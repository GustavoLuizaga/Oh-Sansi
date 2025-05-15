/**
 * Script para conectar el selector de convocatoria con el sistema de validación
 * para garantizar que los datos de convocatoria estén actualizados
 */

$(document).ready(function() {
    // Cuando cambia la selección de convocatoria en el Excel
    $('#excel-convocatoria-dropdown').on('change', function() {
        const convocatoriaId = $(this).val();
        
        // Actualizar atributo en el body para que el sistema de validación lo utilice
        document.querySelector('body').setAttribute('data-convocatoria-id', convocatoriaId);
        
        console.log('Convocatoria cambiada:', convocatoriaId);
        
        // Reinicializar el validador si ya está disponible
        if (typeof InscripcionValidator !== 'undefined' && typeof InscripcionValidator.initialize === 'function') {
            InscripcionValidator.initialize();
        }
    });
    
    // Cuando se muestra el modal de previsualización
    $('#previewModal').on('shown.bs.modal', function() {
        // Verificar que la convocatoria esté seleccionada
        const convocatoriaId = $('#excel-convocatoria-dropdown').val();
        if (!convocatoriaId) {
            // Si no hay convocatoria seleccionada, mostrar mensaje y cerrar modal
            alert('Por favor seleccione una convocatoria antes de continuar.');
            const previewModal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
            previewModal.hide();
        } else {
            // Actualizar el nombre de la convocatoria en la previsualización
            const convocatoriaNombre = $('#excel-convocatoria-dropdown option:selected').text();
            $('#convocatoria-nombre').text(convocatoriaNombre);
            
            // También actualizar el nombre de la delegación
            const delegacionNombre = document.querySelector('body').getAttribute('data-delegacion-nombre');
            $('#delegacion-nombre').text(delegacionNombre);
        }
    });
});
