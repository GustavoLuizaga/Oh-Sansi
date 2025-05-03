document.addEventListener('DOMContentLoaded', function() {
    // Inicializar verificación del token
    document.querySelector('.btn-verificar-token').addEventListener('click', function() {
        const tokenInput = this.closest('.token-verification-container').querySelector('.tutor-token');
        validateTutorToken(tokenInput);
    });

    // Event listener para el cambio de área
    document.querySelector('.area-select').addEventListener('change', function() {
        loadCategorias(this);
    });

    async function validateTutorToken(input) {
        const token = input.value.trim();
        const tutorBlock = input.closest('.tutor-block');
        const statusElement = tutorBlock.querySelector('.token-status');
        const verifyButton = tutorBlock.querySelector('.btn-verificar-token');
        
        if (!token) {
            showTokenStatus(input, false, 'Por favor, ingrese un token');
            return;
        }
        
        try {
            verifyButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
            verifyButton.disabled = true;
            
            const response = await fetch(`/api/validate-tutor-token/${token}`);
            const data = await response.json();
            
            if (data.valid) {
                statusElement.textContent = 'Token válido';
                statusElement.classList.remove('invalid');
                statusElement.classList.add('valid');
                displayTutorInfo(tutorBlock, data);
                
                // Obtener las áreas asociadas al tutor
                const areasResponse = await fetch(`/api/tutor-token/${token}/areas`);
                const areasData = await areasResponse.json();
                
                if (areasData.success) {
                    const areaSelect = tutorBlock.querySelector('.area-select');
                    if (areaSelect) {
                        // Limpiar las opciones actuales excepto la primera
                        while (areaSelect.options.length > 1) {
                            areaSelect.remove(1);
                        }
                        
                        // Agregar las áreas del tutor
                        areasData.areas.forEach(area => {
                            const option = document.createElement('option');
                            option.value = area.idArea;
                            option.textContent = area.nombre;
                            areaSelect.appendChild(option);
                        });
                        
                        areaSelect.disabled = false;
                    }
                }
            } else {
                showTokenStatus(input, false, data.message || 'Token no válido');
                tutorBlock.querySelector('.tutor-info').style.display = 'none';
            }
        } catch (error) {
            console.error('Error:', error);
            showTokenStatus(input, false, 'Error al validar el token');
        } finally {
            verifyButton.innerHTML = '<i class="fas fa-check-circle"></i> Verificar';
            verifyButton.disabled = false;
        }
    }

    function displayTutorInfo(tutorBlock, data) {
        const tutorInfo = tutorBlock.querySelector('.tutor-info');
        tutorInfo.style.display = 'block';
        
        tutorBlock.querySelector('.tutor-delegacion').textContent = data.delegacion;
        tutorBlock.querySelector('.idDelegacion-input').value = data.idDelegacion;
    }

    function showTokenStatus(tokenInput, isValid, message) {
        const statusElement = tokenInput.closest('.tutor-block').querySelector('.token-status');
        statusElement.textContent = message;
        statusElement.className = 'token-status ' + (isValid ? 'valid' : 'invalid');
    }

    async function loadCategorias(areaSelect) {
        const areaId = areaSelect.value;
        const categoriaSelect = areaSelect.closest('.info-row').querySelector('.categoria-select');
        const idConvocatoria = document.querySelector('input[name="idConvocatoria"]').value;

        if (!areaId) {
            categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
            categoriaSelect.disabled = true;
            return;
        }

        try {
            categoriaSelect.innerHTML = '<option value="">Cargando categorías...</option>';
            categoriaSelect.disabled = true;
            
            const response = await fetch(`/api/convocatoria/${idConvocatoria}/area/${areaId}/categorias`);
            const data = await response.json();
            
            categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
            
            if (data && Array.isArray(data)) {
                if (data.length === 0) {
                    categoriaSelect.innerHTML = '<option value="">No hay categorías disponibles</option>';
                } else {
                    data.forEach(categoria => {
                        categoriaSelect.innerHTML += `<option value="${categoria.idCategoria}">${categoria.nombre}</option>`;
                    });
                    categoriaSelect.disabled = false;
                }
            }
        } catch (error) {
            console.error('Error loading categorias:', error);
            categoriaSelect.innerHTML = '<option value="">Error al cargar categorías</option>';
        }
    }

    // Event listener para cambios en la categoría
    document.querySelector('.categoria-select').addEventListener('change', function() {
        loadGrados(this);
    });

    async function loadGrados(categoriaSelect) {
        const categoriaId = categoriaSelect.value;
        const gradoSelect = document.querySelector('.grado-select-common');

        if (!categoriaId) {
            gradoSelect.innerHTML = '<option value="">Seleccione una categoría primero</option>';
            gradoSelect.disabled = true;
            return;
        }

        try {
            gradoSelect.innerHTML = '<option value="">Cargando grados...</option>';
            gradoSelect.disabled = true;

            const response = await fetch(`/api/categoria/${categoriaId}/grados`);
            const grados = await response.json();

            gradoSelect.innerHTML = '<option value="">Seleccione un grado</option>';
            
            if (grados && Array.isArray(grados)) {
                grados.forEach(grado => {
                    gradoSelect.innerHTML += `<option value="${grado.id}">${grado.nombre}</option>`;
                });
                gradoSelect.disabled = false;
            }
        } catch (error) {
            console.error('Error loading grados:', error);
            gradoSelect.innerHTML = '<option value="">Error al cargar grados</option>';
            gradoSelect.disabled = true;
        }
    }

    // Validación del formulario
    function validateForm(event) {
        event.preventDefault();

        // Validar número de contacto
        const numeroContacto = document.querySelector('input[name="numeroContacto"]');
        if (!numeroContacto || !numeroContacto.value || numeroContacto.value.length !== 8) {
            alert('Debe ingresar un número de contacto válido de 8 dígitos');
            return false;
        }

        // Validar tutor y área
        const tokenInput = document.querySelector('.tutor-token');
        const tutorInfo = document.querySelector('.tutor-info');
        const areaSelect = document.querySelector('.area-select');
        const categoriaSelect = document.querySelector('.categoria-select');

        if (!tokenInput.value.trim() || tutorInfo.style.display === 'none') {
            alert('Debe tener un tutor válido para continuar');
            return false;
        }

        if (!areaSelect.value || !categoriaSelect.value) {
            alert('Debe seleccionar un área y una categoría');
            return false;
        }

        // Validar grado
        const gradoSelect = document.querySelector('select[name="idGrado"]');
        if (!gradoSelect || !gradoSelect.value) {
            alert('Debe seleccionar un grado');
            return false;
        }

        // Si todo está validado, enviar el formulario
        document.getElementById('inscriptionForm').submit();
        return true;
    }

    // Asignar la función de validación al formulario
    document.getElementById('inscriptionForm').addEventListener('submit', validateForm);
});