<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        .upload-container {
            background-color: #f8fafc;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .instructions {
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 0 5px 5px 0;
        }
        .download-template {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #f0fdf4;
            border-radius: 5px;
            border: 1px dashed #22c55e;
        }
        .download-template i {
            font-size: 1.5rem;
            color: #22c55e;
            margin-right: 1rem;
        }
        .download-template a {
            color: #16a34a;
            text-decoration: none;
            font-weight: 600;
        }
        .download-template a:hover {
            text-decoration: underline;
        }
        .upload-form {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .upload-btn {
            background-color: #4f46e5;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .upload-btn:hover {
            background-color: #4338ca;
        }
        .error-row {
            background-color: #fee2e2 !important;
        }
        .modal-dialog {
            max-width: 90%;
        }
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        .editable {
            background-color: #f0f9ff;
            padding: 2px 5px;
            border-radius: 3px;
            border: 1px solid transparent;
            cursor: pointer;
        }
        .editable:hover {
            border-color: #93c5fd;
        }
        .editable:focus {
            outline: none;
            border-color: #3b82f6;
            background-color: white;
        }
        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold mb-4">Inscripción Masiva de Estudiantes</h2>
                    
                    <!-- Mensajes de error o éxito -->
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error_messages'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Se encontraron errores:</p>
                            <ul class="list-disc ml-5">
                                @foreach(session('error_messages') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="upload-container">
                        <div class="instructions">
                            <h3 class="text-lg font-medium mb-2">Instrucciones</h3>
                            <p>Para inscribir múltiples estudiantes, siga estos pasos:</p>
                            <ol class="list-decimal ml-5 mt-2">
                                <li>Descargue la plantilla Excel.</li>
                                <li>Complete todos los campos requeridos.</li>
                                <li>Para inscripciones en modalidad dúo o equipo, incluya el código de invitación.</li>
                                <li>Suba el archivo y revise la información antes de confirmar.</li>
                            </ol>
                        </div>

                        <div class="download-template">
                            <i class="fas fa-file-excel"></i>
                            <div>
                                <p class="mb-1">Descargue la plantilla con las columnas necesarias:</p>
                                <a href="{{ route('descargar.plantilla.excel') }}">
                                    <i class="fas fa-download"></i> Descargar Plantilla Excel
                                </a>
                            </div>
                        </div>

                        <div class="upload-form">
                            <form id="excelUploadForm" method="POST" action="{{ route('register.lista.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <x-label for="file" :value="__('Seleccione el archivo Excel')" />
                                    <x-input id="file" 
                                            type="file" 
                                            name="file" 
                                            accept=".xlsx,.xls" 
                                            class="mt-1 block w-full" 
                                            required />
                                </div>

                                <div class="flex justify-end">
                                    <button type="button" id="previewBtn" class="upload-btn">
                                        <i class="fas fa-eye mr-2"></i> Previsualizar Datos
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para previsualización -->
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function() {
            let excelData = [];
            let dataTable;

            // Previsualizar datos del Excel
            $('#previewBtn').click(function() {
                const fileInput = document.getElementById('file');
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
                        rowHtml += `<td>${index + 1}</td>`;

                        // Agregar celdas editables para cada columna
                        for (let i = 0; i < 15; i++) {
                            const value = row[i] || '';
                            rowHtml += `<td><div class="editable" contenteditable="true" data-col="${i}">${value}</div></td>`;
                        }

                        // Modalidad (nueva columna)
                        const modalidad = row[15] || '';
                        rowHtml += `<td><div class="editable" contenteditable="true" data-col="15">${modalidad}</div></td>`;

                        // Código de invitación (nueva columna)
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
            });

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
                    url: $('#excelUploadForm').attr('action'),
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
</x-app-layout>