// Add this to your existing gestionAreas.js file
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
                    
                     // Mostrar errores de validación
                    // const errores = data.errors;
                    // for (const campo in errores) {
                    //     const input = document.querySelector(`[name="${campo}"]`);
                    //     if (input) {
                    //         input.classList.add('is-invalid');                       
                    //          // Crear mensaje de error
                    //         const divError = document.createElement('div');
                    //         divError.classList.add('invalid-feedback');
                    //         divError.textContent = errores[campo][0];
                            
                    //          // Insertar mensaje después del input
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
    // Edit Area Modal
    const editModal = document.getElementById('EditarAreaModal');
    if (editModal) {
        // Populate the edit modal with area data when it's shown
        editModal.addEventListener('show.bs.modal', function(event) {
            try {
                const button = event.relatedTarget;
                if (!button) return;
                
                const idArea = button.getAttribute('data-id');
                const nombreArea = button.getAttribute('data-nombre');
                
                if (!idArea || !nombreArea) return;
                
                // Set the area name in the form input
                const nombreInput = editModal.querySelector('input[name="nombre"]');
                if (nombreInput) {
                    nombreInput.value = nombreArea;
                }
                
                // Update the form action URL to point to the update endpoint
                const editForm = document.getElementById('formEditarArea');
                if (editForm) {
                    editForm.action = `/areas/${idArea}`;
                    
                    // Add a hidden method field for PUT request
                    let methodField = editForm.querySelector('input[name="_method"]');
                    if (!methodField) {
                        methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        editForm.appendChild(methodField);
                    }
                    methodField.value = 'PUT';
                }
            } catch (error) {
                console.error('Error populating edit modal:', error);
            }
        });
        
        // Handle edit form submission with AJAX
        const formEditarArea = document.getElementById('formEditarArea');
        if (formEditarArea) {
            formEditarArea.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const actionUrl = this.action;
                
                // Send AJAX request to update area
                fetch(actionUrl, {
                    method: 'POST', // Will be converted to PUT by Laravel due to _method field
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Close the modal
                        const modal = bootstrap.Modal.getInstance(editModal);
                        modal.hide();
                        
                        // Update the row in the table without reloading
                        const areaId = actionUrl.split('/').pop();
                        const tableRow = document.querySelector(`button[data-id="${areaId}"]`).closest('tr');
                        
                        if (tableRow) {
                            // Update the area name in the table
                            const nameCell = tableRow.querySelector('td:first-child');
                            if (nameCell) {
                                nameCell.textContent = formData.get('nombre');
                            }
                            
                            // Update the data-nombre attribute in the buttons
                            const editButton = tableRow.querySelector('.btn-edit');
                            const deleteButton = tableRow.querySelector('.btn-delete');
                            
                            if (editButton) {
                                editButton.setAttribute('data-nombre', formData.get('nombre'));
                            }
                            
                            if (deleteButton) {
                                deleteButton.setAttribute('data-nombre', formData.get('nombre'));
                            }
                            
                            // // Show success message
                            // const alerta = document.createElement('div');
                            // alerta.className = 'alert alert-success alert-dismissible fade show';
                            // alerta.innerHTML = `
                            //     Área actualizada correctamente
                            //     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            // `;
                            
                            // Insert the message at the beginning of the container
                            document.querySelector('.area-container').prepend(alerta);
                            
                            // Remove the message after 3 seconds
                            setTimeout(() => alerta.remove(), 3000);
                        } else {
                            // If row not found, reload as fallback
                            window.location.reload();
                        }
                    } else {
                        // Clear previous errors
                        editModal.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                        editModal.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                        
                        // Show validation errors if any
                        if (data.errors) {
                            const errores = data.errors;
                            for (const campo in errores) {
                                const input = editModal.querySelector(`[name="${campo}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    
                                    // Create error message
                                    const divError = document.createElement('div');
                                    divError.classList.add('invalid-feedback');
                                    divError.textContent = errores[campo][0];
                                    
                                    // Insert message after the input
                                    input.parentNode.appendChild(divError);
                                }
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error en actualización:', error);
                });
            });
            
            // Clear errors when the modal is closed
            editModal.addEventListener('hidden.bs.modal', function() {
                formEditarArea.reset();
                editModal.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                editModal.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            });
        }
    }
});


//Buscar área por nombre
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchArea');
    const tableRows = document.querySelectorAll('.areas-table tbody tr');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();

        tableRows.forEach(row => {
            const areaName = row.querySelector('td:first-child').textContent.toLowerCase();
            
            if (areaName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Mostrar mensaje cuando no hay resultados
        const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
        const tbody = document.querySelector('.areas-table tbody');
        const noResultsRow = tbody.querySelector('.no-results');

        if (visibleRows.length === 0) {
            if (!noResultsRow) {
                const tr = document.createElement('tr');
                tr.className = 'no-results';
                tr.innerHTML = '<td colspan="2" class="text-center text-danger">No se encontraron áreas con ese nombre</td>';
                tbody.appendChild(tr);
            }
        } else if (noResultsRow) {
            noResultsRow.remove();
        }
    });
});


// Manejador para el select de ordenamiento

document.addEventListener('DOMContentLoaded', function() {
    // Manejador para el select de ordenamiento
    const orderSelect = document.getElementById('orderBy');
    if (orderSelect) {
        orderSelect.addEventListener('change', function() {
            // Obtener los parámetros actuales de la URL
            const urlParams = new URLSearchParams(window.location.search);
            
            // Actualizar o agregar el parámetro de ordenamiento
            if (this.value) {
                urlParams.set('orderBy', this.value);
            } else {
                urlParams.delete('orderBy');
            }
            
            // Mantener el término de búsqueda si existe
            const searchTerm = document.getElementById('searchArea').value;
            if (searchTerm) {
                urlParams.set('search', searchTerm);
            }
            
            // Recargar la página con los nuevos parámetros
            window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
        });
    }
});