@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<link rel="stylesheet" href="{{ asset('css/inscripcion/inscripcionTutor.css') }}">
<link rel="stylesheet" href="{{ asset('css/inscripcion/inscripcionManual.css') }}">
<!-- Scripts necesarios para el modal y la previsualización -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('css/inscripcion/previsualizacion.css') }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    // Verificar que jQuery y DataTables estén disponibles
    window.addEventListener('DOMContentLoaded', function() {
        if (typeof $ === 'undefined') {
            console.error('jQuery no está disponible');
        } else {
            console.log('jQuery está disponible');
            if (typeof $.fn.DataTable === 'undefined') {
                console.error('DataTables no está disponible');
            } else {
                console.log('DataTables está disponible');
            }
        }
    });
</script>

@endpush

<x-app-layout>

    <body data-convocatoria-id="{{ $idConvocatoriaResult ?? '' }}">

        <!-- Change this part -->
        <div class="tutor-container">
            <!-- Top Section: Token and Excel Upload -->
            <div class="top-section">
                <!-- Token Card -->
                <div class="card token-card">
                    <h2><i class="fas fa-key"></i> Token de Inscripción</h2>
                    <div class="token-display">
                        <input type="text"
                            id="tokenInput"
                            value="{{ $token ?? 'No hay token disponible' }}"
                            readonly>
                        <button onclick="copyToken()" class="copy-button" {{ !$token ? 'disabled' : '' }}>
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <!-- Moved group button here -->
                    <div class="group-button-container">
                        <a href="{{ route('inscripcion.grupos') }}" class="group-button">
                            <i class="fas fa-users"></i> Gestionar Grupos
                        </a>
                    </div>
                    @if($token)
                    <div class="token-info">
                        <h3><i class="fas fa-info-circle"></i> Información del Tutor</h3>
                        <div class="areas-list">
                            <h4>Áreas habilitadas:</h4>
                            <ul>
                                @foreach($areas as $area)
                                <li>
                                    <strong>{{ $area->nombre }}</strong>
                                    <ul class="categorias-list">
                                        @php
                                        $categorias = \App\Models\ConvocatoriaAreaCategoria::where('idArea', $area->idArea)
                                        ->where('idConvocatoria', $idConvocatoriaResult ?? null)
                                        ->join('categoria', 'convocatoriaAreaCategoria.idCategoria', '=', 'categoria.idCategoria')
                                        ->select('categoria.*')
                                        ->distinct()
                                        ->get();
                                        @endphp
                                        @foreach($categorias as $categoria)
                                        <li>{{ $categoria->nombre }}</li>
                                        @endforeach
                                    </ul>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Excel Upload Card -->
                <div class="card excel-card">
                    <h2><i class="fas fa-file-excel"></i> Inscripción Masiva</h2>

                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error_messages'))
                    <div class="alert alert-danger">
                        <p>{{ session('message') }}</p>
                        <ul>
                            @foreach(session('error_messages') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('register.lista.store') }}" enctype="multipart/form-data" class="excel-actions">
                        @csrf
                        <input type="file"
                            id="excelFile"
                            name="file"
                            accept=".xlsx, .xls"
                            class="file-input"
                            required>
                        <label for="excelFile" class="upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span id="fileName">Seleccionar archivo</span>
                        </label>
                        <div id="fileInfo" class="file-info" style="display: none;">
                            <i class="fas fa-file-excel"></i>
                            <span id="selectedFileName"></span>
                            <button type="button" class="remove-file" onclick="removeFile()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button type="submit" class="upload-button">
                            <i class="fas fa-upload"></i> Subir
                        </button>
                        <button type="button" id="previewBtn" class="preview-button">
                            <i class="fas fa-eye"></i> Previsualizar
                        </button>
                        <a href="{{ asset('plantillasExel/plantilla_inscripcion.xlsx') }}" class="template-link">
                            <i class="fas fa-download"></i> Descargar plantilla
                        </a>
                        <button onclick="cargarDatosConvocatoria()">
                            Ver información sobre la convocatoria
                        </button>

                    </form>

                    @if ($errors->any())
                    <div class="alert alert-danger mt-3">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            
            
            <div class="manual-registration-section">
                @include('inscripciones/formInscripcionEst')
            </div>
            
            <!-- Modal and other content -->
            <div id="modalDatos" class="modal">
                <div class="modal-contenido">
                    <button onclick="cerrarModal()" class="modal-cerrar">✖</button>
                    <div id="contenidoModal" class="modal-cuerpo">
                        Cargando datos...
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal para previsualización de datos Excel -->
        <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previewModalLabel">
                            <i class="fas fa-table"></i> Previsualización de Datos
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Revise los datos antes de confirmar. Puede hacer clic en las celdas para editar la información.
                        </div>
                        <div class="alert alert-warning mb-3" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span id="errorCountText">Errores encontrados: 0 filas con errores.</span>
                        </div>
                        <div class="table-responsive">
                            <table id="previewTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fila</th>
                                        <th>Nombre</th>
                                        <th>Apellido Paterno</th>
                                        <th>Apellido Materno</th>
                                        <th>CI</th>
                                        <th>Email</th>
                                        <th>Fecha Nacimiento</th>
                                        <th>Género</th>
                                        <th>Área</th>
                                        <th>Categoría</th>
                                        <th>Grado</th>
                                        <th>Número Contacto</th>
                                        <th>Delegación</th>
                                        <th>Nombre Tutor</th>
                                        <th>Email Tutor</th>
                                        <th>Modalidad</th>
                                        <th>Código Invitación</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="previewTableBody">
                                    <!-- Los datos se cargarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" id="submitExcelData" class="btn btn-primary">
                            <i class="fas fa-check"></i> Confirmar Inscripción
                        </button>
                    </div>
                </div>
            </div>
        </div>
@push('scripts')
<script src="{{ asset('js/inscripcionTutor/inscripcionExcel.js') }}"></script>
<script>
    // Asegurarse de que el botón de previsualización funcione correctamente
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM completamente cargado');
        const previewBtn = document.getElementById('previewBtn');
        if (previewBtn) {
            console.log('Botón de previsualización encontrado');
            previewBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Botón de previsualización clickeado');
                // Si estamos usando jQuery
                if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
                    $('#previewModal').modal('show');
                } else if (typeof bootstrap !== 'undefined') {
                    // Si estamos usando Bootstrap nativo
                    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
                    previewModal.show();
                } else {
                    console.error('No se encontró Bootstrap ni jQuery');
                }
            });
        } else {
            console.error('Botón de previsualización no encontrado');
        }
    });
</script>
@endpush
</x-app-layout>

<!-- Overlay de carga -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <h3>Procesando inscripciones...</h3>
    <p>Este proceso puede tardar unos momentos. Por favor, espere mientras guardamos la información.</p>
    <p><small>No cierre esta ventana hasta que el proceso termine</small></p>
</div>

<!-- Mensaje de éxito -->
<div class="success-message" id="successMessage">
    <i class="fas fa-check-circle"></i>
    <h4>¡Operación Exitosa!</h4>
    <span id="successText"></span>
</div>
<script src="{{ asset('js/inscripcionTutor/inscripcionManual.js') }}"></script>