document.addEventListener('DOMContentLoaded', function () {
    // Get DOM elements
    const studentTypeRadios = document.querySelectorAll('input[name="studentType"]');
    const studentTypeHidden = document.getElementById('studentTypeHidden');
    const ciSearchContainer = document.getElementById('ciSearchContainer');
    const userInfoSection = document.querySelector('.user-info');
    const userInfoInputs = userInfoSection.querySelectorAll('input, select');

    // Function to toggle form state
    function toggleFormState(isExistingStudent) {
        // Update hidden input value
        studentTypeHidden.value = isExistingStudent ? 'existing' : 'new';

        // Show/hide CI search
        ciSearchContainer.style.display = isExistingStudent ? 'block' : 'none';

        // For new students, we always enable inputs
        if (!isExistingStudent) {
            userInfoSection.style.opacity = '1';
            userInfoInputs.forEach(input => {
                input.disabled = false;
                input.readOnly = false;
            });
            // Clear form when switching to new student
            userInfoInputs.forEach(input => {
                input.value = '';
            });
        }
        // For existing students, we keep inputs enabled until a student is found
    }

    // Add event listeners to radio buttons
    studentTypeRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            const isExistingStudent = this.value === 'existing';
            toggleFormState(isExistingStudent);
        });
    });

    // Initialize form state (assuming "existing student" is checked by default)
    // But keep inputs enabled by default
    toggleFormState(true);
    userInfoSection.style.opacity = '1';
    userInfoInputs.forEach(input => {
        input.disabled = false;
        input.readOnly = false;
    });

    // Add new code for handling modalidad selection
    const modalidadSelects = document.querySelectorAll('.modalidad-select');
    const grupoContainers = document.querySelectorAll('.grupo-container');

    modalidadSelects.forEach((select, index) => {
        select.addEventListener('change', function () {
            const grupoContainer = grupoContainers[index];
            if (this.value === 'duo' || this.value === 'equipo') {
                grupoContainer.style.display = 'block';
            } else {
                grupoContainer.style.display = 'none';
            }
        });
    });

    // Add area functionality
    const areasContainer = document.getElementById('areasContainer');
    const addAreaBtn = document.getElementById('addAreaBtn');
    const MAX_AREAS = 2;

    function updateAreaVisibility() {
        const areas = areasContainer.querySelectorAll('.area-section');
        const removeButtons = areasContainer.querySelectorAll('.btn-remove-area');

        // Show/hide add button based on number of areas
        addAreaBtn.style.display = areas.length < MAX_AREAS ? 'block' : 'none';

        // Show/hide remove buttons
        removeButtons.forEach((btn, index) => {
            btn.style.display = areas.length > 1 ? 'block' : 'none';
        });

        // Update area titles
        areas.forEach((area, index) => {
            area.querySelector('h4').textContent = `Área de Participación ${index + 1}`;
        });
    }

    // Function to update available areas in selects
    function updateAvailableAreas() {
        const areaSelects = document.querySelectorAll('.area-select');
        const selectedAreas = Array.from(areaSelects).map(select => select.value);

        areaSelects.forEach((select, index) => {
            const currentValue = select.value;
            const options = select.querySelectorAll('option');

            options.forEach(option => {
                if (option.value === '') return; // Skip the default "Select" option
                const isSelected = selectedAreas.includes(option.value);
                option.disabled = isSelected && option.value !== currentValue;
            });
        });
    }

    // Add after existing area select event listeners
    // Add this function after setupModalidadGrupoListeners
    function updateGradoSelect() {
        const categoriaSelects = document.querySelectorAll('.categoria-select');
        const gradoSelect = document.getElementById('gradoSelect');
        const selectedCategorias = [];
        const areaCount = document.querySelectorAll('.area-section').length;

        // Get all selected categoria IDs
        categoriaSelects.forEach(select => {
            if (select.value) {
                selectedCategorias.push(select.value);
            }
        });

        // If no categories selected, clear grado select
        if (selectedCategorias.length === 0) {
            gradoSelect.innerHTML = '<option value="">Seleccione un grado</option>';
            return;
        }

        // If we have two areas but not all categories are selected, show message
        if (areaCount === 2 && selectedCategorias.length !== 2) {
            gradoSelect.innerHTML = '<option value="">Seleccione categorías en ambas áreas</option>';
            return;
        }

        // Fetch grades for selected categories
        fetch('/inscripcion/estudiante/grados', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                categorias: selectedCategorias
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    gradoSelect.innerHTML = '<option value="">Seleccione un grado</option>';
                    if (data.grados.length === 0) {
                        gradoSelect.innerHTML = '<option value="">No hay grados en común</option>';
                    } else {
                        data.grados.forEach(grado => {
                            const option = document.createElement('option');
                            option.value = grado.idGrado;
                            option.textContent = grado.grado;
                            gradoSelect.appendChild(option);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                gradoSelect.innerHTML = '<option value="">Error al cargar grados</option>';
            });
    }

    // Modify setupAreaCategoriaListeners to include grade update
    // Modify setupAreaCategoriaListeners function
    function setupAreaCategoriaListeners(areaSelect) {
        // Remove existing listeners before adding new ones
        const newAreaSelect = areaSelect.cloneNode(true);
        areaSelect.parentNode.replaceChild(newAreaSelect, areaSelect);
        
        const categoriaSelect = newAreaSelect.closest('.area-section').querySelector('.categoria-select');
        const newCategoriaSelect = categoriaSelect.cloneNode(true);
        categoriaSelect.parentNode.replaceChild(newCategoriaSelect, categoriaSelect);

        newAreaSelect.addEventListener('change', async function () {
            newCategoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';

            if (!this.value) return;

            try {
                const response = await fetch(`/inscripcion/estudiante/categorias/${this.value}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        selectedCategoria: null
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Create a Map to store unique categories by ID
                    const uniqueCategories = new Map();
                    
                    data.categorias.forEach(categoria => {
                        if (!uniqueCategories.has(categoria.idCategoria)) {
                            uniqueCategories.set(categoria.idCategoria, categoria);
                        }
                    });

                    // Add unique categories to select
                    uniqueCategories.forEach(categoria => {
                        const option = document.createElement('option');
                        option.value = categoria.idCategoria;
                        option.textContent = categoria.nombre;
                        newCategoriaSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });

        // Single event listener for categoria changes
        newCategoriaSelect.addEventListener('change', updateGradoSelect);
        
        return { newAreaSelect, newCategoriaSelect };
    }

    // Remove the nested DOMContentLoaded event listener
    // document.addEventListener('DOMContentLoaded', function () {
    //     const initialCategoriaSelect = document.querySelector('.categoria-select');
    //     initialCategoriaSelect.addEventListener('change', updateGradoSelect);
    // });

    // Add event listener for initial categoria select
    document.addEventListener('DOMContentLoaded', function () {
        const initialCategoriaSelect = document.querySelector('.categoria-select');
        initialCategoriaSelect.addEventListener('change', updateGradoSelect);
    });

    // Add this new function to update available groups
    function updateAvailableGroups() {
        const grupoSelects = document.querySelectorAll('.grupo-select');
        const selectedGroups = Array.from(grupoSelects).map(select => select.value);

        grupoSelects.forEach((select, index) => {
            const currentValue = select.value;
            const options = select.querySelectorAll('option');

            options.forEach(option => {
                if (option.value === '') return; // Skip the default "Select" option
                const isSelected = selectedGroups.includes(option.value);
                option.disabled = isSelected && option.value !== currentValue;
            });
        });
    }

    // Modify setupModalidadGrupoListeners function
    function setupModalidadGrupoListeners(modalidadSelect) {
        // Remover el listener existente antes de agregar uno nuevo
        const newModalidadSelect = modalidadSelect.cloneNode(true);
        modalidadSelect.parentNode.replaceChild(newModalidadSelect, modalidadSelect);

        newModalidadSelect.addEventListener('change', async function () {
            const grupoContainer = this.closest('.area-section').querySelector('.grupo-container');
            const grupoSelect = grupoContainer.querySelector('.grupo-select');

            // Remover listeners existentes del grupo select
            const newGrupoSelect = grupoSelect.cloneNode(true);
            grupoSelect.parentNode.replaceChild(newGrupoSelect, grupoSelect);

            if (this.value === 'duo' || this.value === 'equipo') {
                grupoContainer.style.display = 'block';
                newGrupoSelect.innerHTML = '<option value="">Seleccione un grupo</option>';

                try {
                    const response = await fetch(`/inscripcion/estudiante/grupos/${this.value}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Crear un objeto para rastrear grupos únicos
                        const gruposUnicos = {};
                        
                        // Filtrar grupos duplicados usando el ID como clave
                        data.grupos.forEach(grupo => {
                            if (!gruposUnicos[grupo.id]) {
                                gruposUnicos[grupo.id] = grupo;
                            }
                        });

                        // Convertir el objeto a array y ordenar
                        const gruposOrdenados = Object.values(gruposUnicos)
                            .sort((a, b) => a.nombreGrupo.localeCompare(b.nombreGrupo));

                        // Agregar las opciones ordenadas
                        gruposOrdenados.forEach(grupo => {
                            const option = document.createElement('option');
                            option.value = grupo.id;
                            option.textContent = grupo.nombreGrupo;
                            newGrupoSelect.appendChild(option);
                        });

                        // Agregar un único event listener
                        newGrupoSelect.addEventListener('change', updateAvailableGroups, { once: true });
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            } else {
                grupoContainer.style.display = 'none';
                newGrupoSelect.value = '';
            }
        });

        return newModalidadSelect;
    }

    // Modify createNewArea function to handle the new modalidad select
    function createNewArea() {
        const areaTemplate = areasContainer.querySelector('.area-section').cloneNode(true);
        const newIndex = areasContainer.querySelectorAll('.area-section').length;

        // Update names for new elements
        areaTemplate.querySelectorAll('[name^="areas[0]"]').forEach(element => {
            element.name = element.name.replace('areas[0]', `areas[${newIndex}]`);
            if (element.tagName === 'SELECT') {
                element.selectedIndex = 0;
            }
        });

        // Clear any selected values
        areaTemplate.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });

        // Hide grupo container by default
        areaTemplate.querySelector('.grupo-container').style.display = 'none';

        // Add the new area
        areasContainer.appendChild(areaTemplate);
        updateAreaVisibility();

        // Add change event listeners to new selects
        const newAreaSelect = areaTemplate.querySelector('.area-select');
        const newModalidadSelect = areaTemplate.querySelector('.modalidad-select');

        setupAreaCategoriaListeners(newAreaSelect);
        setupModalidadGrupoListeners(newModalidadSelect);

        newAreaSelect.addEventListener('change', function () {
            updateAvailableAreas();
        });

        updateAvailableAreas();
    }

    // Initialize event listeners for the first area
    const initialAreaSelect = areasContainer.querySelector('.area-select');
    const initialModalidadSelect = areasContainer.querySelector('.modalidad-select');

    // Setup the event listeners for the initial selects
    setupAreaCategoriaListeners(initialAreaSelect);
    setupModalidadGrupoListeners(initialModalidadSelect);

    // Add change event listener to initial area select for updating available areas
    initialAreaSelect.addEventListener('change', function () {
        updateAvailableAreas();
    });

    // Modify remove area functionality
    areasContainer.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-area')) {
            e.preventDefault();
            const areaToRemove = e.target.closest('.area-section');
            areaToRemove.remove();
            updateAreaVisibility();
            updateAvailableAreas();
        }
    });

    // Add area button click handler
    addAreaBtn.addEventListener('click', function () {
        const currentAreas = areasContainer.querySelectorAll('.area-section');
        if (currentAreas.length < MAX_AREAS) {
            createNewArea();
        }
    });

    // Remove area functionality
    areasContainer.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-area')) {
            e.preventDefault();
            const areaToRemove = e.target.closest('.area-section');
            areaToRemove.remove();
            updateAreaVisibility();
        }
    });

    // Initialize area visibility
    updateAreaVisibility();

    // Search functionality
    const searchButton = document.getElementById('searchButton');
    const searchInput = document.getElementById('searchCI');
    const searchResult = document.getElementById('searchResult');
    let foundStudentCI = ''; // Variable to store the found student's CI

    // Add a clear button to the search container only if it doesn't already exist
    if (!document.getElementById('clearButton')) {
        const clearButtonHTML = `
            <button type="button" id="clearButton" class="clear-button">
                <i class="fas fa-times"></i> Limpiar
            </button>
        `;
        searchInput.insertAdjacentHTML('afterend', clearButtonHTML);
    }
    const clearButton = document.getElementById('clearButton');
    
    // Initially hide the clear button
    clearButton.style.display = 'none';

    // Clear button functionality
    clearButton.addEventListener('click', function() {
        // Clear search input
        searchInput.value = '';
        searchResult.innerHTML = '';
        
        // Clear and enable user info inputs
        userInfoInputs.forEach(input => {
            input.value = '';
            input.disabled = false;
            input.readOnly = false;
        });
        
        userInfoSection.style.opacity = '1';
        clearButton.style.display = 'none';
    });

    searchButton.addEventListener('click', async function () {
        const ci = searchInput.value.trim();
        if (!ci) {
            searchResult.innerHTML = '<div class="alert alert-warning">Por favor ingrese un CI</div>';
            return;
        }

        try {
            const response = await fetch(`/inscripcion/estudiante/buscar?ci=${ci}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (!data.success) {
                searchResult.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                return;
            }

            // Fill form with student data
            const estudiante = data.estudiante;
            document.querySelector('input[name="nombres"]').value = estudiante.nombres;
            document.querySelector('input[name="apellidoPaterno"]').value = estudiante.apellidoPaterno;
            document.querySelector('input[name="apellidoMaterno"]').value = estudiante.apellidoMaterno;
            document.querySelector('input[name="ci"]').value = estudiante.ci;
            foundStudentCI = estudiante.ci; // Store the CI for form submission
            document.querySelector('input[name="fechaNacimiento"]').value = estudiante.fechaNacimiento;
            document.querySelector('select[name="genero"]').value = estudiante.genero;
            document.querySelector('input[name="email"]').value = estudiante.email;

            // Disable user info inputs after finding a student
            userInfoSection.style.opacity = '0.7';
            userInfoInputs.forEach(input => {
                input.disabled = true;
                input.readOnly = true;
            });

            // Show clear button after finding a student
            clearButton.style.display = 'block';

            searchResult.innerHTML = '<div class="alert alert-success">Estudiante encontrado</div>';

        } catch (error) {
            searchResult.innerHTML = '<div class="alert alert-danger">Error al buscar estudiante</div>';
            console.error('Error:', error);
        }
    });

    // Add form submission handler
    // Replace the form submission handler with this updated version
    const form = document.querySelector('.registration-form');
    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        // Disable submit button to prevent double submission
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
        }

        try {
            // Get the student type (existing or new)
            const studentType = studentTypeHidden.value;
            
            // Common form data for both student types
            const formData = {
                idConvocatoria: document.getElementById('idConvocatoria').value,
                idDelegacion: document.getElementById('idDelegacion').value,
                grado: document.getElementById('gradoSelect').value,
                numeroContacto: document.querySelector('input[name="numeroContacto"]').value,
                nombreCompletoTutor: document.querySelector('input[name="nombreCompletoTutor"]').value,
                correoTutor: document.querySelector('input[name="correoTutor"]').value,
                areas: []
            };

            // Get areas data
            document.querySelectorAll('.area-section').forEach((areaSection) => {
                const areaData = {
                    area: areaSection.querySelector('.area-select').value,
                    categoria: areaSection.querySelector('.categoria-select').value,
                    modalidad: areaSection.querySelector('.modalidad-select').value,
                };

                // Add grupo if modalidad is duo or equipo
                if (areaData.modalidad !== 'individual') {
                    areaData.grupo = areaSection.querySelector('.grupo-select').value;
                }

                formData.areas.push(areaData);
            });

            // Add student-specific data based on student type
            if (studentType === 'existing') {
                // For existing student, just add the CI
                formData.ci = document.querySelector('input[name="ci"]').value;
                var endpoint = '/inscripcion/estudiante/manual/store';
            } else {
                // For new student, add all user information
                formData.nombres = document.querySelector('input[name="nombres"]').value;
                formData.apellidoPaterno = document.querySelector('input[name="apellidoPaterno"]').value;
                formData.apellidoMaterno = document.querySelector('input[name="apellidoMaterno"]').value;
                formData.ci = document.querySelector('input[name="ci"]').value;
                formData.fechaNacimiento = document.querySelector('input[name="fechaNacimiento"]').value;
                formData.genero = document.querySelector('select[name="genero"]').value;
                formData.email = document.querySelector('input[name="email"]').value;
                var endpoint = '/inscripcion/estudiante/manual/store-new';
            }

            console.log('Sending form data:', formData);
            console.log('To endpoint:', endpoint);

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            console.log('Server response:', data);

            if (data.success) {
                window.location.href = data.redirect;
            } else {
                if (data.errors) {
                    // Si hay errores específicos de validación, mostrarlos
                    const errorMessages = Object.values(data.errors).flat().join('\n');
                    displayError(errorMessages);
                } else {
                    displayError(data.message || 'Error al procesar la inscripción');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            displayError('Error al enviar el formulario');
        } finally {
            // Re-enable submit button
            if (submitButton) {
                submitButton.disabled = false;
            }
        }
    });

    // Replace showError with displayError function
    function displayError(message) {
        const errorDisplay = document.getElementById('errorDisplay');
        if (errorDisplay) {
            errorDisplay.textContent = message;
            errorDisplay.style.display = 'block';
        } else {
            // Fallback if errorDisplay doesn't exist
            alert(message);
        }
        console.error('Form Error:', message);
    }

    // Update validateForm to use displayError
    function validateForm() {
        const studentType = document.querySelector('input[name="studentType"]:checked').value;
        const areas = document.querySelectorAll('.area-section');
        const gradoSelect = document.getElementById('gradoSelect');

        if (studentType === 'existing' && !document.querySelector('input[name="ci"]').value) {
            displayError('Por favor, ingrese el CI del estudiante');
            return false;
        }

        // Rest of validation code...
        for (let area of areas) {
            const areaSelect = area.querySelector('.area-select');
            const categoriaSelect = area.querySelector('.categoria-select');
            const modalidadSelect = area.querySelector('.modalidad-select');
            const grupoSelect = area.querySelector('.grupo-select');

            if (!areaSelect.value || !categoriaSelect.value || !modalidadSelect.value) {
                showError('Por favor, complete todos los campos de área');
                return false;
            }

            // Validate grupo if modalidad is duo or equipo
            if (['duo', 'equipo'].includes(modalidadSelect.value)) {
                if (!grupoSelect.value) {
                    showError('Por favor, seleccione un grupo para la modalidad ' + modalidadSelect.value);
                    return false;
                }
            }
        }

        // Validate grado
        if (!gradoSelect.value) {
            showError('Por favor, seleccione un grado');
            return false;
        }

        // Validaciones para nombres y apellidos
        const nameFields = ['nombres', 'apellidoPaterno', 'apellidoMaterno'];
        const nameRegex = /^[A-Za-záéíóúÁÉÍÓÚñÑ\s]{3,}$/;
        
        for (const field of nameFields) {
            const input = document.querySelector(`input[name="${field}"]`);
            if (!nameRegex.test(input.value)) {
                displayError(`El campo ${field} solo debe contener letras y tener al menos 3 caracteres`);
                input.focus();
                return false;
            }
        }

        // Validación de CI
        const ciInput = document.querySelector('input[name="ci"]');
        if (!/^\d{7}$/.test(ciInput.value)) {
            displayError('El CI debe tener exactamente 7 dígitos');
            ciInput.focus();
            return false;
        }

        // Validación de fecha de nacimiento
        const fechaNacimiento = document.querySelector('input[name="fechaNacimiento"]');
        const fechaMinima = new Date();
        fechaMinima.setFullYear(fechaMinima.getFullYear() - 5);
        
        if (new Date(fechaNacimiento.value) > fechaMinima) {
            displayError('La edad mínima requerida es 5 años');
            fechaNacimiento.focus();
            return false;
        }

        // Validación de emails
        const emailFields = ['email', 'correoTutor'];
        const emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
        
        for (const field of emailFields) {
            const input = document.querySelector(`input[name="${field}"]`);
            if (!emailRegex.test(input.value)) {
                displayError(`El ${field} debe ser una dirección de Gmail válida`);
                input.focus();
                return false;
            }
        }

        // Validación del nombre del tutor
        const nombreTutorInput = document.querySelector('input[name="nombreCompletoTutor"]');
        if (!nameRegex.test(nombreTutorInput.value)) {
            displayError('El nombre del tutor solo debe contener letras y tener al menos 3 caracteres');
            nombreTutorInput.focus();
            return false;
        }

        // Validación del número de contacto
        const numeroContactoInput = document.querySelector('input[name="numeroContacto"]');
        if (!/^\d{8}$/.test(numeroContactoInput.value)) {
            displayError('El número de contacto debe tener exactamente 8 dígitos');
            numeroContactoInput.focus();
            return false;
        }

        return true;
    }

    // Función para validar solo letras y espacios
    function soloLetras(event) {
        const charCode = event.which || event.keyCode;
        const char = String.fromCharCode(charCode);
        // Permitir letras, espacios, ñ, Ñ y vocales con acentos
        if (!/^[A-Za-záéíóúÁÉÍÓÚñÑ\s]$/.test(char)) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // Función para validar solo números con límite
    function soloNumeros(event, maxLength) {
        const input = event.target;
        const charCode = event.which || event.keyCode;
        
        // Permitir teclas de control (backspace, delete, flechas, etc.)
        if (event.type === 'keydown') {
            if (charCode === 8 || charCode === 9 || charCode === 37 || charCode === 39 || charCode === 46) {
                return true;
            }
        }

        // Validar que sea número y no exceda el máximo
        if (event.type === 'keypress') {
            const char = String.fromCharCode(charCode);
            if (!/^\d$/.test(char) || input.value.length >= maxLength) {
                event.preventDefault();
                return false;
            }
        }

        return true;
    }

    // Validación en tiempo real para campos de texto (nombres y apellidos)
    const camposTexto = [
        'input[name="nombres"]',
        'input[name="apellidoPaterno"]',
        'input[name="apellidoMaterno"]',
        'input[name="nombreCompletoTutor"]'
    ];

    camposTexto.forEach(selector => {
        const input = document.querySelector(selector);
        if (input) {
            input.addEventListener('keypress', soloLetras);
            input.addEventListener('input', function() {
                // Remover cualquier número que se haya pegado
                this.value = this.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ\s]/g, '');
                
                // Validar longitud mínima
                if (this.value.length < 3) {
                    this.classList.add('invalid');
                    mostrarError(this, 'Mínimo 3 caracteres');
                } else {
                    this.classList.remove('invalid');
                    ocultarError(this);
                }
            });
        }
    });

    // Validación en tiempo real para CI
    const inputCI = document.querySelector('input[name="ci"]');
    if (inputCI) {
        inputCI.addEventListener('keydown', e => soloNumeros(e, 7));
        inputCI.addEventListener('keypress', e => soloNumeros(e, 7));
        inputCI.addEventListener('paste', function(e) {
            e.preventDefault();
            const texto = (e.clipboardData || window.clipboardData).getData('text');
            const numerosFiltrados = texto.replace(/\D/g, '').slice(0, 7);
            this.value = numerosFiltrados;
        });
    }

    // Validación en tiempo real para número de contacto
    const inputContacto = document.querySelector('input[name="numeroContacto"]');
    if (inputContacto) {
        inputContacto.addEventListener('keydown', e => soloNumeros(e, 8));
        inputContacto.addEventListener('keypress', e => soloNumeros(e, 8));
        inputContacto.addEventListener('paste', function(e) {
            e.preventDefault();
            const texto = (e.clipboardData || window.clipboardData).getData('text');
            const numerosFiltrados = texto.replace(/\D/g, '').slice(0, 8);
            this.value = numerosFiltrados;
        });
    }

    // Validación en tiempo real para emails
    const camposEmail = ['input[name="email"]', 'input[name="correoTutor"]'];
    camposEmail.forEach(selector => {
        const input = document.querySelector(selector);
        if (input) {
            input.addEventListener('input', function() {
                const emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
                if (!emailRegex.test(this.value)) {
                    this.classList.add('invalid');
                    mostrarError(this, 'Debe ser un correo Gmail válido');
                } else {
                    this.classList.remove('invalid');
                    ocultarError(this);
                }
            });
        }
    });

    // Función para mostrar error debajo del campo
    function mostrarError(input, mensaje) {
        let errorDiv = input.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('error-message')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            input.parentNode.insertBefore(errorDiv, input.nextSibling);
        }
        errorDiv.textContent = mensaje;
        errorDiv.style.display = 'block';
    }

    // Función para ocultar error
    function ocultarError(input) {
        const errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('error-message')) {
            errorDiv.style.display = 'none';
        }
    }

    // Agregar estilos para los mensajes de error y campos inválidos
    const style = document.createElement('style');
    style.textContent = `
        .error-message {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: none;
        }
        .invalid {
            border-color: #dc3545 !important;
            background-color: #fff5f5 !important;
        }
        .invalid:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
    `;
    document.head.appendChild(style);

    // Agregar validación en tiempo real para los campos
    document.addEventListener('DOMContentLoaded', function() {
        // Validación en tiempo real para nombres y apellidos
        const nameInputs = document.querySelectorAll('input[pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\\s]+"]');
        nameInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^A-Za-záéíóúÁÉÍÓÚñÑ\s]/g, '');
            });
        });

        // Validación en tiempo real para CI y número de contacto
        const numberInputs = document.querySelectorAll('input[pattern="[0-9]{7,8}"]');
        numberInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, this.pattern.match(/\d+/)[0]);
            });
        });

        // Validación de fecha de nacimiento en tiempo real
        const fechaNacimientoInput = document.querySelector('input[name="fechaNacimiento"]');
        fechaNacimientoInput.addEventListener('change', function() {
            const fechaMinima = new Date();
            fechaMinima.setFullYear(fechaMinima.getFullYear() - 5);
            
            if (new Date(this.value) > fechaMinima) {
                displayError('La edad mínima requerida es 5 años');
                this.value = '';
            }
        });
    });

    // Modify the event listener of the form to include the new validation
    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        // Validate the form before sending
        if (!validateForm()) {
            return;
        }

        // ... rest of the existing code for submitting the form ...
    });
});

// Function to display errors
function showError(message) {
    const errorDisplay = document.getElementById('errorDisplay');
    errorDisplay.textContent = message;
    errorDisplay.style.display = 'block';
    console.error('Form Error:', message);
}

// Add this at the beginning of your DOMContentLoaded event
async function cargarConvocatoriaActiva() {
    try {
        const response = await fetch('/inscripcion/estudiante/convocatoria-activa', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Respuesta convocatoria:', data);

        const convocatoriaInput = document.getElementById('convocatoriaInput');
        const idConvocatoriaInput = document.getElementById('idConvocatoria');

        if (data.success && data.convocatoria) {
            convocatoriaInput.value = data.convocatoria.nombre;
            idConvocatoriaInput.value = data.convocatoria.id;
        } else {
            convocatoriaInput.value = 'No hay convocatoria activa';
            idConvocatoriaInput.value = '';
            displayError('No hay una convocatoria activa disponible');
        }
    } catch (error) {
        console.error('Error al cargar convocatoria:', error);
        document.getElementById('convocatoriaInput').value = 'Error al cargar convocatoria';
        displayError('Error al cargar la convocatoria');
    }
}

// Call the function when the page loads
cargarConvocatoriaActiva();

async function cargarColegio() {
    try {
        const response = await fetch('/inscripcion/estudiante/colegio', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        const colegioInput = document.getElementById('colegioInput');
        const idDelegacionInput = document.getElementById('idDelegacion');

        if (data.success) {
            colegioInput.value = data.colegio.nombre;
            idDelegacionInput.value = data.colegio.id;
        } else {
            colegioInput.value = 'No asignado';
            idDelegacionInput.value = '';
            displayError('No hay colegio asignado');
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('colegioInput').value = 'Error al cargar colegio';
        displayError('Error al cargar el colegio');
    }
}

// Call both functions when the page loads
document.addEventListener('DOMContentLoaded', function() {
    cargarConvocatoriaActiva();
    cargarColegio();
});