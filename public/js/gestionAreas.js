document.addEventListener('DOMContentLoaded', function() {
    // Formulario para crear nueva área
    const formNuevaArea = document.getElementById('formNuevaArea');
    
    if (formNuevaArea) {
        formNuevaArea.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Enviar petición AJAX para crear área
            fetch('/areas', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaAreaModal'));
                    modal.hide();
                    
                    // Recargar página para mostrar la nueva área
                    window.location.reload();
                } else {
                    // Limpiar errores anteriores
                    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    
                    // // Mostrar errores de validación
                    // const errores = data.errors;
                    // for (const campo in errores) {
                    //     const input = document.querySelector(`[name="${campo}"]`);
                    //     if (input) {
                    //         input.classList.add('is-invalid');
                            
                    //         // Crear mensaje de error
                    //         const divError = document.createElement('div');
                    //         divError.classList.add('invalid-feedback');
                    //         divError.textContent = errores[campo][0];
                            
                    //         // Insertar mensaje después del input
                    //         input.parentNode.appendChild(divError);
                    //     }
                    // }
                }
            })
            .catch(error => {
                console.error('Error en creación:', error);
            });
        });
        
        // Limpiar errores cuando se cierra el modal
        document.getElementById('nuevaAreaModal').addEventListener('hidden.bs.modal', function() {
            formNuevaArea.reset();
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        });
    }
    
    // Modal de eliminación
    const deleteModal = document.getElementById('ConfirmarBorradoModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            try {
                const button = event.relatedTarget;
                if (!button) return;
                
                const idArea = button.getAttribute('data-id');
                const nombreArea = button.getAttribute('data-nombre');
                
                if (!idArea || !nombreArea) return;
                
                const nombreElement = document.getElementById('nombreAreaEliminar');
                if (!nombreElement) return;
                
                // Asignar el nombre al elemento correspondiente
                nombreElement.textContent = nombreArea;
                
                // Configurar acción de eliminación
                const confirmButton = document.getElementById('confirmarEliminar');
                if (confirmButton) {
                    // Almacenamos el ID como atributo de datos en el botón confirmar
                    confirmButton.setAttribute('data-area-id', idArea);
                    
                    // Eliminamos eventos anteriores para evitar duplicados
                    confirmButton.replaceWith(confirmButton.cloneNode(true));
                    
                    // Volvemos a obtener la referencia al botón clonado
                    const newConfirmButton = document.getElementById('confirmarEliminar');
                    
                    // Agregamos el nuevo evento click
                    newConfirmButton.addEventListener('click', function() {
                        const areaId = this.getAttribute('data-area-id');
                        if (!areaId) return;
                        
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        if (!token) return;
                        
                        // Cerrar el modal inmediatamente para mejorar la experiencia de usuario
                        const modal = bootstrap.Modal.getInstance(deleteModal);
                        modal.hide();
                        
                        fetch(`/areas/${areaId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // 1. Cerrar el modal
                                const modal = bootstrap.Modal.getInstance(deleteModal);
                                modal.hide();
                                
                                // 2. Eliminar la fila de la tabla sin recargar
                                const filaAEliminar = document.querySelector(`button[data-id="${areaId}"]`).closest('tr');
                                if (filaAEliminar) {
                                    filaAEliminar.remove();
                                    
                                    // 3. Mostrar mensaje de éxito (opcional)
                                    const alerta = document.createElement('div');
                                    alerta.className = 'alert alert-success alert-dismissible fade show';
                                    alerta.innerHTML = `
                                        Área eliminada correctamente
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    `;
                                    
                                    // Inserta el mensaje al inicio del contenedor
                                    document.querySelector('.area-container').prepend(alerta);
                                    
                                    // Elimina el mensaje después de 3 segundos
                                    setTimeout(() => alerta.remove(), 3000);
                                } else {
                                    // Si no encuentra la fila, recarga como fallback
                                    window.location.reload();
                                }
                            } else {
                                console.error('Error en eliminación:', data.message || 'Error desconocido');
                                window.location.reload(); // Recarga si hay error
                            }
                        })
                    });
                }
            } catch (error) {
                console.error('Error en modal:', error);
                // No mostramos alerta, solo registramos en consola
            }
        });
    }
});