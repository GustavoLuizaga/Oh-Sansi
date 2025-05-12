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

<div class="container mt-4">
    <h2>Datos del Estudiante</h2>
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $estudiante['nombre'] }} {{ $estudiante['apellido_paterno'] }} {{ $estudiante['apellido_materno'] }}</p>
            <p><strong>CI:</strong> {{ $estudiante['ci'] }}</p>
            <p><strong>Email:</strong> {{ $estudiante['email'] }}</p>
            <p><strong>Grado:</strong> {{ $estudiante['grado'] }}</p>
            <p><strong>Fecha Nacimiento:</strong> {{ $estudiante['fecha_nacimiento'] }}</p>
            <p><strong>Género:</strong> {{ $estudiante['genero'] }}</p>
        </div>
    </div>

    <h2>Datos del Tutor</h2>
    @foreach ($tutores as $tutor)
        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $tutor['nombre'] }} {{ $tutor['apellido_paterno'] }} {{ $tutor['apellido_materno'] }}</p>
                <p><strong>Token:</strong> {{ $tutor['token'] }}</p>
                <p><strong>CI:</strong> {{ $tutor['ci'] }}</p>
                <p><strong>Profesión:</strong> {{ $tutor['profesion'] }}</p>
                <p><strong>Teléfono:</strong> {{ $tutor['telefono'] }}</p>
                <p><strong>Email:</strong> {{ $tutor['email'] }}</p>
                <h5>Colegio</h5>
                <p><strong>Nombre:</strong> {{ $tutor['colegio']['nombre'] }}</p>
                <p><strong>Dependencia:</strong> {{ $tutor['colegio']['dependencia'] }}</p>
                <p><strong>Dirección:</strong> {{ $tutor['colegio']['direccion'] }}</p>
                <p><strong>Teléfono:</strong> {{ $tutor['colegio']['telefono'] }}</p>
                
                <h5>Áreas en esta inscripción</h5>
                <ul>
                    @foreach ($tutor['areas'] as $area)
                        <li>
                            {{ $area['nombre'] }} ({{ $area['categoria'] }}) - Registrado el: {{ $area['fecha_registro'] }}
                        </li>
                    @endforeach
                </ul>
                
                <h5>Todas las áreas del tutor</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre del Área</th>
                                        <th>Categorías del Área</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tutor['todas_areas'] as $area)
                                        <tr>
                                            <td>{{ $area['id'] }}</td>
                                            <td>{{ $area['nombre'] }}</td>
                                            <td>
                                                @if (!empty($area['categorias']))
                                                    <ul class="list-unstyled">
                                                        @foreach ($area['categorias'] as $categoria)
                                                            <li>
                                                                {{ $categoria['nombre_categoria'] }} 
                                                                (Precios: Individual: {{ $categoria['precios']['individual'] }} Bs, 
                                                                Duo: {{ $categoria['precios']['duo'] }} Bs, 
                                                                Equipo: {{ $categoria['precios']['equipo'] }} Bs)
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">Sin categorías</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No hay áreas adicionales para este tutor</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

        <h2>Datos de Convocatoria</h2>
        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $convocatoria['nombre'] }}</p>
                <p><strong>Fecha Límite:</strong> {{ $convocatoria['fecha_limite'] }}</p>
                <p><strong>Método de Pago:</strong> {{ $convocatoria['metodo_pago'] }}</p>
                <p><strong>Contacto:</strong> {{ $convocatoria['contacto'] }}</p>
            </div>
        </div>
        <h2>Datos de Inscripción</h2>
        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Fecha:</strong> {{ $inscripcion['fecha'] }}</p>
                <p><strong>Contacto:</strong> {{ $inscripcion['numero_contacto'] }}</p>
                <p><strong>Status:</strong> {{ $inscripcion['status'] }}</p>
                <p><strong>Nombre Apellidos Tutor:</strong> {{ $inscripcion['nombre_apellidos_tutor'] }}</p>
                <p><strong>Correo Tutor:</strong> {{ $inscripcion['correo_tutor'] }}</p>
            </div>
        </div>
        <h2>Inscripciones por Área</h2>
        <div class="card mb-3">
            <div class="card-body">
                <ul>
                    @foreach ($inscripciones as $ins)
                        <li>
                            {{ $ins['area'] }} ({{ $ins['categoria'] }}) - Precio: {{ $ins['precio'] }} Bs - Registrado: {{ $ins['fecha_registro'] }}
                        </li>
                    @endforeach
                </ul>
                <p><strong>Total a Pagar:</strong> {{ $totalPagar }} Bs</p>
            </div>
        </div>
        <h2>Boleta de Pago</h2>
        <div class="card mb-3">
            <div class="card-body">
                <p><strong>Código Orden:</strong> {{ $codigoOrden }}</p>
                <p><strong>Fecha Generación:</strong> {{ $fechaGeneracion }}</p>
                <p><strong>Fecha Vencimiento:</strong> {{ $fechaVencimiento }}</p>
            </div>
        </div>
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
                                        value="{{ $inscripcion['numero_contacto'] }}" 
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
                            <h2><i class="fas fa-chalkboard-teacher"></i> Información de Tutores y las áreas y categorías a las que te inscribiste con ese Tutor</h2>
                            <p class="section-subtitle">Puede agregar hasta 2 tutores</p>
                        </div>
                        <div class="seccion-body">
                            <div id="tutorContainer">
                                @foreach ($tutores as $index => $tutor)
                                <div class="tutor-block">
                                    <div class="tutor-header">
                                        <h3>Tutor {{ $index + 1 }}</h3>
                                    </div>
                                    <div class="input-grupo">
                                        <label>Token del Tutor</label>
                                        <div class="input-with-icon token-verification-container">
                                            <input 
                                                type="text" 
                                                class="tutor-token" 
                                                name="tutor_tokens[]"
                                                value="{{ $tutor['token'] }}" 
                                                placeholder="Token del tutor" 
                                                readonly <!-- Si el token no debe editarse -->
                                            >
                                            <button type="button" class="btn-verificar-token" style="display: none;">
                                                <i class="fas fa-check-circle"></i> Verificar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="tutor-info">
                                        <div class="info-row">
                                            <div class="info-group">
                                                <label>Delegación</label>
                                                <div class="info-value tutor-delegacion">
                                                    {{ $tutor['colegio']['nombre'] }}
                                                </div>
                                                <input 
                                                    type="hidden" 
                                                    class="idDelegacion-input" 
                                                    name="tutor_delegaciones[]"
                                                    value="{{ $tutor['colegio']['id'] }}"
                                                >
                                            </div>
                                        </div>
                                        
                                        <!-- Áreas y Categorías -->
                                        <div class="areas-container">
                                            @foreach ($tutor['areas'] as $area)
                                            <div class="area-block">
                                                <div class="info-row">
                                                    <div class="info-group">
                                                        <label>Área</label>
                                                        <select 
                                                            class="area-select" 
                                                            name="tutor_areas_{{ $index + 1 }}[]" 
                                                            required
                                                        >
                                                            <option value="">Seleccione un área</option>
                                                            @foreach ($tutor['todas_areas'] as $todasArea)
                                                                <option 
                                                                    value="{{ $todasArea['id'] }}" 
                                                                    {{ $todasArea['id'] == $area['id'] ? 'selected' : '' }}
                                                                >
                                                                    {{ $todasArea['nombre'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="input-grupo">
                                                        <label>Categoría</label>
                                                        <select 
                                                            class="categoria-select" 
                                                            name="tutor_categorias_{{ $index + 1 }}[]" 
                                                            required
                                                        >
                                                            <option value="">Seleccione una categoría</option>
                                                            <!-- Iterar sobre las categorías de la área actual -->
                                                            @foreach ($tutor['todas_areas'] as $todasArea)
                                                                @if ($todasArea['id'] == $area['id'])
                                                                    @foreach ($todasArea['categorias'] as $categoria)
                                                                        <option 
                                                                            value="{{ $categoria['id_categoria'] }}" 
                                                                            {{ $categoria['id_categoria'] == $area['categoria_id'] ? 'selected' : '' }}
                                                                        >
                                                                            {{ $categoria['nombre_categoria'] }}
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Botón para agregar otro tutor (solo si hay menos de 2) -->
                            @if(count($tutores) < 2)
                            <button type="button" id="addTutorBtn" class="btn-add-tutor">
                                <i class="fas fa-plus"></i> Agregar otro tutor
                            </button>
                            @endif
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
                            <select id="idGrado" name="idGrado" class="grado-select-common" required>
    <option value="">Seleccione un grado</option>
    <option value="1ro de Primaria" {{ $estudiante['grado'] == '1ro de Primaria' ? 'selected' : '' }}>1ro de Primaria</option>
    <option value="2do de Primaria" {{ $estudiante['grado'] == '2do de Primaria' ? 'selected' : '' }}>2do de Primaria</option>
    <option value="3ro de Primaria" {{ $estudiante['grado'] == '3ro de Primaria' ? 'selected' : '' }}>3ro de Primaria</option>
    <option value="4to de Primaria" {{ $estudiante['grado'] == '4to de Primaria' ? 'selected' : '' }}>4to de Primaria</option>
    <option value="5to de Primaria" {{ $estudiante['grado'] == '5to de Primaria' ? 'selected' : '' }}>5to de Primaria</option>
    <option value="6to de Primaria" {{ $estudiante['grado'] == '6to de Primaria' ? 'selected' : '' }}>6to de Primaria</option>
    <option value="1ro de Secundaria" {{ $estudiante['grado'] == '1ro de Secundaria' ? 'selected' : '' }}>1ro de Secundaria</option>
    <option value="2do de Secundaria" {{ $estudiante['grado'] == '2do de Secundaria' ? 'selected' : '' }}>2do de Secundaria</option>
    <option value="3ro de Secundaria" {{ $estudiante['grado'] == '3ro de Secundaria' ? 'selected' : '' }}>3ro de Secundaria</option>
    <option value="4to de Secundaria" {{ $estudiante['grado'] == '4to de Secundaria' ? 'selected' : '' }}>4to de Secundaria</option>
    <option value="5to de Secundaria" {{ $estudiante['grado'] == '5to de Secundaria' ? 'selected' : '' }}>5to de Secundaria</option>
    <option value="6to de Secundaria" {{ $estudiante['grado'] == '6to de Secundaria' ? 'selected' : '' }}>6to de Secundaria</option>
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
</script>
