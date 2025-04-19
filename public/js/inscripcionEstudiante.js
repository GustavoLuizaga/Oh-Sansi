document.addEventListener('DOMContentLoaded', function() {
    const tutorContainer = document.getElementById('tutorContainer');
    const addTutorBtn = document.getElementById('addTutorBtn');
    let tutorCount = 1;

    // Handle verify token button clicks and remove tutor buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-verificar-token')) {
            const button = e.target.closest('.btn-verificar-token');
            const tokenInput = button.closest('.token-verification-container').querySelector('.tutor-token');
            validateTutorToken(tokenInput);
        } else if (e.target.closest('.btn-eliminar-tutor')) {
            const tutorBlock = e.target.closest('.tutor-block');
            removeTutorBlock(tutorBlock);
        }
    });
    
    // Agregar botón de eliminar al primer tutor
    addRemoveButtonToTutor(document.querySelector('.tutor-block'));
    
    // Inicializar los manejadores de eventos para el primer tutor
    initializeTokenVerification();

    // Opcional: También validar al perder el foco
    document.addEventListener('blur', function(e) {
        if (e.target.classList.contains('tutor-token')) {
            if (e.target.value.trim().length >= 6) {
                validateTutorToken(e.target);
            }
        }
    }, true);

    // Handle category selection
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('categoria-select')) {
            loadGrados(e.target);
        }
    });

    // Add new tutor block
    addTutorBtn.addEventListener('click', function() {
        tutorCount++;
        addTutorBlock();
        
        // Ocultar el botón si ya hay 2 tutores
        if (tutorCount >= 2) {
            addTutorBtn.style.display = 'none';
        }
    });
    
    // Función para agregar botón de eliminar a un tutor
    function addRemoveButtonToTutor(tutorBlock) {
        // Verificar si ya tiene un botón de eliminar
        if (tutorBlock.querySelector('.btn-eliminar-tutor')) {
            return;
        }
        
        const tutorHeader = tutorBlock.querySelector('.tutor-header');
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn-eliminar-tutor';
        removeButton.innerHTML = '<i class="fas fa-trash"></i>';
        removeButton.title = 'Eliminar tutor';
        tutorHeader.appendChild(removeButton);
    }
    
    // Función para eliminar un bloque de tutor
    function removeTutorBlock(tutorBlock) {
        // Verificar si es el último tutor
        const tutorBlocks = document.querySelectorAll('.tutor-block');
        if (tutorBlocks.length <= 1) {
            alert('Debe haber al menos un tutor');
            return;
        }

        tutorBlock.remove();
        tutorCount--;
        
        // Actualizar los números de los tutores restantes
        const remainingBlocks = document.querySelectorAll('.tutor-block');
        remainingBlocks.forEach((block, index) => {
            block.querySelector('.tutor-header h3').textContent = `Tutor ${index + 1}`;
            
            // Actualizar los nombres de los campos
            const categoriaSelect = block.querySelector('.categoria-select');
            if (categoriaSelect) {
                categoriaSelect.name = `idCategoria`;
            }
            
            const gradoSelect = block.querySelector('.grado-select');
            if (gradoSelect) {
                gradoSelect.name = `idGrado`;
            }
        });
        
        // Mostrar el botón de agregar tutor si hay menos de 2 tutores
        if (tutorCount < 2) {
            addTutorBtn.style.display = 'block';
        }
    }
    
    // Función para inicializar los manejadores de eventos para verificación de token
    function initializeTokenVerification() {
        document.querySelectorAll('.btn-verificar-token').forEach(button => {
            button.addEventListener('click', function() {
                const tokenInput = this.closest('.token-verification-container').querySelector('.tutor-token');
                validateTutorToken(tokenInput);
            });
        });
    }

    function addTutorBlock() {
        const tutorBlock = document.querySelector('.tutor-block').cloneNode(true);
        
        // Limpiar todos los campos
        tutorBlock.querySelectorAll('input, select').forEach(input => {
            input.value = '';
            if (input.classList.contains('categoria-select') || input.classList.contains('grado-select')) {
                input.disabled = true;
            }
        });
        
        // Resetear el estado del token
        const statusElement = tutorBlock.querySelector('.token-status');
        if (statusElement) {
            statusElement.textContent = '';
            statusElement.className = 'token-status';
        }
        
        // Ocultar la información del tutor hasta que se valide el token
        tutorBlock.querySelector('.tutor-info').style.display = 'none';
        
        // Actualizar el título del tutor
        tutorBlock.querySelector('.tutor-header h3').textContent = `Tutor ${tutorCount}`;
        
        // Asegurarse de que los nombres de los campos sean únicos para cada tutor
        const categoriaSelect = tutorBlock.querySelector('.categoria-select');
        categoriaSelect.name = `idCategoria_${tutorCount}`;
        
        const gradoSelect = tutorBlock.querySelector('.grado-select');
        gradoSelect.name = `idGrado_${tutorCount}`;
        
        // Resetear el botón de verificación
        const verifyButton = tutorBlock.querySelector('.btn-verificar-token');
        if (verifyButton) {
            verifyButton.innerHTML = '<i class="fas fa-check-circle"></i> Verificar';
            verifyButton.disabled = false;
        }
        
        // Añadir el botón de eliminar al nuevo tutor
        addRemoveButtonToTutor(tutorBlock);
        
        // Añadir el nuevo bloque al contenedor
        tutorContainer.appendChild(tutorBlock);
        
        // Asegurarse de que el botón de agregar tutor se muestre correctamente
        if (tutorCount < 2) {
            addTutorBtn.style.display = 'block';
        } else {
            addTutorBtn.style.display = 'none';
        }
        
        // Inicializar los manejadores de eventos para el nuevo tutor
        initializeTokenVerification();
    }

    async function validateTutorToken(input) {
        const token = input.value.trim();
        const tutorBlock = input.closest('.tutor-block');
        const statusElement = tutorBlock.querySelector('.token-status');
        const verifyButton = tutorBlock.querySelector('.btn-verificar-token');
        
        try {
            verifyButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
            verifyButton.disabled = true;
            
            console.log('Validating token:', token); // Add debugging
            
            const response = await fetch(`/api/validate-tutor-token/${token}`);
            const data = await response.json();
            
            console.log('Response:', data); // Add debugging
            
            if (data.valid) {
                statusElement.textContent = 'Token válido';
                statusElement.classList.add('valid');
                displayTutorInfo(tutorBlock, data);
            } else {
                statusElement.textContent = data.message || 'Token no válido';
                statusElement.classList.add('invalid');
                tutorBlock.querySelector('.tutor-info').style.display = 'none';
            }
        } catch (error) {
            console.error('Error:', error);
            statusElement.textContent = 'Error al validar el token';
            statusElement.classList.add('invalid');
        } finally {
            verifyButton.innerHTML = '<i class="fas fa-check-circle"></i> Verificar';
            verifyButton.disabled = false;
        }
    }

    function displayTutorInfo(tutorBlock, data) {
        const tutorInfo = tutorBlock.querySelector('.tutor-info');
        tutorInfo.style.display = 'block';
        
        // Mostrar información del área y delegación
        tutorBlock.querySelector('.tutor-area').textContent = data.area;
        tutorBlock.querySelector('.tutor-delegacion').textContent = data.delegacion;
        
        // Actualizar los campos ocultos con los IDs
        tutorBlock.querySelector('.idArea-input').value = data.idArea;
        tutorBlock.querySelector('.idDelegacion-input').value = data.idDelegacion;
        
        // Cargar las categorías disponibles
        const categoriaSelect = tutorBlock.querySelector('.categoria-select');
        categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
        
        if (data.categorias && Array.isArray(data.categorias)) {
            data.categorias.forEach(categoria => {
                categoriaSelect.innerHTML += `<option value="${categoria.id}">${categoria.nombre}</option>`;
            });
        }
        
        // Habilitar el selector de categoría
        categoriaSelect.disabled = false;
    }

    async function loadGrados(categoriaSelect) {
        const categoriaId = categoriaSelect.value;
        const tutorBlock = categoriaSelect.closest('.tutor-block');
        const gradoSelect = tutorBlock.querySelector('.grado-select');
        
        // Resetear y deshabilitar el selector de grados si no hay categoría seleccionada
        if (!categoriaId) {
            gradoSelect.innerHTML = '<option value="">Seleccione un grado</option>';
            gradoSelect.disabled = true;
            return;
        }
        
        try {
            // Mostrar estado de carga
            gradoSelect.innerHTML = '<option value="">Cargando grados...</option>';
            
            const response = await fetch(`/api/categoria/${categoriaId}/grados`);
            const grados = await response.json();
            
            gradoSelect.innerHTML = '<option value="">Seleccione un grado</option>';
            
            if (grados && Array.isArray(grados)) {
                grados.forEach(grado => {
                    gradoSelect.innerHTML += `<option value="${grado.id}">${grado.nombre}</option>`;
                });
            }
            
            // Habilitar el selector de grados
            gradoSelect.disabled = false;
        } catch (error) {
            console.error('Error loading grados:', error);
            gradoSelect.innerHTML = '<option value="">Error al cargar grados</option>';
        }
    }

    function validateForm(event) {
        event.preventDefault();

        // Validar número de contacto
        const numeroContacto = document.querySelector('input[name="numeroContacto"]');
        if (!numeroContacto || !numeroContacto.value || numeroContacto.value.length !== 8) {
            alert('Debe ingresar un número de contacto válido de 8 dígitos');
            return false;
        }

        // Validar token de tutor
        const tutorTokens = document.querySelectorAll('input[name="tutor_tokens[]"]');
        let validTokenFound = false;
        tutorTokens.forEach(token => {
            if (token.value.trim() !== '') {
                validTokenFound = true;
            }
        });

        if (!validTokenFound) {
            alert('Debe ingresar al menos un token de tutor válido');
            return false;
        }

        // Validar categoría y grado
        const categoria = document.querySelector('select[name="idCategoria"]');
        const grado = document.querySelector('select[name="idGrado"]');

        if (!categoria || !categoria.value) {
            alert('Debe seleccionar una categoría');
            return false;
        }

        if (!grado || !grado.value) {
            alert('Debe seleccionar un grado');
            return false;
        }

        // Si todo está validado, enviar el formulario
        document.getElementById('inscriptionForm').submit();
        return true;
    }
});