@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<link rel="stylesheet" href="{{ asset('css/inscripcion/inscripcionTutor.css') }}">
@endpush

<x-app-layout>

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

        <!-- Bottom Section: Registration Form -->
        <div class="card form-card">
            <h2><i class="fas fa-user-plus"></i> Registro Manual de Estudiante</h2>
            <form class="registration-form" method="POST" action="{{ route('inscripcion.estudiante.manual.store') }}">
                @csrf
                <div class="form-grid">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3>Información Personal</h3>
                        <div class="input-group">
                            <label>Nombres</label>
                            <input type="text" name="nombres" required>
                        </div>
                        <div class="input-row">
                            <div class="input-group">
                                <label>Apellido Paterno</label>
                                <input type="text" name="apellidoPaterno" required>
                            </div>
                            <div class="input-group">
                                <label>Apellido Materno</label>
                                <input type="text" name="apellidoMaterno" required>
                            </div>

                        </div>
                        <div class="input-row">
                            <div class="input-group">
                                <label>CI</label>
                                <input type="text" name="ci" required>
                            </div>
                            <div class="input-group">
                                <label>Fecha de Nacimiento</label>
                                <input type="date" name="fechaNacimiento" required>
                            </div>
                            <div class="input-group">
                                <label>Género</label>
                                <select name="genero" required>
                                    <option value="">Seleccione un Género</option>
                                    <option value="">F</option>
                                    <option value="">M</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Nombre Completo Tutor</label>
                                <input type="text" name="nombreCompletoTutor" required>
                            </div>
                            <div class="input-group">
                                <label>Correo Tutor</label>
                                <input type="text" name="correoTutor" required>
                            </div>
                        </div>
                    </div>

                    <!-- Contact and Academic Information -->
                    <div class="form-section">
                        <h3>Información de Contacto</h3>
                        <div class="input-row">
                            <div class="input-group">
                                <label>Email</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="input-group">
                                <label>Teléfono</label>
                                <input type="tel" name="telefono" required>
                            </div>
                        </div>

                        <h3 class="mt-4">Información Académica</h3>
                        <div class="input-row">
                            <div class="input-group">
                                <label>Área</label>
                                <select name="area" id="areaSelect" required>
                                    <option value="">Seleccione un área</option>
                                    @foreach($areas as $area)
                                    <option value="{{ $area->idArea }}">{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Categoría</label>
                                <select name="categoria" id="categoriaSelect" required>
                                    <option value="">Seleccione una categoría</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Grado</label>
                                <select name="grado" id="gradoSelect" required>
                                    <option value="">Seleccione un grado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-button">
                        <i class="fas fa-save"></i> Registrar Estudiante
                    </button>
                </div>
            </form>
        </div>
        <!-- Modal -->
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
</x-app-layout>

<!-- Scripts necesarios para el modal y la previsualización -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('css/inscripcion/previsualizacion.css') }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<!-- Estilos para la animación de carga y mensajes -->
<style>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        color: white;
        display: none;
    }
    
    .spinner {
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 2s linear infinite;
        margin-bottom: 20px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .success-message {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        display: none;
    }
    
    .error-row {
        background-color: rgba(255, 0, 0, 0.1) !important;
    }
    
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>

<!-- Overlay de carga -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <h3>Procesando inscripciones...</h3>
    <p>Este proceso puede tardar unos momentos. Por favor, espere.</p>
</div>

<!-- Mensaje de éxito -->
<div class="success-message" id="successMessage">
    <i class="fas fa-check-circle"></i> <span id="successText"></span>
</div>

<script>
    function copyToken() {
        var tokenInput = document.getElementById('tokenInput');
        tokenInput.select();
        document.execCommand('copy');

        var copyButton = document.querySelector('.copy-button');
        var originalContent = copyButton.innerHTML;
        copyButton.innerHTML = '<i class="fas fa-check"></i>';

        setTimeout(function() {
            copyButton.innerHTML = originalContent;
        }, 2000);
    }
    
    document.getElementById('excelFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const fileInfo = document.getElementById('fileInfo');
        const selectedFileName = document.getElementById('selectedFileName');
        const uploadLabel = document.getElementById('fileName');

        if (file) {
            fileInfo.style.display = 'flex';
            selectedFileName.textContent = file.name;
            uploadLabel.textContent = 'Cambiar archivo';
        } else {
            fileInfo.style.display = 'none';
            uploadLabel.textContent = 'Seleccionar archivo';
        }
    });

    function removeFile() {
        const input = document.getElementById('excelFile');
        const fileInfo = document.getElementById('fileInfo');
        const uploadLabel = document.getElementById('fileName');

        input.value = '';
        fileInfo.style.display = 'none';
        uploadLabel.textContent = 'Seleccionar archivo';
    }

    function cargarDatosConvocatoria() {
        fetch('/verDatosCovocatoria')
            .then(response => response.text())
            .then(html => {
                document.getElementById('contenidoModal').innerHTML = html;
                document.getElementById('modalDatos').style.display = 'flex';
            })
            .catch(error => {
                console.error(error);
                document.getElementById('contenidoModal').innerHTML = '<p>Error al cargar los datos.</p>';
                document.getElementById('modalDatos').style.display = 'flex';
            });
    }

    function cerrarModal() {
        document.getElementById('modalDatos').style.display = 'none';
    }
    
    // Código para previsualizar datos del Excel
    $(document).ready(function() {
        let excelData = [];
        let dataTable;
        let errorCount = 0;

        // El botón Subir enviará el formulario directamente sin previsualización
        // No prevenimos el envío automático del formulario para que funcione normalmente
        
        // Agregar botón de previsualización si no existe
        if ($('#previewBtn').length === 0) {
            $('.upload-button').before('<button type="button" id="previewBtn" class="preview-button"><i class="fas fa-eye"></i> Previsualizar</button>');
        }
        
        // Eliminar cualquier evento previo y agregar el nuevo
        $(document).off('click', '#previewBtn');
        $(document).on('click', '#previewBtn', function() {
            previewExcelData();
        });
        
        // Asegurarse de que el modal se reinicie completamente al cerrarse
        $('#previewModal').on('hidden.bs.modal', function () {
            // Limpiar la tabla
            $('#previewTableBody').empty();
            
            // Destruir DataTable si existe
            if ($.fn.DataTable.isDataTable('#previewTable')) {
                $('#previewTable').DataTable().destroy();
            }
            
            // Ocultar contador de errores
            $('#errorCounter').hide();
        });


        function previewExcelData() {
            const fileInput = document.getElementById('excelFile');
            if (!fileInput.files.length) {
                alert('Por favor, seleccione un archivo Excel');
                return;
            }

            const file = fileInput.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                // Reiniciar variables
                excelData = [];
                errorCount = 0;
                
                // Limpiar tabla anterior
                $('#previewTableBody').empty();
                
                // Destruir DataTable si ya existe
                if ($.fn.DataTable.isDataTable('#previewTable')) {
                    $('#previewTable').DataTable().destroy();
                }
                
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {type: 'array'});
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const jsonData = XLSX.utils.sheet_to_json(firstSheet, {header: 1});

                // Eliminar la fila de encabezados
                const headers = jsonData.shift();
                excelData = jsonData;

                // Agregar contador de errores antes de la tabla
                if (!$('#errorCounter').length) {
                    $('.alert.alert-info').after(
                        '<div id="errorCounter" class="alert alert-warning mb-3" style="display: none;">' +
                        '<i class="fas fa-exclamation-triangle"></i> ' +
                        '<span id="errorCountText">Errores encontrados: 0 filas con errores.</span>' +
                        '</div>'
                    );
                }

                // Llenar tabla con datos
                jsonData.forEach((row, index) => {
                    let rowHtml = `<tr data-row="${index}">`;
                    rowHtml += `<td>${index + 1}</td>`; // Fila

                    // Crear un array con 16 elementos (columnas de datos) inicializados como vacíos
                    let rowData = Array(16).fill('');

                    // Copiar los datos existentes del Excel
                    for (let i = 0; i < Math.min(row.length, 16); i++) {
                        if (row[i] !== undefined) {
                            rowData[i] = row[i];
                        }
                    }

                    // Agregar celdas editables para cada columna de datos
                    for (let i = 0; i < 16; i++) {
                        const value = rowData[i] || '';
                        rowHtml += `<td><div class="editable" contenteditable="true" data-col="${i}">${value}</div></td>`;
                    }

                    // Agregar columna de estado de validación
                    rowHtml += `<td class="validation-status text-muted">Pendiente</td>`;

                    rowHtml += `</tr>`;
                    $('#previewTableBody').append(rowHtml);
                });

                // Destruir DataTable si ya existe
                if ($.fn.DataTable.isDataTable('#previewTable')) {
                    $('#previewTable').DataTable().clear().destroy();
                }
                
                // Inicializar DataTable
                dataTable = $('#previewTable').DataTable({
                    pageLength: 10,
                    language: {
                        "sProcessing":     "Procesando...",
                        "sLengthMenu":     "Mostrar _MENU_ registros",
                        "sZeroRecords":    "No se encontraron resultados",
                        "sEmptyTable":     "Ningún dato disponible en esta tabla",
                        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                        "sInfoPostFix":    "",
                        "sSearch":         "Buscar:",
                        "sUrl":            "",
                        "sInfoThousands":  ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst":    "Primero",
                            "sLast":     "Último",
                            "sNext":     "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        },
                        "buttons": {
                            "copy": "Copiar",
                            "colvis": "Visibilidad"
                        }
                    },
                    dom: '<"top"lf>rt<"bottom"ip><"clear">'
                });

                // Mostrar modal
                const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
                previewModal.show();

                // Validar datos
                validateExcelData();
            };

            reader.readAsArrayBuffer(file);
        }

        // Actualizar datos cuando se edita una celda
        $(document).on('blur', '.editable', function() {
            const row = $(this).closest('tr').data('row');
            const col = $(this).data('col');
            const value = $(this).text().trim();

            // Actualizar el valor en excelData
            if (!excelData[row]) {
                excelData[row] = [];
            }
            excelData[row][col] = value;

            // Revalidar la fila
            validateRow($(this).closest('tr'));

            // Actualizar contador de errores
            updateErrorCounter();
        });

        // Función para validar todos los datos
        function validateExcelData() {
            errorCount = 0;
            $('#previewTableBody tr').each(function() {
                if (!validateRow($(this))) {
                    errorCount++;
                }
            });

            // Actualizar contador de errores
            updateErrorCounter();
        }

        // Función para actualizar el contador de errores
        function updateErrorCounter() {
            if (errorCount > 0) {
                $('#errorCountText').text(`Errores encontrados: ${errorCount} ${errorCount === 1 ? 'fila con error' : 'filas con errores'}.`);
                $('#errorCounter').show();
            } else {
                $('#errorCounter').hide();
            }
        }

        // Función para validar una fila
        function validateRow(row) {
            const rowIndex = row.data('row');
            const rowData = excelData[rowIndex] || [];
            let isValid = true;
            let errorMessage = '';

            // Validar campos requeridos
            if (!rowData[0]) { // Nombre
                isValid = false;
                errorMessage = 'Falta el nombre';
            } else if (!rowData[4]) { // Email
                isValid = false;
                errorMessage = 'Falta el email';
            } else if (!rowData[7]) { // Área
                isValid = false;
                errorMessage = 'Falta el área';
            } else if (!rowData[8]) { // Categoría
                isValid = false;
                errorMessage = 'Falta la categoría';
            } else if (!rowData[9]) { // Grado
                isValid = false;
                errorMessage = 'Falta el grado';
            } else if (!rowData[11]) { // Delegación
                isValid = false;
                errorMessage = 'Falta la delegación';
            }

            // Validar modalidad y código de invitación
            if (rowData[15]) {
                const modalidad = rowData[15].toString().toLowerCase();
                if ((modalidad === 'duo' || modalidad === 'equipo') && !rowData[16]) {
                    isValid = false;
                    errorMessage = 'Falta el código de invitación para modalidad ' + rowData[15];
                }
            }
            
            // Validar que el área pertenezca al tutor actual
            if (isValid && rowData[7]) {
                const areaName = rowData[7].toString();
                // Verificar si el área está en la lista de áreas habilitadas para el tutor
                const areasHabilitadas = [];
                $('.areas-list li strong').each(function() {
                    areasHabilitadas.push($(this).text());
                });
                
                if (!areasHabilitadas.includes(areaName)) {
                    isValid = false;
                    errorMessage = `El área "${areaName}" no está habilitada para el tutor actual`;
                }
            }

            // Actualizar estado visual de la fila
            if (isValid) {
                row.removeClass('error-row');
                row.find('.validation-status').text('Válido').removeClass('text-danger text-muted').addClass('text-success');
            } else {
                row.addClass('error-row');
                row.find('.validation-status').text(errorMessage).removeClass('text-success text-muted').addClass('text-danger');
            }

            return isValid;
        }

        // Validar grupos de invitación
        function validateGroups() {
            const groups = {};
            let groupErrors = [];
            
            // Agrupar por código de invitación
            $('#previewTableBody tr').each(function() {
                const rowIndex = $(this).data('row');
                const rowData = excelData[rowIndex] || [];
                
                if (rowData[15] && rowData[16]) { // Si tiene modalidad y código
                    const modalidad = rowData[15].toString().toLowerCase();
                    const codigo = rowData[16].toString();
                    
                    if (!groups[codigo]) {
                        groups[codigo] = {
                            modalidad: modalidad,
                            area: rowData[7],
                            categoria: rowData[8],
                            miembros: []
                        };
                    }
                    
                    groups[codigo].miembros.push(rowIndex + 1); // +1 para mostrar número de fila real
                }
            });
            
            // Validar cada grupo
            for (const codigo in groups) {
                const grupo = groups[codigo];
                
                // Validar número de miembros según modalidad
                if (grupo.modalidad === 'duo' && grupo.miembros.length !== 2) {
                    groupErrors.push(`Código de invitación '${codigo}': La modalidad Dúo requiere exactamente 2 estudiantes (actualmente tiene ${grupo.miembros.length}).`);
                } else if (grupo.modalidad === 'equipo') {
                    if (grupo.miembros.length < 3) {
                        groupErrors.push(`Código de invitación '${codigo}': La modalidad Equipo requiere al menos 3 estudiantes (actualmente tiene ${grupo.miembros.length}).`);
                    } else if (grupo.miembros.length > 10) {
                        groupErrors.push(`Código de invitación '${codigo}': La modalidad Equipo permite máximo 10 estudiantes (actualmente tiene ${grupo.miembros.length}).`);
                    }
                }
            }
            
            return groupErrors;
        }
        
        // Mostrar errores de grupo en el modal
        function showGroupErrors(errors) {
            // Crear o actualizar el contenedor de errores de grupo
            if ($('#groupErrorContainer').length === 0) {
                $('.alert.alert-info').after(
                    '<div id="groupErrorContainer" class="alert alert-danger mb-3">' +
                    '<i class="fas fa-exclamation-triangle"></i> ' +
                    '<strong>Errores en grupos:</strong>' +
                    '<ul id="groupErrorList"></ul>' +
                    '</div>'
                );
            } else {
                $('#groupErrorList').empty();
            }
            
            // Mostrar u ocultar según corresponda
            if (errors.length > 0) {
                errors.forEach(error => {
                    $('#groupErrorList').append(`<li>${error}</li>`);
                });
                $('#groupErrorContainer').show();
                return true; // Hay errores
            } else {
                $('#groupErrorContainer').hide();
                return false; // No hay errores
            }
        }

        // Enviar datos al servidor
        $('#submitExcelData').click(function() {
            // Verificar si hay errores en filas individuales
            if (errorCount > 0) {
                alert(`Por favor, corrija los ${errorCount} errores antes de continuar.`);
                return;
            }
            
            // Validar grupos
            const groupErrors = validateGroups();
            if (showGroupErrors(groupErrors)) {
                return; // Si hay errores de grupo, no continuar
            }

            // Mostrar overlay de carga
            $('#loadingOverlay').fadeIn();

            // Crear un nuevo archivo Excel con los datos editados
            const wb = XLSX.utils.book_new();
            
            // Agregar encabezados
            const headers = [
                'Nombre', 'Apellido Paterno', 'Apellido Materno', 'CI', 'Email', 
                'Fecha Nacimiento', 'Género', 'Área', 'Categoría', 'Grado', 
                'Número Contacto', 'Delegación', 'Nombre Tutor', 'Email Tutor',
                'Modalidad', 'Código Invitación'
            ];
            
            const wsData = [headers, ...excelData.map(row => row.slice(0, 17))];
            const ws = XLSX.utils.aoa_to_sheet(wsData);
            XLSX.utils.book_append_sheet(wb, ws, 'Inscripciones');
            
            // Convertir a blob
            const wbout = XLSX.write(wb, {bookType:'xlsx', type:'array'});
            const blob = new Blob([wbout], {type:'application/octet-stream'});
            
            // Crear FormData y enviar
            const formData = new FormData();
            formData.append('file', new File([blob], 'inscripciones_editadas.xlsx', {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'}));
            formData.append('_token', $('input[name="_token"]').val());
            
            $.ajax({
                url: $('form.excel-actions').attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Ocultar overlay de carga
                    $('#loadingOverlay').fadeOut();
                    
                    // Cerrar modal
                    bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();
                    
                    // Mostrar mensaje de éxito
                    let successMessage = "La inscripción se ha completado con éxito.";
                    if (response && response.success) {
                        successMessage = response.success;
                    }
                    
                    $('#successText').text(successMessage);
                    $('#successMessage').fadeIn();
                    
                    // Recargar página después de mostrar el mensaje
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                },
                error: function(xhr) {
                    // Ocultar overlay de carga
                    $('#loadingOverlay').fadeOut();
                    
                    let errorMsg = 'Ocurrió un error al procesar la inscripción.';
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error_messages) {
                            errorMsg = xhr.responseJSON.error_messages.join('\n');
                        } else if (xhr.responseJSON.errors) {
                            errorMsg = 'Se encontraron los siguientes errores:\n';
                            for (const error in xhr.responseJSON.errors) {
                                errorMsg += xhr.responseJSON.errors[error].join('\n') + '\n';
                            }
                        } else if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                    }
                    
                    // Mostrar errores en un modal más amigable
                    const errorModal = `
                    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title"><i class="fas fa-exclamation-circle"></i> Error en la inscripción</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>${errorMsg.replace(/\n/g, '<br>')}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                    
                    // Agregar modal al DOM si no existe
                    if ($('#errorModal').length === 0) {
                        $('body').append(errorModal);
                    } else {
                        $('#errorModal .modal-body p').html(errorMsg.replace(/\n/g, '<br>'));
                    }
                    
                    // Mostrar modal de error
                    const errorModalInstance = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModalInstance.show();
                }
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const areaSelect = document.getElementById('areaSelect');
        const categoriaSelect = document.getElementById('categoriaSelect');
        const gradoSelect = document.getElementById('gradoSelect');

        const idConvocatoria = "{{ $idConvocatoriaResult ?? '' }}";

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

        // NUEVO: Cuando el usuario cambie una CATEGORÍA
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

    });
</script>