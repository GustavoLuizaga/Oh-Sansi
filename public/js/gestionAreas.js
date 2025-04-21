document.addEventListener('DOMContentLoaded', function() {
    // Helper function to show alerts
    function showAlert(message, type = 'success') {
        const alerta = document.createElement('div');
        alerta.className = `alert alert-${type} alert-dismissible fade show`;
        alerta.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.area-container');
        if (container) {
            container.prepend(alerta);
            setTimeout(() => alerta.remove(), 5000);
        }
    }

    // Helper function to resetForm
    function resetForm(form, submitButton = null) {
        form.reset();
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = submitButton.dataset.originalText || 'Submit';
        }
    }

    // Formulario/Modal para crear nueva área
    const formNuevaArea = document.getElementById('formNuevaArea');
    if (formNuevaArea) {
        const submitButton = formNuevaArea.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.dataset.originalText = submitButton.innerHTML;
        }

        formNuevaArea.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
            }
            
            const formData = new FormData(this);
            
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
                    const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaAreaModal'));
                    if (modal) modal.hide();
                    
                    showAlert('Área creada correctamente');
                    
                    // Agregar la nueva fila a la tabla
                    const tablaAreas = document.querySelector('table tbody');
                    if (tablaAreas) {
                        const nuevaFila = document.createElement('tr');
                        nuevaFila.innerHTML = `
                        <td>${formData.get('nombre')}</td>
                        <td class="action-cell">
                            <button class="btn-action btn-edit" 
                                    data-id="${data.area.idArea}"
                                    data-nombre="${formData.get('nombre')}" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#EditarAreaModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-delete" 
                                    data-id="${data.area.idArea}" 
                                    data-nombre="${formData.get('nombre')}"  
                                    data-bs-toggle="modal" 
                                    data-bs-target="#ConfirmarBorradoModal">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td> `;
                        tablaAreas.appendChild(nuevaFila);
                    }
                    
                    resetForm(formNuevaArea, submitButton);
                } else {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = submitButton.dataset.originalText;
                    }
                    
                    // Mostrar errores de validación
                    if (data.errors) {
                        formNuevaArea.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                        formNuevaArea.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                        
                        for (const campo in data.errors) {
                            const input = formNuevaArea.querySelector(`[name="${campo}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                
                                const divError = document.createElement('div');
                                divError.classList.add('invalid-feedback');
                                divError.textContent = data.errors[campo][0];
                                input.parentNode.appendChild(divError);
                            }
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error en creación:', error);
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = submitButton.dataset.originalText;
                }
                showAlert('Error al crear el área', 'danger');
            });
        });
        
        // Limpiar formulario cuando se cierra el modal
        document.getElementById('nuevaAreaModal').addEventListener('hidden.bs.modal', function() {
            resetForm(formNuevaArea, submitButton);
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
                        // Desactivar el botón para evitar múltiples envíos
                        this.disabled = true;
                        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Eliminando...';
                        
                        const areaId = this.getAttribute('data-area-id');
                        if (!areaId) {
                            this.disabled = false;
                            this.innerHTML = 'Eliminar';
                            return;
                        }
                        
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        if (!token) {
                            this.disabled = false;
                            this.innerHTML = 'Eliminar';
                            return;
                        }
                        
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
                            console.log('Respuesta del backend:', data);
                        
                            if (data.status === 'success') {
                                // 1. Cerrar el modal
                                const modal = bootstrap.Modal.getInstance(deleteModal);
                                if (modal) modal.hide();
                        
                                // 2. Eliminar la fila correspondiente
                                const filaAEliminar = document.querySelector(`button.btn-delete[data-id="${areaId}"]`)?.closest('tr');
                                if (filaAEliminar) {
                                    filaAEliminar.remove();
                                }
                        
                                // 3. Mostrar alerta de éxito
                                showAlert(data.message || 'Área eliminada correctamente');
                        
                            } else {
                                this.disabled = false;
                                this.innerHTML = 'Eliminar';
                                console.error('Error en eliminación:', data.message || 'Error desconocido');
                            }
                        })
                        
                        .catch(error => {
                            // Restaurar el botón en caso de error
                            this.disabled = false;
                            this.innerHTML = 'Eliminar';
                            console.error('Error en eliminación:', error);
                        });
                    });
                }
            } catch (error) {
                console.error('Error en modal:', error);
                // No mostramos alerta, solo registramos en consola
            }
        });
        
        // Restaurar botón cuando se cierra el modal de eliminación sin confirmar
        deleteModal.addEventListener('hidden.bs.modal', function() {
            const confirmButton = document.getElementById('confirmarEliminar');
            if (confirmButton) {
                confirmButton.disabled = false;
                confirmButton.innerHTML = 'Eliminar';
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
                
                // Desactivar el botón para evitar múltiples envíos
                const submitButton = this.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Actualizando...';
                }
                
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
                            
                            // Show success message
                            const alerta = document.createElement('div');
                            alerta.className = 'alert alert-success alert-dismissible fade show';
                            alerta.innerHTML = `
                                Área actualizada correctamente
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            
                            // Insert the message at the beginning of the container
                            document.querySelector('.area-container').prepend(alerta);
                            
                            // Remove the message after 5 seconds (increased from 3)
                            setTimeout(() => alerta.remove(), 5000);
                        } else {
                            // If row not found, reload as fallback
                            window.location.reload();
                        }
                    } else {
                        // Restaurar el botón si hay errores
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'Actualizar';
                        }
                        
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
                    // Restaurar el botón en caso de error
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = 'Actualizar';
                    }
                });
            });
            
            // Clear errors when the modal is closed
            editModal.addEventListener('hidden.bs.modal', function() {
                formEditarArea.reset();
                editModal.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                editModal.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
                // Restaurar el botón al cerrar el modal
                const submitButton = formEditarArea.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Actualizar';
                }
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