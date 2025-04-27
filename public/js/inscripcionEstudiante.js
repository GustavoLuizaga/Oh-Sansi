document.addEventListener('DOMContentLoaded', function() {
    const tutorContainer = document.getElementById('tutorContainer');
    const addTutorBtn = document.getElementById('addTutorBtn');
    let tutorCount = 1;
    let areaCount = {}; // Para llevar el conteo de áreas por tutor

    // Handle verify token button clicks and remove tutor buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-verificar-token')) {
            const button = e.target.closest('.btn-verificar-token');
            const tokenInput = button.closest('.token-verification-container').querySelector('.tutor-token');
            validateTutorToken(tokenInput);
        } else if (e.target.closest('.btn-eliminar-tutor')) {
            const tutorBlock = e.target.closest('.tutor-block');
            removeTutorBlock(tutorBlock);
        } else if (e.target.closest('.btn-add-area')) {
            const tutorBlock = e.target.closest('.tutor-block');
            addAreaBlock(tutorBlock);
        } else if (e.target.closest('.btn-eliminar-area')) {
            const areaBlock = e.target.closest('.area-block');
            removeAreaBlock(areaBlock);
        }
    });
    
    // Agregar botón de eliminar al primer tutor
    addRemoveButtonToTutor(document.querySelector('.tutor-block'));
    
    // Inicializar los manejadores de eventos para el primer tutor
    initializeTokenVerification();
    
    // Agregar botón de eliminar al primer bloque de área
    const firstAreaBlock = document.querySelector('.area-block');
    if (firstAreaBlock && !firstAreaBlock.querySelector('.btn-eliminar-area')) {
        const areaRow = firstAreaBlock.querySelector('.info-row');
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn-eliminar-area';
        removeButton.innerHTML = '<i class="fas fa-trash"></i>';
        removeButton.title = 'Eliminar área';
        areaRow.appendChild(removeButton);
    }
    
    // Opcional: También validar al perder el foco
    document.addEventListener('blur', function(e) {
        if (e.target.classList.contains('tutor-token')) {
            if (e.target.value.trim().length >= 6) {
                validateTutorToken(e.target);
            }
        }
    }, true);

    // Handle category selection and area selection
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('categoria-select')) {
            loadGrados(e.target);
        } else if (e.target.classList.contains('area-select')) {
            loadCategorias(e.target);
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
        // Verificar si ya hay 2 tutores
        const existingTutors = document.querySelectorAll('.tutor-block').length;
        if (existingTutors >= 2) {
            alert('El máximo de tutores permitidos es 2');
            return;
        }
        
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
        if (categoriaSelect) {
            categoriaSelect.name = `idCategoria_${tutorCount}`;
        }
        
        const gradoSelect = tutorBlock.querySelector('.grado-select');
        if (gradoSelect) {
            gradoSelect.name = `idGrado_${tutorCount}`;
        }
        
        // Resetear el botón de verificación
        const verifyButton = tutorBlock.querySelector('.btn-verificar-token');
        if (verifyButton) {
            verifyButton.innerHTML = '<i class="fas fa-check-circle"></i> Verificar';
            verifyButton.disabled = false;
        }
        
        // Añadir el botón de eliminar al nuevo tutor
        addRemoveButtonToTutor(tutorBlock);
        
        // Limpiar cualquier área adicional que pudiera haber en el tutor clonado
        const areasContainer = tutorBlock.querySelector('.areas-container');
        const areaBlocks = areasContainer.querySelectorAll('.area-block');
        
        // Mantener solo el primer bloque de área y eliminar los demás
        if (areaBlocks.length > 1) {
            for (let i = 1; i < areaBlocks.length; i++) {
                areaBlocks[i].remove();
            }
        }
        
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
        
        // Inicializar el contador de áreas para este tutor
        areaCount[tutorCount] = 1;
    }

    // Función para agregar un nuevo bloque de área
    function addAreaBlock(tutorBlock) {
        // Verificar el número total de áreas en todos los tutores
        const totalAreas = document.querySelectorAll('.area-block').length;
        
        // Limitar a un máximo de 2 áreas en total para la inscripción
        if (totalAreas >= 2) {
            alert('El máximo de áreas por inscripción es 2');
            return;
        }
        
        // Obtener el índice del tutor
        const tutorIndex = parseInt(tutorBlock.querySelector('.tutor-header h3').textContent.replace('Tutor ', ''));
        
        // Inicializar el contador si no existe
        if (!areaCount[tutorIndex]) {
            areaCount[tutorIndex] = 1;
        }
        
        // Incrementar el contador de áreas
        areaCount[tutorIndex]++;
        
        // Clonar el primer bloque de área
        const areasContainer = tutorBlock.querySelector('.areas-container');
        const firstAreaBlock = areasContainer.querySelector('.area-block');
        const newAreaBlock = firstAreaBlock.cloneNode(true);
        
        // Limpiar los campos
        newAreaBlock.querySelectorAll('select').forEach(select => {
            select.value = '';
            if (select.classList.contains('categoria-select')) {
                select.disabled = true;
                select.innerHTML = '<option value="">Seleccione una categoría</option>';
            }
        });
        
        // Agregar botón de eliminar si no existe
        if (!newAreaBlock.querySelector('.btn-eliminar-area')) {
            const areaRow = newAreaBlock.querySelector('.info-row');
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn-eliminar-area';
            removeButton.innerHTML = '<i class="fas fa-trash"></i>';
            removeButton.title = 'Eliminar área';
            areaRow.appendChild(removeButton);
        }
        
        // Actualizar los nombres de los campos para que sean únicos
        const areaSelect = newAreaBlock.querySelector('.area-select');
        const categoriaSelect = newAreaBlock.querySelector('.categoria-select');
        
        areaSelect.name = `tutor_areas_${tutorIndex}_${areaCount[tutorIndex]}`;
        categoriaSelect.name = `tutor_categorias_${tutorIndex}_${areaCount[tutorIndex]}`;
        
        // Insertar el nuevo bloque antes del botón de agregar área
        areasContainer.insertBefore(newAreaBlock, areasContainer.querySelector('.btn-add-area'));
    }
    
    // Función para eliminar un bloque de área
    function removeAreaBlock(areaBlock) {
        const areasContainer = areaBlock.closest('.areas-container');
        const areaBlocks = areasContainer.querySelectorAll('.area-block');
        
        // Verificar si es el último bloque de área
        if (areaBlocks.length <= 1) {
            alert('Debe haber al menos un área');
            return;
        }
        
        // Eliminar el bloque
        areaBlock.remove();
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
        
        // Mostrar información de la delegación
        tutorBlock.querySelector('.tutor-delegacion').textContent = data.delegacion;
        
        // Guardar el área del tutor en un campo oculto (para referencia)
        tutorBlock.querySelector('.tutor-area-hidden').value = data.area;
        
        // Actualizar los campos ocultos con los IDs
        tutorBlock.querySelector('.idDelegacion-input').value = data.idDelegacion;
        
        // Seleccionar por defecto el área del tutor en el desplegable
        const areaSelect = tutorBlock.querySelector('.area-select');
        if (areaSelect) {
            const options = areaSelect.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value == data.idArea) {
                    options[i].selected = true;
                    break;
                }
            }
            
            // Cargar las categorías para el área seleccionada
            loadCategorias(areaSelect);
        }
    }
    
    // Función para cargar las categorías según el área seleccionada
    async function loadCategorias(areaSelect) {
        const areaId = areaSelect.value;
        const tutorBlock = areaSelect.closest('.tutor-block');
        const areaBlock = areaSelect.closest('.area-block');
        const categoriaSelect = areaBlock.querySelector('.categoria-select');
        const idConvocatoria = document.querySelector('input[name="idConvocatoria"]').value;
        
        // Resetear y deshabilitar el selector de categorías si no hay área seleccionada
        if (!areaId) {
            categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
            categoriaSelect.disabled = true;
            return;
        }
        
        try {
            // Mostrar estado de carga
            categoriaSelect.innerHTML = '<option value="">Cargando categorías...</option>';
            categoriaSelect.disabled = true;
            
            // Hacer la petición para obtener las categorías según el área y la convocatoria
            const response = await fetch(`/api/convocatoria/${idConvocatoria}/area/${areaId}/categorias`);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            
            categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
            
            if (data && Array.isArray(data)) {
                if (data.length === 0) {
                    categoriaSelect.innerHTML = '<option value="">No hay categorías disponibles</option>';
                    alert('No hay categorías disponibles para esta área en la convocatoria actual');
                } else {
                    data.forEach(categoria => {
                        categoriaSelect.innerHTML += `<option value="${categoria.idCategoria}">${categoria.nombre}</option>`;
                    });
                    categoriaSelect.disabled = false;
                }
            } else {
                console.error('No se recibieron categorías válidas');
                categoriaSelect.innerHTML = '<option value="">Formato de datos inválido</option>';
                alert('Error: Formato de datos inválido al cargar categorías');
            }
        } catch (error) {
            console.error('Error loading categorias:', error);
            categoriaSelect.innerHTML = '<option value="">Error al cargar categorías</option>';
            alert(`Error al cargar categorías: ${error.message}`);
        }
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

        // Validar tutores
        const tutorBlocks = document.querySelectorAll('.tutor-block');
        let validTutorFound = false;
        let totalValidAreas = 0;
        
        for (const tutorBlock of tutorBlocks) {
            const tokenInput = tutorBlock.querySelector('.tutor-token');
            const tutorInfo = tutorBlock.querySelector('.tutor-info');
            
            // Verificar si el tutor tiene un token válido y su información está visible
            if (tokenInput.value.trim() !== '' && tutorInfo.style.display !== 'none') {
                // Validar que cada tutor tenga al menos un área y categoría seleccionada
                const areaBlocks = tutorBlock.querySelectorAll('.area-block');
                let validAreaFound = false;
                
                for (const areaBlock of areaBlocks) {
                    const areaSelect = areaBlock.querySelector('.area-select');
                    const categoriaSelect = areaBlock.querySelector('.categoria-select');
                    
                    if (areaSelect.value && categoriaSelect.value) {
                        validAreaFound = true;
                        totalValidAreas++;
                    }
                }
                
                if (!validAreaFound) {
                    alert('Cada tutor debe tener al menos un área y categoría seleccionada');
                    return false;
                }
                
                validTutorFound = true;
            }
        }

        if (!validTutorFound) {
            alert('Debe tener al menos un tutor válido para continuar');
            return false;
        }
        
        // Validar el número total de áreas (máximo 2)
        if (totalValidAreas > 2) {
            alert('El máximo de áreas por inscripción es 2');
            return false;
        } else if (totalValidAreas === 0) {
            alert('Debe seleccionar al menos un área para la inscripción');
            return false;
        }

        // Validar grado común
        const gradoComun = document.querySelector('select[name="idGrado"]');
        if (!gradoComun || !gradoComun.value) {
            alert('Debe seleccionar un grado');
            return false;
        }

        // Si todo está validado, enviar el formulario
        document.getElementById('inscriptionForm').submit();
        return true;
    }
    
    // Inicializar el contador de áreas para el primer tutor
    areaCount[1] = 1;
});