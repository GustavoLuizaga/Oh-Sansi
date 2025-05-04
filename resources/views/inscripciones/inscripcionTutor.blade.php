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
                
                <!-- Botón para gestionar grupos -->
                <div class="group-button-container" style="margin-top: 15px;">
                    <a href="{{ route('inscripcion.grupos') }}" class="group-button">
                        <i class="fas fa-users"></i> Gestionar Grupos
                    </a>
                </div>
                
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
                    <h5 class="modal-title" id="previewModalLabel">Previsualización de Datos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Revise los datos antes de confirmar. Puede hacer clic en las celdas para editar la información.
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
                                </tr>
                            </thead>
                            <tbody id="previewTableBody">
                                <!-- Los datos se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="submitExcelData" class="btn btn-primary">Confirmar Inscripción</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Scripts necesarios para el modal y la previsualización -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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

        // Modificar el formulario para prevenir el envío automático
        $('form.excel-actions').on('submit', function(e) {
            e.preventDefault();
            previewExcelData();
        });
        
        // Agregar botón de previsualización si no existe
        if ($('#previewBtn').length === 0) {
            $('.upload-button').before('<button type="button" id="previewBtn" class="preview-button" style="margin-right: 10px; background-color: #4f46e5; color: white; border: none; padding: 0.5rem 1rem; border-radius: 0.375rem; cursor: pointer;"><i class="fas fa-eye"></i> Previsualizar</button>');
        }
        
        // Evento para el botón de previsualización (usando off/on para evitar duplicación de eventos)
        $(document).off('click', '#previewBtn').on('click', '#previewBtn', function() {
            previewExcelData();
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
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {type: 'array'});
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const jsonData = XLSX.utils.sheet_to_json(firstSheet, {header: 1});

                // Eliminar la fila de encabezados
                const headers = jsonData.shift();
                excelData = jsonData;

                // Limpiar tabla anterior
                $('#previewTableBody').empty();

                // Llenar tabla con datos
                jsonData.forEach((row, index) => {
                    let rowHtml = `<tr data-row="${index}">`;
                    rowHtml += `<td>${index + 1}</td>`; // Fila

                    // Agregar celdas editables para las primeras 15 columnas
                    for (let i = 0; i < 15; i++) {
                        const value = row[i] || '';
                        rowHtml += `<td><div class="editable" contenteditable="true" data-col="${i}">${value}</div></td>`;
                    }

                    // Modalidad (columna 15)
                    const modalidad = row[15] || '';
                    rowHtml += `<td><div class="editable" contenteditable="true" data-col="15">${modalidad}</div></td>`;

                    // Código de invitación (columna 16)
                    const codigoInvitacion = row[16] || '';
                    rowHtml += `<td><div class="editable" contenteditable="true" data-col="16">${codigoInvitacion}</div></td>`;

                    rowHtml += `</tr>`;
                    $('#previewTableBody').append(rowHtml);
                });

                // Inicializar DataTable
                if (dataTable) {
                    dataTable.destroy();
                }
                
                dataTable = $('#previewTable').DataTable({
                    pageLength: 10,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                    }
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
        });

        // Función para validar todos los datos
        function validateExcelData() {
            $('#previewTableBody tr').each(function() {
                validateRow($(this));
            });
        }

        // Función para validar una fila
        function validateRow(row) {
            const rowIndex = row.data('row');
            const rowData = excelData[rowIndex];
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
            if (rowData[15] && (rowData[15].toLowerCase() === 'duo' || rowData[15].toLowerCase() === 'equipo')) {
                if (!rowData[16]) {
                    isValid = false;
                    errorMessage = 'Falta el código de invitación para modalidad ' + rowData[15];
                }
            }

            // Actualizar estado visual de la fila
            if (isValid) {
                row.removeClass('error-row');
                row.find('.validation-status').text('Válido').removeClass('text-danger').addClass('text-success');
            } else {
                row.addClass('error-row');
                row.find('.validation-status').text(errorMessage).removeClass('text-success').addClass('text-danger');
            }

            return isValid;
        }

        // Enviar datos al servidor
        $('#submitExcelData').click(function() {
            // Verificar si hay errores
            let hasErrors = false;
            $('#previewTableBody tr').each(function() {
                if ($(this).hasClass('error-row')) {
                    hasErrors = true;
                    return false; // Salir del bucle
                }
            });

            if (hasErrors) {
                alert('Por favor, corrija los errores antes de continuar.');
                return;
            }

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
                    // Cerrar modal
                    bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();
                    
                    // Recargar página con mensaje de éxito
                    location.reload();
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errorMsg = 'Se encontraron errores:\n';
                        for (const error in xhr.responseJSON.errors) {
                            errorMsg += xhr.responseJSON.errors[error].join('\n') + '\n';
                        }
                        alert(errorMsg);
                    } else {
                        alert('Ocurrió un error al procesar la inscripción.');
                    }
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