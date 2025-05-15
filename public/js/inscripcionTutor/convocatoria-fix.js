/**
 * Script para corregir los problemas de validación de convocatoria
 * y asegurar que siempre se envía el ID de convocatoria correcto
 */

$(document).ready(function() {
    // Reemplaza la función handleSubmitExcelData original
    if (typeof InscripcionValidator !== 'undefined') {
        const originalHandler = InscripcionValidator.handleSubmitExcelData;
        
        InscripcionValidator.handleSubmitExcelData = async function() {
            // Verificar errores básicos
            if (errorCount > 0) {
                alert(`Por favor, corrija los ${errorCount} errores antes de continuar.`);
                return;
            }
            
            // Verificar que hay una convocatoria seleccionada
            const convocatoriaId = $('#excel-convocatoria-dropdown').val();
            if (!convocatoriaId) {
                alert('Por favor, seleccione una convocatoria antes de continuar.');
                return;
            }
            
            // Establecer el ID de convocatoria en el body para futuras llamadas
            document.querySelector('body').setAttribute('data-convocatoria-id', convocatoriaId);
            
            // Continuar con la validación original
            // Validar grupos
            const groupErrors = validateGroups();
            if (showGroupErrors(groupErrors)) {
                return; // Si hay errores de grupo, no continuar
            }
            
            // Validar datos completos
            if (!InscripcionValidator.validarDatosCompletos()) {
                // Si hay errores avanzados, mostrarlos
                InscripcionValidator.mostrarErroresAvanzados();
                
                if (InscripcionValidator.errorCount > 0) {
                    alert(`Por favor, corrija los ${InscripcionValidator.errorCount} errores avanzados antes de continuar.`);
                }
                return;
            }
            
            // Verificar usuarios existentes
            const usuariosValidos = await InscripcionValidator.verificarUsuariosExistentes();
            
            if (!usuariosValidos) {
                // Si hay errores en usuarios, mostrarlos
                InscripcionValidator.mostrarErroresAvanzados();
                
                if (InscripcionValidator.errorCount > 0) {
                    alert(`Por favor, corrija los ${InscripcionValidator.errorCount} errores de verificación de usuarios antes de continuar.`);
                }
                return;
            }

            // Todo está correcto, continuar con la inscripción
            // Cerrar el modal de previsualización
            const previewModal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
            previewModal.hide();

            // Mostrar overlay de carga
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'flex';
            }

            // Preparar los datos para enviar al servidor
            const excelDataToSend = [];
            $('#previewTableBody tr').each(function() {
                const rowIndex = $(this).data('row');
                if (typeof rowIndex !== 'undefined' && excelData[rowIndex]) {
                    excelDataToSend.push(excelData[rowIndex]);
                }
            });

            // Enviar datos al servidor con verificación adicional de convocatoria
            $.ajax({
                url: '/register/lista/save',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    idConvocatoria: convocatoriaId, // Usar la convocatoria verificada
                    excelData: JSON.stringify(excelDataToSend)
                },
                success: function(response) {
                    // Ocultar overlay de carga
                    if (loadingOverlay) {
                        loadingOverlay.style.display = 'none';
                    }

                    if (response.success) {
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            title: 'Éxito',
                            text: response.message || 'Inscripciones completadas correctamente.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Recargar la página o redirigir si es necesario
                            window.location.reload();
                        });
                    } else {
                        // Mostrar errores
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'Ocurrió un error al procesar las inscripciones.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(error) {
                    // Ocultar overlay de carga
                    if (loadingOverlay) {
                        loadingOverlay.style.display = 'none';
                    }

                    console.error('Error al enviar datos:', error);
                    
                    let mensajeError = 'Ocurrió un error al enviar los datos. Por favor intente nuevamente.';
                    
                    // Intentar extraer mensaje de error específico
                    if (error.responseJSON && error.responseJSON.message) {
                        mensajeError = error.responseJSON.message;
                    } else if (error.responseText) {
                        try {
                            const respuesta = JSON.parse(error.responseText);
                            if (respuesta.message) {
                                mensajeError = respuesta.message;
                            }
                        } catch (e) {
                            // Si no es JSON válido, usar el texto de respuesta directamente
                            if (error.responseText.length < 200) {  // Solo si es un mensaje corto
                                mensajeError = error.responseText;
                            }
                        }
                    }
                    
                    // Mostrar mensaje de error
                    Swal.fire({
                        title: 'Error en la inscripción',
                        text: mensajeError,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        };
        
        console.log('Se ha mejorado el manejador de envío de inscripción con validación de convocatoria');
    } else {
        console.error('InscripcionValidator no está disponible');
    }
    
    // Asegurar que se valida la convocatoria al abrir el modal
    $('#previewModal').on('show.bs.modal', function (e) {
        const convocatoriaId = $('#excel-convocatoria-dropdown').val();
        if (!convocatoriaId) {
            alert('Por favor, seleccione una convocatoria antes de continuar.');
            e.preventDefault();
            return false;
        }
    });
});
