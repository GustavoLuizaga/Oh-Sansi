document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const studentTypeRadios = document.querySelectorAll('input[name="studentType"]');
    const ciSearchContainer = document.getElementById('ciSearchContainer');
    const userInfoSection = document.querySelector('.user-info');
    const userInfoInputs = userInfoSection.querySelectorAll('input, select');
    
    // Function to toggle form state
    function toggleFormState(isExistingStudent) {
        // Show/hide CI search
        ciSearchContainer.style.display = isExistingStudent ? 'block' : 'none';
        
        // Toggle user info section state
        userInfoSection.style.opacity = isExistingStudent ? '0.7' : '1';
        
        // Enable/disable user info inputs
        userInfoInputs.forEach(input => {
            input.disabled = isExistingStudent;
            input.readOnly = isExistingStudent;
        });
        
        // Clear form if switching to new student
        if (!isExistingStudent) {
            userInfoInputs.forEach(input => {
                input.value = '';
            });
        }
    }
    
    // Add event listeners to radio buttons
    studentTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const isExistingStudent = this.value === 'existing';
            toggleFormState(isExistingStudent);
        });
    });
    
    // Initialize form state (assuming "existing student" is checked by default)
    toggleFormState(true);
    
    // Add new code for handling modalidad selection
    const modalidadSelects = document.querySelectorAll('.modalidad-select');
    const grupoContainers = document.querySelectorAll('.grupo-container');
    
    modalidadSelects.forEach((select, index) => {
        select.addEventListener('change', function() {
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
    function setupAreaCategoriaListeners(areaSelect) {
        areaSelect.addEventListener('change', async function() {
            const categoriaSelect = this.closest('.area-section').querySelector('.categoria-select');
            categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
            
            if (!this.value) return;
    
            // Get the other area's selected categoria (if exists)
            const areas = document.querySelectorAll('.area-section');
            let selectedCategoria = null;
            if (areas.length > 1) {
                const otherArea = Array.from(areas).find(area => 
                    area.querySelector('.area-select') !== this
                );
                if (otherArea) {
                    selectedCategoria = otherArea.querySelector('.categoria-select').value;
                }
            }
    
            try {
                const response = await fetch(`/inscripcion/estudiante/categorias/${this.value}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        selectedCategoria: selectedCategoria
                    })
                });
    
                const data = await response.json();
                
                if (data.success) {
                    data.categorias.forEach(categoria => {
                        const option = document.createElement('option');
                        option.value = categoria.idCategoria;
                        option.textContent = categoria.nombre;
                        categoriaSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    
        const categoriaSelect = areaSelect.closest('.area-section').querySelector('.categoria-select');
        categoriaSelect.addEventListener('change', updateGradoSelect);
    }
    
    // Add event listener for initial categoria select
    document.addEventListener('DOMContentLoaded', function() {
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
        modalidadSelect.addEventListener('change', async function() {
            const grupoContainer = this.closest('.area-section').querySelector('.grupo-container');
            const grupoSelect = grupoContainer.querySelector('.grupo-select');
            
            if (this.value === 'duo' || this.value === 'equipo') {
                grupoContainer.style.display = 'block';
                grupoSelect.innerHTML = '<option value="">Seleccione un grupo</option>';
                
                try {
                    const response = await fetch(`/inscripcion/estudiante/grupos/${this.value}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                        }
                    });
    
                    const data = await response.json();
                    
                    if (data.success) {
                        data.grupos.forEach(grupo => {
                            const option = document.createElement('option');
                            option.value = grupo.id;
                            option.textContent = grupo.nombreGrupo;
                            grupoSelect.appendChild(option);
                        });
                        
                        // Add change event listener to grupo select
                        grupoSelect.addEventListener('change', updateAvailableGroups);
                        updateAvailableGroups();
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            } else {
                grupoContainer.style.display = 'none';
                grupoSelect.value = ''; // Clear selection when switching to individual
                updateAvailableGroups();
            }
        });
    }
    
    // Modify createNewArea function
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
        const newGrupoContainer = areaTemplate.querySelector('.grupo-container');
        
        setupAreaCategoriaListeners(newAreaSelect);
        setupModalidadGrupoListeners(newModalidadSelect);

        newAreaSelect.addEventListener('change', function() {
            updateAvailableAreas();
        });

        // Remove duplicate declaration and use the existing newModalidadSelect variable
        newModalidadSelect.addEventListener('change', function() {
            if (this.value === 'duo' || this.value === 'equipo') {
                newGrupoContainer.style.display = 'block';
            } else {
                newGrupoContainer.style.display = 'none';
            }
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
    initialAreaSelect.addEventListener('change', function() {
        updateAvailableAreas();
    });

    // Modify remove area functionality
    areasContainer.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-area')) {
            e.preventDefault();
            const areaToRemove = e.target.closest('.area-section');
            areaToRemove.remove();
            updateAreaVisibility();
            updateAvailableAreas();
        }
    });
    
    // Add area button click handler
    addAreaBtn.addEventListener('click', function() {
        const currentAreas = areasContainer.querySelectorAll('.area-section');
        if (currentAreas.length < MAX_AREAS) {
            createNewArea();
        }
    });
    
    // Remove area functionality
    areasContainer.addEventListener('click', function(e) {
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

    searchButton.addEventListener('click', async function() {
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
            document.querySelector('input[name="fechaNacimiento"]').value = estudiante.fechaNacimiento;
            document.querySelector('select[name="genero"]').value = estudiante.genero;
            document.querySelector('input[name="email"]').value = estudiante.email;

            searchResult.innerHTML = '<div class="alert alert-success">Estudiante encontrado</div>';

        } catch (error) {
            searchResult.innerHTML = '<div class="alert alert-danger">Error al buscar estudiante</div>';
            console.error('Error:', error);
        }
    });
});

// Add listeners to initial selects
document.addEventListener('DOMContentLoaded', function() {
    // This code is problematic because areasContainer and the setup functions are defined in another scope
    // Let's remove this duplicate event listener as it's causing errors
});