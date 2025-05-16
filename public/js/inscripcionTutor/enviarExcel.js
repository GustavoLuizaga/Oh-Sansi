// Función principal para enviar datos
async function enviarDatosExcel(datos, idConvocatoria) {
    try {
        // Mostrar overlay de carga
        document.getElementById('loadingOverlay').style.display = 'flex';

        // Validar datos antes de enviar
        const validacion = await validarExcel(datos, idConvocatoria);
        if (!validacion.valido) {
            mostrarErrores(validacion.errores);
            return false;
        }

        // Enviar datos al servidor
        const response = await fetch('/registrar-lista-estudiantes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                estudiantes: datos,
                idConvocatoria: idConvocatoria
            })
        });

        const resultado = await response.json();

        if (resultado.success) {
            // Mostrar mensaje de éxito
            const successMessage = document.getElementById('successMessage');
            const successText = document.getElementById('successText');
            successText.textContent = resultado.message;
            successMessage.style.display = 'flex';
            setTimeout(() => {
                successMessage.style.display = 'none';
                // Recargar la página después de 2 segundos
                window.location.reload();
            }, 2000);
            return true;        } else {
            // Si hay errores específicos, mostrarlos
            if (resultado.errores && Array.isArray(resultado.errores)) {
                mostrarErrores(resultado.errores);
            } else {
                mostrarErrores([resultado.message || 'Ocurrió un error al procesar la inscripción']);
            }
            return false;
        }
    } catch (error) {
        console.error('Error al enviar datos:', error);
        mostrarErrores(['Error al procesar la inscripción']);
        return false;
    } finally {
        // Ocultar overlay de carga
        document.getElementById('loadingOverlay').style.display = 'none';
    }
}

// Función para mostrar errores
function mostrarErrores(errores) {
    const errorContainer = document.getElementById('errorContainer');
    if (!errorContainer) {
        console.error('No se encontró el contenedor de errores');
        // Intentar crear el contenedor si no existe
        try {
            const modalBody = document.querySelector('.modal-body');
            if (modalBody) {
                const newErrorContainer = document.createElement('div');
                newErrorContainer.id = 'errorContainer';
                newErrorContainer.className = 'alert alert-danger mb-3';
                modalBody.insertBefore(newErrorContainer, modalBody.firstChild);
                // Ahora usamos el contenedor recién creado
                mostrarErrores(errores);
                return;
            }
        } catch (e) {
            console.error('Error al crear el contenedor de errores:', e);
            alert('Errores: ' + errores.join(', '));
            return;
        }
        return;
    }

    // Limpiar contenedor y establecer estilo
    errorContainer.innerHTML = '';
    errorContainer.style.display = 'block';
    
    // Añadir icono y título
    const header = document.createElement('div');
    header.className = 'mb-2';
    header.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <strong>Errores encontrados:</strong>';
    errorContainer.appendChild(header);
    
    // Crear lista de errores
    const ul = document.createElement('ul');
    ul.className = 'error-list mb-0';

    errores.forEach(error => {
        const li = document.createElement('li');
        // Usar innerHTML en lugar de textContent para manejar posible HTML en mensajes
        li.innerHTML = error;
        ul.appendChild(li);
    });

    errorContainer.appendChild(ul);
}

// Función para limpiar errores
function limpiarErrores() {
    const errorContainer = document.getElementById('errorContainer');
    if (errorContainer) {
        errorContainer.innerHTML = '';
        errorContainer.style.display = 'none';
    }
}

// Exportar funciones
window.enviarDatosExcel = enviarDatosExcel;
window.limpiarErrores = limpiarErrores;
