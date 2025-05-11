document.addEventListener('DOMContentLoaded', function () {
    // Referencias a elementos del formulario
    const ciInput = document.querySelector('input[name="ci"]');
    const nombresInput = document.querySelector('input[name="nombres"]');
    const apellidoPaternoInput = document.querySelector('input[name="apellidoPaterno"]');
    const apellidoMaternoInput = document.querySelector('input[name="apellidoMaterno"]');
    const fechaNacimientoInput = document.querySelector('input[name="fechaNacimiento"]');
    const generoSelect = document.querySelector('select[name="genero"]');
    const emailInput = document.querySelector('input[name="email"]');
    const telefonoInput = document.querySelector('input[name="telefono"]');
    const nombreCompletoTutorInput = document.querySelector('input[name="nombreCompletoTutor"]');
    const correoTutorInput = document.querySelector('input[name="correoTutor"]');
    const areaSelect = document.getElementById('areaSelect');
    const categoriaSelect = document.getElementById('categoriaSelect');
    const gradoSelect = document.getElementById('gradoSelect');
    const registrationForm = document.querySelector('.registration-form');

    // Agregar elementos para la selección de usuario existente/nuevo
    const formHeader = document.querySelector('.form-card h2');
    if (formHeader) {
        const userTypeSelector = document.createElement('div');
        userTypeSelector.className = 'user-type-selector';
        userTypeSelector.innerHTML = `
            <div class="selector-container">
                <label class="selector-label">Tipo de registro:</label>
                <div class="selector-options">
                    <label class="option">
                        <input type="radio" name="userType" value="existing" checked>
                        <span>Estudiante existente</span>
                    </label>
                    <label class="option">
                        <input type="radio" name="userType" value="new">
                        <span>Nuevo estudiante</span>
                    </label>
                </div>
            </div>
            <div class="ci-verification-container" id="ciVerificationContainer">
                <div class="input-group">
                    <label>CI del estudiante</label>
                    <div class="input-with-button">
                        <input type="text" id="ciVerification" placeholder="Ingrese CI para verificar">
                        <button type="button" id="verifyCI" class="verify-button">
                            <i class="fas fa-search"></i> Verificar
                        </button>
                    </div>
                </div>
                <div id="verificationResult" class="verification-result"></div>
            </div>
        `;
        formHeader.insertAdjacentElement('afterend', userTypeSelector);

        // Agregar contenedor para áreas adicionales
        const academicSection = document.querySelector('.form-section:last-child');
        if (academicSection) {
            const additionalAreasContainer = document.createElement('div');
            additionalAreasContainer.id = 'additionalAreasContainer';
            additionalAreasContainer.className = 'additional-areas-container';
            additionalAreasContainer.innerHTML = `
                <h3 class="mt-4">Áreas adicionales (opcional)</h3>
                <div class="area-container" id="additionalArea" style="display: none;">
                    <div class="input-row">
                        <div class="input-group">
                            <label>Área</label>
                            <select id="areaSelect2" name="area2">
                                <option value="">Seleccione un área</option>
                                ${areaSelect.innerHTML.split('<option value="">Seleccione un área</option>')[1] || ''}
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Categoría</label>
                            <select id="categoriaSelect2" name="categoria2">
                                <option value="">Seleccione una categoría</option>
                            </select>
                        </div>
                        <!-- Eliminamos el selector de grado ya que debe ser único por inscripción -->
                        <div class="input-group area-note">
                            <small class="text-info">Nota: El grado seleccionado en el área principal se aplicará a todas las áreas.</small>
                        </div>
                    </div>
                </div>
                <div class="toggle-area-container">
                    <button type="button" id="toggleAdditionalArea" class="toggle-area-button">
                        <i class="fas fa-plus-circle"></i> Agregar otra área
                    </button>
                </div>

                <div class="input-group modalidad-container">
                    <label>Modalidad de participación</label>
                    <select id="modalidadSelect" name="modalidad">
                        <option value="individual">Individual</option>
                        <option value="duo">Dúo</option>
                        <option value="equipo">Equipo</option>
                    </select>
                </div>

                <div class="input-group grupo-container" style="display: none;">
                    <label>Grupo</label>
                    <select id="grupoSelect" name="grupo">
                        <option value="">Seleccione un grupo</option>
                    </select>
                </div>
            `;
            academicSection.appendChild(additionalAreasContainer);
        }

        // Eventos para el selector de tipo de usuario
        const userTypeRadios = document.querySelectorAll('input[name="userType"]');
        const ciVerificationContainer = document.getElementById('ciVerificationContainer');
        const personalInfoSection = document.querySelector('.form-section:first-child');

        userTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'existing') {
                    ciVerificationContainer.style.display = 'block';
                    personalInfoSection.style.opacity = '0.5';
                    personalInfoSection.style.pointerEvents = 'none';
                    // Deshabilitar campos de información personal
                    togglePersonalFieldsState(true);
                } else {
                    ciVerificationContainer.style.display = 'none';
                    personalInfoSection.style.opacity = '1';
                    personalInfoSection.style.pointerEvents = 'auto';
                    // Habilitar campos de información personal y limpiarlos
                    togglePersonalFieldsState(false);
                    clearPersonalFields();
                }
            });
        });

        // Inicializar estado
        personalInfoSection.style.opacity = '0.5';
        personalInfoSection.style.pointerEvents = 'none';
        togglePersonalFieldsState(true);

        // Evento para verificar CI
        const verifyButton = document.getElementById('verifyCI');
        const ciVerificationInput = document.getElementById('ciVerification');
        const verificationResult = document.getElementById('verificationResult');

        verifyButton.addEventListener('click', function() {
            const ci = ciVerificationInput.value.trim();
            if (!ci) {
                verificationResult.innerHTML = '<div class="alert alert-warning">Por favor, ingrese un CI válido.</div>';
                return;
            }

            verificationResult.innerHTML = '<div class="alert alert-info">Verificando...</div>';

            // Llamada AJAX para verificar el CI
            fetch(`/verificar-estudiante/${ci}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists && data.isEstudiante) {
                        verificationResult.innerHTML = '<div class="alert alert-success">Estudiante encontrado. Cargando datos...</div>';
                        // Llenar el formulario con los datos del estudiante
                        fillFormWithStudentData(data.estudiante);
                        // Mostrar la sección pero mantener los campos deshabilitados
                        personalInfoSection.style.opacity = '1';
                        personalInfoSection.style.pointerEvents = 'none';
                        togglePersonalFieldsState(true);
                    } else if (data.exists && !data.isEstudiante) {
                        verificationResult.innerHTML = '<div class="alert alert-warning">El usuario existe pero no es un estudiante.</div>';
                    } else {
                        verificationResult.innerHTML = '<div class="alert alert-danger">No se encontró ningún estudiante con ese CI.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error al verificar CI:', error);
                    verificationResult.innerHTML = '<div class="alert alert-danger">Error al verificar. Intente nuevamente.</div>';
                });
        });

        // Evento para mostrar/ocultar área adicional
        const toggleAdditionalAreaBtn = document.getElementById('toggleAdditionalArea');
        const additionalAreaDiv = document.getElementById('additionalArea');

        toggleAdditionalAreaBtn.addEventListener('click', function() {
            if (additionalAreaDiv.style.display === 'none') {
                additionalAreaDiv.style.display = 'block';
                this.innerHTML = '<i class="fas fa-minus-circle"></i> Quitar área adicional';
            } else {
                additionalAreaDiv.style.display = 'none';
                this.innerHTML = '<i class="fas fa-plus-circle"></i> Agregar otra área';
                // Limpiar selecciones
                document.getElementById('areaSelect2').value = '';
                document.getElementById('categoriaSelect2').value = '';
            }
        });

        // Evento para cambiar modalidad y mostrar grupos
        const modalidadSelect = document.getElementById('modalidadSelect');
        const grupoContainer = document.querySelector('.grupo-container');
        const grupoSelect = document.getElementById('grupoSelect');

        modalidadSelect.addEventListener('change', function() {
            const modalidad = this.value;
            if (modalidad === 'duo' || modalidad === 'equipo') {
                grupoContainer.style.display = 'block';
                // Cargar grupos según la modalidad
                loadGrupos(modalidad);
            } else {
                grupoContainer.style.display = 'none';
                grupoSelect.innerHTML = '<option value="">Seleccione un grupo</option>';
            }
        });

        // Eventos para cargar categorías y grados para el área principal
        const idConvocatoria = document.body.getAttribute('data-convocatoria-id') || document.querySelector('input[name="idConvocatoria"]')?.value || '';
        
        // Evento para cargar categorías cuando se selecciona un área principal
        areaSelect.addEventListener('change', function() {
            const idArea = this.value;
            categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
            gradoSelect.innerHTML = '<option value="">Seleccione un grado</option>';

            if (idArea) {
                fetch(`/obtener-categorias/${idConvocatoria}/${idArea}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(categoria => {
                            const option = document.createElement('option');
                            option.value = categoria.idCategoria;
                            option.textContent = categoria.nombre;
                            categoriaSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar categorías:', error);
                    });
            }
        });

        // Evento para cargar grados cuando se selecciona una categoría principal
        categoriaSelect.addEventListener('change', function() {
            const idCategoria = this.value;
            gradoSelect.innerHTML = '<option value="">Seleccione un grado</option>';

            if (idCategoria) {
                fetch(`/obtener-grados/${idCategoria}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(grado => {
                            const option = document.createElement('option');
                            option.value = grado.idGrado;
                            option.textContent = grado.grado;
                            gradoSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar grados:', error);
                    });
            }
        });
        
        // Eventos para cargar categorías y grados para el área adicional
        const areaSelect2 = document.getElementById('areaSelect2');
        const categoriaSelect2 = document.getElementById('categoriaSelect2');
        const gradoSelect2 = document.getElementById('gradoSelect2');
        // Usamos el mismo ID de convocatoria que para el área principal

        areaSelect2.addEventListener('change', function() {
            const idArea = this.value;
            categoriaSelect2.innerHTML = '<option value="">Seleccione una categoría</option>';

            if (idArea) {
                fetch(`/obtener-categorias/${idConvocatoria}/${idArea}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(categoria => {
                            const option = document.createElement('option');
                            option.value = categoria.idCategoria;
                            option.textContent = categoria.nombre;
                            categoriaSelect2.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar categorías:', error);
                    });
            }
        });

        // Ya no necesitamos el evento para cargar grados en el área adicional
        // porque usamos el mismo grado para todas las áreas

        // Validación del formulario antes de enviar
        registrationForm.addEventListener('submit', function(event) {
            // Verificar si se seleccionó un área adicional pero no se completaron todos los campos
            if (additionalAreaDiv.style.display !== 'none') {
                const area2 = document.getElementById('areaSelect2').value;
                const categoria2 = document.getElementById('categoriaSelect2').value;
                
                if ((area2 && !categoria2) || (categoria2 && !area2)) {
                    event.preventDefault();
                    alert('Si selecciona un área adicional, debe completar todos los campos relacionados (Área y Categoría).');
                    return false;
                }
            }

            // Verificar si se seleccionó modalidad dúo o equipo pero no se seleccionó un grupo
            const modalidad = document.getElementById('modalidadSelect').value;
            const grupo = document.getElementById('grupoSelect').value;
            
            if ((modalidad === 'duo' || modalidad === 'equipo') && !grupo) {
                event.preventDefault();
                alert('Para la modalidad seleccionada, debe elegir un grupo.');
                return false;
            }
        });
    } // Closing brace for if(formHeader) condition

    // Función para habilitar/deshabilitar campos de información personal
    function togglePersonalFieldsState(disabled) {
        const personalFields = [
            nombresInput, apellidoPaternoInput, apellidoMaternoInput, ciInput,
            fechaNacimientoInput, generoSelect, emailInput, telefonoInput
        ];
        
        personalFields.forEach(field => {
            if (field) field.disabled = disabled;
        });
    }

    // Función para limpiar campos de información personal
    function clearPersonalFields() {
        const personalFields = [
            nombresInput, apellidoPaternoInput, apellidoMaternoInput, ciInput,
            fechaNacimientoInput, generoSelect, emailInput, telefonoInput
        ];
        
        personalFields.forEach(field => {
            if (field) field.value = '';
        });
    }

    // Función para llenar el formulario con los datos del estudiante
    function fillFormWithStudentData(estudiante) {
        if (nombresInput) nombresInput.value = estudiante.name || '';
        if (apellidoPaternoInput) apellidoPaternoInput.value = estudiante.apellidoPaterno || '';
        if (apellidoMaternoInput) apellidoMaternoInput.value = estudiante.apellidoMaterno || '';
        if (ciInput) ciInput.value = estudiante.ci || '';
        if (fechaNacimientoInput) fechaNacimientoInput.value = estudiante.fechaNacimiento || '';
        if (generoSelect) {
            // Asegurarse de que el valor del género se establezca correctamente
            generoSelect.value = estudiante.genero || '';
            console.log('Estableciendo género:', estudiante.genero);
        }
        if (emailInput) emailInput.value = estudiante.email || '';
        if (telefonoInput) telefonoInput.value = estudiante.telefono || '';
    }

    // Función para cargar grupos según la modalidad
    function loadGrupos(modalidad) {
        const grupoSelect = document.getElementById('grupoSelect');
        grupoSelect.innerHTML = '<option value="">Cargando grupos...</option>';

        // El backend ya filtra los grupos por la delegación del tutor autenticado
        fetch(`/obtener-grupos/${modalidad}`)
            .then(response => response.json())
            .then(data => {
                grupoSelect.innerHTML = '<option value="">Seleccione un grupo</option>';
                if (data.length === 0) {
                    const noGruposOption = document.createElement('option');
                    noGruposOption.disabled = true;
                    noGruposOption.textContent = 'No hay grupos disponibles para su delegación';
                    grupoSelect.appendChild(noGruposOption);
                } else {
                    data.forEach(grupo => {
                        const option = document.createElement('option');
                        option.value = grupo.id;
                        option.textContent = grupo.nombreGrupo || `Grupo ${grupo.codigoInvitacion}`;
                        grupoSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error al cargar grupos:', error);
                grupoSelect.innerHTML = '<option value="">Error al cargar grupos</option>';
            });
    }
}); // Closing brace for DOMContentLoaded event listener