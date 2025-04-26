// Example starter JavaScript for disabling form submissions if there are invalid fields
(() => {
    'use strict'
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')
    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
        }
        form.classList.add('was-validated')
        }, false)
    })
})()

//Modal de creacion
document.addEventListener('DOMContentLoaded', function() {
    // Store all existing area names in an array when the page loads
    const existingAreaNames = [];
    
    // Populate the existingAreaNames array from the table data
    document.querySelectorAll('.areas-table tbody tr').forEach(row => {
        const areaNameCell = row.querySelector('td:first-child');
        if (areaNameCell) {
            existingAreaNames.push(areaNameCell.textContent.trim().toLowerCase());
        }
    });

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
        form.querySelectorAll('.is-invalid, .is-valid').forEach(el => el.classList.remove('is-invalid', 'is-valid'));
        
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = submitButton.dataset.originalText || 'Submit';
        }
    }

    // VALIDATION METHODS
    
    /**
     * Validates if area name exists in database
     * @param {string} name - The area name to check
     * @returns {boolean} True if name doesn't exist (valid), false if exists (invalid)
     */
    function isAreaNameUnique(name) {
        // Convert to lowercase for case-insensitive comparison
        const normalizedName = name.trim().toLowerCase();
        
        // Check if the name exists in our array of current area names
        return !existingAreaNames.includes(normalizedName);
    }
    
    /**
     * Checks if area name is similar to any existing name
     * @param {string} name - The area name to check
     * @param {number} threshold - Similarity threshold (lower is more strict)
     * @returns {boolean} True if no similar names found, false otherwise
     */
    function isAreaNameNotSimilar(name, threshold = 0.8) {
        const normalizedName = name.trim().toLowerCase();
        
        // Check for similarity with each existing name
        for (const existingName of existingAreaNames) {
            // Skip exact check as it's handled by isAreaNameUnique
            if (existingName === normalizedName) continue;
            
            // Calculate similarity
            const similarityScore = calculateSimilarity(normalizedName, existingName);
            
            // If similarity is above threshold, it's too similar
            if (similarityScore > threshold) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Calculates similarity between two strings
     * @param {string} str1 - First string to compare
     * @param {string} str2 - Second string to compare
     * @returns {number} Similarity score between 0 (no similarity) and 1 (identical)
     */
    function calculateSimilarity(str1, str2) {
        // Simple implementation of Levenshtein distance algorithm
        const levenshteinDistance = (a, b) => {
            if (a.length === 0) return b.length;
            if (b.length === 0) return a.length;
            
            const matrix = [];
            
            // Initialize matrix
            for (let i = 0; i <= b.length; i++) {
                matrix[i] = [i];
            }
            
            for (let j = 0; j <= a.length; j++) {
                matrix[0][j] = j;
            }
            
            // Fill matrix
            for (let i = 1; i <= b.length; i++) {
                for (let j = 1; j <= a.length; j++) {
                    const cost = a[j - 1] === b[i - 1] ? 0 : 1;
                    matrix[i][j] = Math.min(
                        matrix[i - 1][j] + 1,      // deletion
                        matrix[i][j - 1] + 1,      // insertion
                        matrix[i - 1][j - 1] + cost // substitution
                    );
                }
            }
            
            return matrix[b.length][a.length];
        };
        
        // Calculate distance
        const distance = levenshteinDistance(str1, str2);
        
        // Calculate max possible distance
        const maxLength = Math.max(str1.length, str2.length);
        
        // Calculate similarity as 1 - normalized distance
        return maxLength === 0 ? 1 : 1 - distance / maxLength;
    }
    
    /**
     * Validates if an area name meets all requirements
     * @param {string} name - The area name to validate
     * @returns {boolean} True if name is valid, false otherwise
     */
    function isValidAreaName(name) {
        // Check pattern (letters and spaces only)
        const pattern = /^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/;
        const hasValidChars = pattern.test(name);
        
        // Check length (5-20 characters)
        const hasValidLength = name.length >= 5 && name.length <= 20;
        
        // Check uniqueness
        const isUnique = isAreaNameUnique(name);
        
        // Check similarity
        const isNotSimilar = isAreaNameNotSimilar(name);
        
        // All checks must pass
        return hasValidChars && hasValidLength && isUnique && isNotSimilar;
    }
    
    /**
     * Validates area name and updates UI accordingly
     * @param {HTMLInputElement} inputElement - The input element to validate
     */
    function validateAreaName(inputElement) {
        const name = inputElement.value.trim();
        const submitButton = inputElement.form.querySelector('button[type="submit"]');
        
        // Remove existing feedback elements
        const existingFeedback = inputElement.parentNode.querySelector('.invalid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        // Reset classes
        inputElement.classList.remove('is-valid', 'is-invalid');
        
        // Skip validation if name is empty
        if (!name) return;
        
        // Check pattern
        const pattern = /^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/;
        if (!pattern.test(name)) {
            inputElement.classList.add('is-invalid');
            addErrorFeedback(inputElement, 'Solo se permiten letras y espacios');
            if (submitButton) submitButton.disabled = true;
            return;
        }
        
        // Check length
        if (name.length < 5 || name.length > 20) {
            inputElement.classList.add('is-invalid');
            addErrorFeedback(inputElement, 'El nombre debe tener entre 5 y 20 caracteres');
            if (submitButton) submitButton.disabled = true;
            return;
        }
        
        // Check uniqueness
        if (!isAreaNameUnique(name)) {
            inputElement.classList.add('is-invalid');
            addErrorFeedback(inputElement, 'Este nombre de área ya existe');
            if (submitButton) submitButton.disabled = true;
            return;
        }
        
        // Check similarity
        if (!isAreaNameNotSimilar(name)) {
            inputElement.classList.add('is-invalid');
            addErrorFeedback(inputElement, 'Este nombre es muy similar a un área existente');
            if (submitButton) submitButton.disabled = true;
            return;
        }
        
        // All checks passed
        inputElement.classList.add('is-valid');
        if (submitButton) submitButton.disabled = false;
    }
    
    /**
     * Adds error feedback message after an input
     * @param {HTMLElement} inputElement - The input element to add feedback to
     * @param {string} message - The error message to display
     */
    function addErrorFeedback(inputElement, message) {
        const divError = document.createElement('div');
        divError.classList.add('invalid-feedback');
        divError.textContent = message;
        inputElement.parentNode.appendChild(divError);
    }

    // Formulario/Modal para crear nueva área
    const formNuevaArea = document.getElementById('formNuevaArea');
    if (formNuevaArea) {
        const submitButton = formNuevaArea.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.dataset.originalText = submitButton.innerHTML;
        }
        
        // Add input event listener for real-time validation
        const nombreInput = formNuevaArea.querySelector('input[name="nombre"]');
        if (nombreInput) {
            nombreInput.addEventListener('input', function() {
                validateAreaName(this);
            });
        }

        formNuevaArea.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get area name and validate before submission
            const nombre = formNuevaArea.querySelector('input[name="nombre"]').value.trim();
            if (!isValidAreaName(nombre)) {
                // Validation already displays errors via validateAreaName
                return false;
            }
            
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
                    
                    // Add the new area name to our existingAreaNames array
                    existingAreaNames.push(nombre.toLowerCase());
                    
                    // Agregar la nueva fila a la tabla
                    const tablaAreas = document.querySelector('table tbody');
                    if (tablaAreas) {
                        // Check if the "no areas" message exists and remove it
                        const noAreasRow = tablaAreas.querySelector('tr td[colspan="2"]');
                        if (noAreasRow && noAreasRow.textContent.includes('No hay áreas registradas')) {
                            noAreasRow.closest('tr').remove();
                        }
                        
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
                        </td>`;
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
});

//Modal de eliminación
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

});

//modal de edicion
document.addEventListener('DOMContentLoaded', function() {
    // Store all existing area names in an array when the page loads
    const existingAreaNames = [];
    
    // Populate the existingAreaNames array from the table data
    document.querySelectorAll('.areas-table tbody tr').forEach(row => {
        const areaNameCell = row.querySelector('td:first-child');
        if (areaNameCell) {
            existingAreaNames.push(areaNameCell.textContent.trim().toLowerCase());
        }
    });
    
    // Edit Area Modal enhanced with validation
    const editModal = document.getElementById('EditarAreaModal');
    if (editModal) {
        let originalAreaName = ''; // Store the original name before editing
        
        // Populate the edit modal with area data when it's shown
        editModal.addEventListener('show.bs.modal', function(event) {
            try {
                const button = event.relatedTarget;
                if (!button) return;
                
                const idArea = button.getAttribute('data-id');
                const nombreArea = button.getAttribute('data-nombre');
                originalAreaName = nombreArea.trim().toLowerCase(); // Store the original name
                
                if (!idArea || !nombreArea) return;
                
                // Set the area name in the form input
                const nombreInput = editModal.querySelector('input[name="nombre"]');
                if (nombreInput) {
                    nombreInput.value = nombreArea;
                    
                    // Add input event listener to validate as user types
                    nombreInput.addEventListener('input', function() {
                        validateAreaName(this, originalAreaName);
                    });
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
        
        // Handle edit form submission with validation
        const formEditarArea = document.getElementById('formEditarArea');
        if (formEditarArea) {
            formEditarArea.addEventListener('submit', function(e) {
                const nombreInput = this.querySelector('input[name="nombre"]');
                const newAreaName = nombreInput.value.trim();
                
                // Validate before submission
                if (!isValidAreaName(newAreaName, originalAreaName)) {
                    e.preventDefault(); // Stop form submission
                    return false;
                }
                
                e.preventDefault(); // Still prevent default for AJAX handling
                
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
                                
                                // Update existingAreaNames array with the new name
                                const index = existingAreaNames.indexOf(originalAreaName);
                                if (index !== -1) {
                                    existingAreaNames[index] = formData.get('nombre').trim().toLowerCase();
                                }
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
                            submitButton.innerHTML = 'Guardar Cambios';
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
                        submitButton.innerHTML = 'Guardar Cambios';
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
                    submitButton.innerHTML = 'Guardar Cambios';
                }
                
                // Remove validation classes
                const nombreInput = formEditarArea.querySelector('input[name="nombre"]');
                if (nombreInput) {
                    nombreInput.classList.remove('is-valid', 'is-invalid');
                }
            });
        }
    }
    
    // VALIDATION METHODS
    
    /**
     * Validates if area name exists in database
     * @param {string} name - The area name to check
     * @param {string} originalName - The original name (to ignore when checking duplicates)
     * @returns {boolean} True if name doesn't exist (valid), false if exists (invalid)
     */
    function isAreaNameUnique(name, originalName) {
        // Convert to lowercase for case-insensitive comparison
        const normalizedName = name.trim().toLowerCase();
        
        // If name hasn't changed, it's valid
        if (normalizedName === originalName) {
            return true;
        }
        
        // Check if the name exists in our array of current area names
        return !existingAreaNames.includes(normalizedName);
    }
    
    /**
     * Checks if area name is similar to any existing name
     * @param {string} name - The area name to check
     * @param {string} originalName - The original name (to ignore when checking similarities)
     * @param {number} threshold - Similarity threshold (lower is more strict)
     * @returns {boolean} True if no similar names found, false otherwise
     */
    function isAreaNameNotSimilar(name, originalName, threshold = 0.8) {
        const normalizedName = name.trim().toLowerCase();
        
        // If name hasn't changed, it's valid
        if (normalizedName === originalName) {
            return true;
        }
        
        // Check for similarity with each existing name
        for (const existingName of existingAreaNames) {
            // Skip comparing with original name
            if (existingName === originalName) continue;
            
            // Skip exact check as it's handled by isAreaNameUnique
            if (existingName === normalizedName) continue;
            
            // Calculate similarity
            const similarityScore = calculateSimilarity(normalizedName, existingName);
            
            // If similarity is above threshold, it's too similar
            if (similarityScore > threshold) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Calculates similarity between two strings
     * @param {string} str1 - First string to compare
     * @param {string} str2 - Second string to compare
     * @returns {number} Similarity score between 0 (no similarity) and 1 (identical)
     */
    function calculateSimilarity(str1, str2) {
        // Simple implementation of Levenshtein distance algorithm
        const levenshteinDistance = (a, b) => {
            if (a.length === 0) return b.length;
            if (b.length === 0) return a.length;
            
            const matrix = [];
            
            // Initialize matrix
            for (let i = 0; i <= b.length; i++) {
                matrix[i] = [i];
            }
            
            for (let j = 0; j <= a.length; j++) {
                matrix[0][j] = j;
            }
            
            // Fill matrix
            for (let i = 1; i <= b.length; i++) {
                for (let j = 1; j <= a.length; j++) {
                    const cost = a[j - 1] === b[i - 1] ? 0 : 1;
                    matrix[i][j] = Math.min(
                        matrix[i - 1][j] + 1,      // deletion
                        matrix[i][j - 1] + 1,      // insertion
                        matrix[i - 1][j - 1] + cost // substitution
                    );
                }
            }
            
            return matrix[b.length][a.length];
        };
        
        // Calculate distance
        const distance = levenshteinDistance(str1, str2);
        
        // Calculate max possible distance
        const maxLength = Math.max(str1.length, str2.length);
        
        // Calculate similarity as 1 - normalized distance
        return maxLength === 0 ? 1 : 1 - distance / maxLength;
    }
    
    /**
     * Validates if an area name meets all requirements
     * @param {string} name - The area name to validate
     * @param {string} originalName - The original name before editing
     * @returns {boolean} True if name is valid, false otherwise
     */
    function isValidAreaName(name, originalName) {
        // Check pattern (letters and spaces only)
        const pattern = /^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/;
        const hasValidChars = pattern.test(name);
        
        // Check length (5-20 characters)
        const hasValidLength = name.length >= 5 && name.length <= 20;
        
        // Check uniqueness
        const isUnique = isAreaNameUnique(name, originalName);
        
        // Check similarity
        const isNotSimilar = isAreaNameNotSimilar(name, originalName);
        
        // All checks must pass
        return hasValidChars && hasValidLength && isUnique && isNotSimilar;
    }
    
    /**
     * Validates area name and updates UI accordingly
     * @param {HTMLInputElement} inputElement - The input element to validate
     * @param {string} originalName - Original area name before editing
     */
    function validateAreaName(inputElement, originalName) {
        const name = inputElement.value.trim();
        const submitButton = document.querySelector('#formEditarArea button[type="submit"]');
        
        // Remove existing feedback elements
        const existingFeedback = inputElement.parentNode.querySelector('.invalid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        // Reset classes
        inputElement.classList.remove('is-valid', 'is-invalid');
        
        // Skip validation if name is empty
        if (!name) return;
        
        // Check pattern
        const pattern = /^[A-Za-záéíóúÁÉÍÓÚñÑ\s]+$/;
        if (!pattern.test(name)) {
            inputElement.classList.add('is-invalid');
            addErrorFeedback(inputElement, 'Solo se permiten letras y espacios');
            if (submitButton) submitButton.disabled = true;
            return;
        }
        
        // Check length
        if (name.length < 5 || name.length > 20) {
            inputElement.classList.add('is-invalid');
            addErrorFeedback(inputElement, 'El nombre debe tener entre 5 y 20 caracteres');
            if (submitButton) submitButton.disabled = true;
            return;
        }
        
        // Check uniqueness
        if (!isAreaNameUnique(name, originalName)) {
            inputElement.classList.add('is-invalid');
            addErrorFeedback(inputElement, 'Este nombre de área ya existe');
            if (submitButton) submitButton.disabled = true;
            return;
        }
        
        // Check similarity
        if (!isAreaNameNotSimilar(name, originalName)) {
            inputElement.classList.add('is-invalid');
            addErrorFeedback(inputElement, 'Este nombre es muy similar a un área existente');
            if (submitButton) submitButton.disabled = true;
            return;
        }
        
        // All checks passed
        inputElement.classList.add('is-valid');
        if (submitButton) submitButton.disabled = false;
    }
    
    /**
     * Adds error feedback message after an input
     * @param {HTMLElement} inputElement - The input element to add feedback to
     * @param {string} message - The error message to display
     */
    function addErrorFeedback(inputElement, message) {
        const divError = document.createElement('div');
        divError.classList.add('invalid-feedback');
        divError.textContent = message;
        inputElement.parentNode.appendChild(divError);
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