@push('styles')
    <link rel="stylesheet" href="{{ asset('css/inscripcion/FormularioDatosInscripcionEst.css') }}">
@endpush
@push('scripts')
    <!-- Carga Tesseract.js de forma tradicional -->
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    <script src="{{ asset('js/FormularioDatosInscripcionEst.js') }}"></script>
@endpush
<x-app-layout>
    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-1">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('numero_boleta'))
    <div class="alert alert-info py-1 px-2 mb-1">
        <i class="fas fa-file-invoice"></i> Número de boleta detectado: {{ session('numero_boleta') }}
    </div>
    @endif

    <!-- Header Section -->
    <div class="estudiantes-header py-2">
        <h1><i class="fas fa-user-plus"></i> Datos de Inscripción del Postulante</h1>
    </div>

    <!-- Actions Container -->
    <div class="actions-container mb-1">
        <div class="button-group">
            <a href="{{ route('inscripcion.estudiante') }}" class="add-button py-1 px-2">
                <i class="fas fa-arrow-left"></i> Volver Formulario de Inscripción
            </a>
        </div>
        <div class="export-buttons">
            <button type="button" class="export-button pdf py-1 px-2" id="exportPdf">
                <i class="fas fa-file-pdf"></i> Generar orden de pago
            </button>
            
            <button type="button" class="export-button excel py-1 px-2" id="importComprobante" data-bs-toggle="modal" data-bs-target="#SubirComprobantePago">
                <i class="fas fa-file-pdf"></i> Subir comprobante de pago
            </button>
        </div>
    </div>

        <!-- IDs ocultos para uso en JS -->
        <div id="data-ids" 
        data-estudiante-id="{{ $ids['estudiante_id'] }}"
        data-tutor-id="{{ $ids['tutor_id'] }}"
        data-inscripcion-id="{{ $ids['inscripcion_id'] }}"
        data-convocatoria-id="{{ $ids['convocatoria_id'] }}"
        data-delegacion-id="{{ $ids['delegacion_id'] }}"
        data-grado-id="{{ $ids['grado_id'] }}"
        style="display: none;">
    </div>

    <!-- Main Form -->
        <form id="inscriptionForm" method="POST" action="#" class="inscription-form" onsubmit="return validateForm(event)">
            <input type="hidden" name="idConvocatoria" value="1">

            <!-- Instrucciones del Formulario -->
            <div class="form-instructions">
                <h2>Complete todos los campos del formulario</h2>
            </div>

            <div class="form-content">
                <!-- Información Personal -->
                <div class="formulario-seccion" id="personal-info">
                    <div class="seccion-card">
                        <div class="seccion-header">
                            <h2><i class="fas fa-user"></i> Información Personal</h2>
                        </div>
                        <div class="seccion-body">
                            <div class="info-group">
                                <label>Nombre Completo</label>
                                <div class="info-value">
                                    {{ $estudiante['nombre'] }} {{ $estudiante['apellido_paterno'] }} {{ $estudiante['apellido_materno'] }} 
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-group">
                                    <label>Cédula de identidad</label>
                                    <div class="info-value">{{ $estudiante['ci'] }}</div>
                                </div>
                                <div class="info-group">
                                    <label>Fecha de Nacimiento</label>
                                    <div class="info-value">
                                        @if($estudiante['fecha_nacimiento'])
                                            {{ date('d/m/Y', strtotime($estudiante['fecha_nacimiento'])) }}
                                        @else
                                            No especificado
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-group">
                                    <label>Correo electrónico</label>
                                    <div class="info-value">{{ $estudiante['email'] ?? 'No especificado' }}</div>
                                </div>
                                <div class="info-group">
                                    <label>Género</label>
                                    <div class="info-value">
                                        @if($estudiante['genero'] == 'M')
                                            Masculino
                                        @elseif($estudiante['genero'] == 'F')
                                            Femenino
                                        @else
                                            No especificado
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Información de Contacto -->
                <div class="formulario-seccion" id="contact-info">
                    <div class="seccion-card">
                        <div class="seccion-header">
                            <h2><i class="fas fa-phone"></i> Información de Contacto</h2>
                        </div>
                        <div class="seccion-body">
                            <div class="input-grupo">
                                <label for="numeroContacto">Número de Contacto</label>
                                <div class="input-with-icon">
                                    <input type="tel" id="numeroContacto" name="numeroContacto" required
                                        maxlength="8" pattern="[0-9]{8}"
                                        placeholder="Ej: 63772394">
                                </div>
                                <span class="error-message"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Tutores -->
                <div class="formulario-seccion" id="tutor-info">
                    <div class="seccion-card">
                        <div class="seccion-header">
                            <h2><i class="fas fa-chalkboard-teacher"></i> Información de Tutores</h2>
                            <p class="section-subtitle">Puede agregar hasta 2 tutores</p>
                        </div>
                        <div class="seccion-body">
                            <div id="tutorContainer">
                                <div class="tutor-block">
                                    <div class="tutor-header">
                                        <h3>Tutor 1</h3>
                                    </div>
                                    <div class="input-grupo">
                                        <label>Token del Tutor</label>
                                        <div class="input-with-icon token-verification-container">
                                            <input type="text" class="tutor-token" name="tutor_tokens[]"
                                                placeholder="Ingrese el token del tutor" required>
                                            <button type="button" class="btn-verificar-token">
                                                <i class="fas fa-check-circle"></i> Verificar
                                            </button>
                                        </div>
                                        <span class="token-status"></span>
                                    </div>
                                    <div class="tutor-info" style="display: none;">
                                        <div class="info-row">
                                            <div class="info-group">
                                                <label>Delegación</label>
                                                <div class="info-value tutor-delegacion"></div>
                                                <input type="hidden" class="idDelegacion-input" name="tutor_delegaciones[]">
                                            </div>
                                        </div>
                                        
                                        <!-- Áreas y Categorías -->
                                        <div class="areas-container">
                                            <div class="area-block">
                                                <div class="info-row">
                                                    <div class="info-group">
                                                        <label>Área</label>
                                                        <select class="area-select" name="tutor_areas_1_1" required>
                                                            <option value="">Seleccione un área</option>
                                                            <option value="1">Área 1</option>
                                                            <option value="2">Área 2</option>
                                                            <option value="3">Área 3</option>
                                                        </select>
                                                        <input type="hidden" class="tutor-area-hidden" value="">
                                                    </div>
                                                    <div class="input-grupo">
                                                        <label>Categoría</label>
                                                        <select class="categoria-select" name="tutor_categorias_1_1" required>
                                                            <option value="">Seleccione una categoría</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-add-area">
                                                <i class="fas fa-plus-circle"></i> Agregar otra área
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="addTutorBtn" class="btn-add-tutor">
                                <i class="fas fa-plus"></i> Agregar otro tutor
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Grado Común -->
            <div class="formulario-seccion" id="grado-info">
                <div class="seccion-card">
                    <div class="seccion-header">
                        <h2><i class="fas fa-graduation-cap"></i> Selección de Grado</h2>
                        <p class="section-subtitle">El grado se cargará automáticamente según las categorías seleccionadas</p>
                    </div>
                    <div class="seccion-body">
                        <div class="input-grupo">
                            <label for="idGrado">Grado</label>
                            <select id="idGrado" name="idGrado" class="grado-select-common" required disabled>
                                <option value="">Seleccione un grado</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón de Envío -->
            <div class="subir-formulario">
                <button type="submit" class="btn-subir">
                    <i class="fas fa-check"></i> Completar Inscripción
                </button>
            </div>
        </form>


    <!-- Modal para SUBIR comprobante de Pago -->
    <div class="modal fade" id="SubirComprobantePago" tabindex="-1" aria-labelledby="SubirComprobantePagoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h2 class="modal-title fs-4 fw-bold" id="SubirComprobantePagoLabel">
                        <i class="fas fa-file-upload me-2"></i>Subir Comprobante de Pago
                    </h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-file-invoice-dollar display-4 mb-3" id="icon-dollar"></i>
                        <p class="lead">Por favor, sube tu comprobante de pago para completar el proceso de inscripción </p>
                        <div>Una vez se verifique el Nro.Comprobante sea correcto, seras aceptado oficialmente como estudiante inscrito en las Olimpiadas Oh Sansi!!</div>
                    </div>
                    
                    <form id="comprobantePagoForm" enctype="multipart/form-data">
                    @csrf <!-- Faltaba el token CSRF -->
                    <input type="hidden" name="estudiante_id" value="{{ $ids['estudiante_id'] }}">
                    <input type="hidden" name="inscripcion_id" value="{{ $ids['inscripcion_id'] }}">
                        <div class="file-drop-area border-2 border-dashed rounded-3 p-5 text-center mb-3">
                            <input type="file" id="comprobantePagoFile" name="comprobantePago" class="file-input" accept=".pdf,.jpg,.jpeg,.png">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <p class="mb-1">Arrastra y suelta tu comprobante aquí</p>
                            <p class="text-muted small mb-3">o</p>
                            <label for="comprobantePagoFile" class="btn btn-primary px-4">
                                <i class="fas fa-folder-open me-2"></i>Buscar Archivo
                            </label>
                            <p class="small text-muted mt-2">Formatos aceptados: PDF, JPG, PNG (Tamaño máximo: 5MB)</p>
                        </div>
                        
                        <div class="file-feedback text-danger" style="display: none;"></div>
                        
                        <div class="file-preview p-4 border rounded text-center" style="display: none;">
                            <!-- Previsualización para imágenes -->
                            <div class="image-preview mb-3" style="display: none;">
                                <img src="" alt="Vista previa" class="img-preview img-fluid mb-2" style="max-height: 200px;">
                            </div>
                            
                            <!-- Icono para PDF -->
                            <div class="pdf-preview mb-3" style="display: none;">
                                <i class="fas fa-file-pdf fa-4x text-danger mb-2"></i>
                            </div>
                            
                            <!-- Nombre del archivo -->
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-paperclip me-2"></i>
                                <span class="file-name fw-bold"></span>
                                <button type="button" class="btn-remove-file ms-3 btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash-alt me-1"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal" title="Cancelar subida de comprobante de pago">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <!-- Cambiar el ID del botón de submit para evitar conflicto -->
                    <button type="submit" form="comprobantePagoForm" class="btn-save" id="btnSubirComprobante" title="Subir el comprobante de pago para su verificacion" disabled>
                        <i class="fas fa-upload me-2"></i>Subir Comprobante
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Export PDF button
        document.getElementById('exportPdf').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = "{{ route('inscripcionEstudiante.exportar.pdf') }}";
        });
        
    }); 
    document.addEventListener('DOMContentLoaded', function() 
    {
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
        
        // Actualizar los nombres de los campos del primer bloque de área
        const areaSelect = firstAreaBlock.querySelector('.area-select');
        const categoriaSelect = firstAreaBlock.querySelector('.categoria-select');
        if (areaSelect && categoriaSelect) {
            areaSelect.name = 'tutor_areas_1_1';
            categoriaSelect.name = 'tutor_categorias_1_1';
        }
    }
    
    // Inicializar el contador de áreas para el primer tutor
    areaCount[1] = 1;
    
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
            // Actualizar el estado del botón de agregar tutor según el número de áreas
            updateAddTutorButtonState();
        } else if (e.target.classList.contains('area-select')) {
            loadCategorias(e.target);
        }
    });
    
    // Función para actualizar el estado del botón de agregar tutor
    function updateAddTutorButtonState() {
        const totalAreas = document.querySelectorAll('.area-block').length;
        const validAreas = Array.from(document.querySelectorAll('.area-select')).filter(select => select.value).length;
        
        // Si ya hay 2 áreas válidas, ocultar el botón de agregar tutor
        if (validAreas >= 2) {
            addTutorBtn.style.display = 'none';
        } else if (tutorCount < 2) {
            // Mostrar el botón solo si hay menos de 2 tutores
            addTutorBtn.style.display = 'block';
        }
    }

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
        
        // Actualizar los nombres de los campos de área y categoría para el nuevo tutor
        const tutorAreaBlocks = tutorBlock.querySelectorAll('.area-block');
        tutorAreaBlocks.forEach((areaBlock, areaIndex) => {
            const areaSelect = areaBlock.querySelector('.area-select');
            const categoriaSelect = areaBlock.querySelector('.categoria-select');
            if (areaSelect && categoriaSelect) {
                areaSelect.name = `tutor_areas_${tutorCount}_${areaIndex + 1}`;
                categoriaSelect.name = `tutor_categorias_${tutorCount}_${areaIndex + 1}`;
            }
        });
        
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
        
        // Verificar si ya hay áreas seleccionadas y obtener sus valores
        const selectedAreas = [];
        document.querySelectorAll('.area-select').forEach(select => {
            if (select.value) {
                selectedAreas.push(select.value);
            }
        });
        
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
        
        // Filtrar las opciones de área para eliminar las ya seleccionadas
        const areaSelect = newAreaBlock.querySelector('.area-select');
        const optionsToRemove = [];
        
        // Identificar las opciones que deben ser eliminadas (áreas ya seleccionadas)
        for (let i = 0; i < areaSelect.options.length; i++) {
            const option = areaSelect.options[i];
            if (option.value && selectedAreas.includes(option.value)) {
                optionsToRemove.push(i);
            }
        }
        
        // Eliminar las opciones de atrás hacia adelante para no afectar los índices
        for (let i = optionsToRemove.length - 1; i >= 0; i--) {
            areaSelect.remove(optionsToRemove[i]);
        }
        
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
        const categoriaSelect = newAreaBlock.querySelector('.categoria-select');
        
        areaSelect.name = `tutor_areas_${tutorIndex}_${areaCount[tutorIndex]}`;
        categoriaSelect.name = `tutor_categorias_${tutorIndex}_${areaCount[tutorIndex]}`;
        
        // Insertar el nuevo bloque antes del botón de agregar área
        areasContainer.insertBefore(newAreaBlock, areasContainer.querySelector('.btn-add-area'));
        
        // Si ya hay 2 áreas en total, ocultar todos los botones de agregar área
        if (document.querySelectorAll('.area-block').length >= 2) {
            document.querySelectorAll('.btn-add-area').forEach(btn => {
                btn.style.display = 'none';
            });
        }
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
        
        // Guardar el valor del área que se va a eliminar para actualizar los otros selectores
        const areaSelect = areaBlock.querySelector('.area-select');
        const areaValue = areaSelect.value;
        
        // Eliminar el bloque
        areaBlock.remove();
        
        // Mostrar todos los botones de agregar área si hay menos de 2 áreas en total
        if (document.querySelectorAll('.area-block').length < 2) {
            document.querySelectorAll('.btn-add-area').forEach(btn => {
                btn.style.display = 'block';
            });
        }
        
        // Actualizar el selector de grado común
        const categoriaSelects = document.querySelectorAll('.categoria-select');
        if (categoriaSelects.length > 0 && categoriaSelects[0].value) {
            loadGrados(categoriaSelects[0]);
        } else {
            // Si no hay categorías seleccionadas, deshabilitar el selector de grado
            const gradoSelectCommon = document.querySelector('.grado-select-common');
            gradoSelectCommon.innerHTML = '<option value="">Seleccione una categoría primero</option>';
            gradoSelectCommon.disabled = true;
        }
        
        // Si se eliminó un área con valor, actualizar los otros selectores para que muestren esa área
        if (areaValue) {
            document.querySelectorAll('.area-select').forEach(select => {
                // Verificar si ya existe la opción
                let optionExists = false;
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].value === areaValue) {
                        optionExists = true;
                        break;
                    }
                }
                
                // Si no existe, agregar la opción
                if (!optionExists) {
                    // Buscar el nombre del área en otro selector que tenga todas las opciones
                    const allAreasSelect = document.querySelector('.area-select');
                    let areaName = '';
                    for (let i = 0; i < allAreasSelect.options.length; i++) {
                        if (allAreasSelect.options[i].value === areaValue) {
                            areaName = allAreasSelect.options[i].text;
                            break;
                        }
                    }
                    
                    if (areaName) {
                        const newOption = new Option(areaName, areaValue);
                        select.add(newOption);
                    }
                }
            });
        }
    }

    // Función para mostrar el estado del token
    function showTokenStatus(tokenInput, isValid, message) {
        const tutorBlock = tokenInput.closest('.tutor-block');
        const statusElement = tutorBlock.querySelector('.token-status');
        
        statusElement.textContent = message;
        statusElement.className = 'token-status ' + (isValid ? 'valid' : 'invalid');
        
        if (!isValid) {
            tutorBlock.querySelector('.tutor-info').style.display = 'none';
        }
    }
    
    // Función para validar el token del tutor (simulada)
    function validateTutorToken(input) {
        const token = input.value.trim();
        const tutorBlock = input.closest('.tutor-block');
        const statusElement = tutorBlock.querySelector('.token-status');
        const verifyButton = tutorBlock.querySelector('.btn-verificar-token');
        
        if (!token) {
            showTokenStatus(input, false, 'Por favor, ingrese un token');
            return;
        }
        
        // Simular validación (en un caso real, esto sería una llamada al servidor)
        verifyButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
        verifyButton.disabled = true;
        
        // Simular retardo de red
        setTimeout(() => {
            // Simular respuesta exitosa (en un caso real, esto vendría del servidor)
            const isValidToken = token.length >= 6; // Simulación simple
            
            if (isValidToken) {
                statusElement.textContent = 'Token válido (simulado)';
                statusElement.classList.remove('invalid');
                statusElement.classList.add('valid');
                
                // Simular datos del tutor
                const tutorData = {
                    valid: true,
                    delegacion: 'Delegación Simulada',
                    idDelegacion: 1,
                    area: 'Área Simulada',
                    idArea: 1
                };
                
                displayTutorInfo(tutorBlock, tutorData);
            } else {
                statusElement.textContent = 'Token no válido (simulado)';
                statusElement.classList.remove('valid');
                statusElement.classList.add('invalid');
                tutorBlock.querySelector('.tutor-info').style.display = 'none';
            }
            
            verifyButton.innerHTML = '<i class="fas fa-check-circle"></i> Verificar';
            verifyButton.disabled = false;
        }, 1000);
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
    
    // Función para cargar las categorías según el área seleccionada (simulada)
    function loadCategorias(areaSelect) {
        const areaId = areaSelect.value;
        const tutorBlock = areaSelect.closest('.tutor-block');
        const areaBlock = areaSelect.closest('.area-block');
        const categoriaSelect = areaBlock.querySelector('.categoria-select');
        
        // Verificar si el área ya está seleccionada en otro bloque
        if (areaId) {
            const otherAreaSelects = document.querySelectorAll('.area-select');
            for (const otherSelect of otherAreaSelects) {
                if (otherSelect !== areaSelect && otherSelect.value === areaId) {
                    alert('Esta área ya ha sido seleccionada. Por favor, elija otra área.');
                    areaSelect.value = '';
                    categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
                    categoriaSelect.disabled = true;
                    return;
                }
            }
        }
        
        // Resetear y deshabilitar el selector de categorías si no hay área seleccionada
        if (!areaId) {
            categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
            categoriaSelect.disabled = true;
            return;
        }
        
        // Mostrar estado de carga
        categoriaSelect.innerHTML = '<option value="">Cargando categorías...</option>';
        categoriaSelect.disabled = true;
        
        // Simular retardo de red
        setTimeout(() => {
            // Simular datos de categorías (en un caso real, esto vendría del servidor)
            const categoriasSimuladas = [
                { idCategoria: 1, nombre: 'Categoría 1' },
                { idCategoria: 2, nombre: 'Categoría 2' }
            ];
            
            categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
            
            categoriasSimuladas.forEach(categoria => {
                categoriaSelect.innerHTML += `<option value="${categoria.idCategoria}">${categoria.nombre}</option>`;
            });
            
            categoriaSelect.disabled = false;
        }, 500);
    }

    // Función para cargar los grados según las categorías seleccionadas (simulada)
    function loadGrados(categoriaSelect) {
        const categoriaId = categoriaSelect.value;
        const tutorBlock = categoriaSelect.closest('.tutor-block');
        const areaBlock = categoriaSelect.closest('.area-block');
        const areaSelect = areaBlock.querySelector('.area-select');
        
        // Obtener el selector de grado común
        const gradoSelectCommon = document.querySelector('.grado-select-common');
        
        // Actualizar el estado del botón de agregar tutor
        updateAddTutorButtonState();
        
        // Recopilar todas las categorías seleccionadas
        const selectedCategorias = [];
        document.querySelectorAll('.categoria-select').forEach(select => {
            if (select.value) {
                selectedCategorias.push(select.value);
            }
        });
        
        // Si no hay categorías seleccionadas, deshabilitar el selector de grados
        if (selectedCategorias.length === 0) {
            gradoSelectCommon.innerHTML = '<option value="">Seleccione una categoría primero</option>';
            gradoSelectCommon.disabled = true;
            return;
        }
        
        // Mostrar estado de carga
        gradoSelectCommon.innerHTML = '<option value="">Cargando grados...</option>';
        gradoSelectCommon.disabled = true;
        
        // Simular retardo de red
        setTimeout(() => {
            // Simular datos de grados (en un caso real, esto vendría del servidor)
            const gradosSimulados = [
                { id: 1, nombre: 'Grado 1' },
                { id: 2, nombre: 'Grado 2' }
            ];
            
            gradoSelectCommon.innerHTML = '<option value="">Seleccione un grado</option>';
            
            gradosSimulados.forEach(grado => {
                gradoSelectCommon.innerHTML += `<option value="${grado.id}">${grado.nombre}</option>`;
            });
            
            // Habilitar el selector de grados común
            gradoSelectCommon.disabled = false;
        }, 500);
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
</script>
