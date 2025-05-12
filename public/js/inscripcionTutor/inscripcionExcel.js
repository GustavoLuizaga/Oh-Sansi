
function copyToken() {
    var tokenInput = document.getElementById('tokenInput');
    tokenInput.select();
    document.execCommand('copy');

    var copyButton = document.querySelector('.copy-button');
    var originalContent = copyButton.innerHTML;
    copyButton.innerHTML = '<i class="fas fa-check"></i>';

    setTimeout(function () {
        copyButton.innerHTML = originalContent;
    }, 2000);
}

document.getElementById('excelFile').addEventListener('change', function (e) {
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
$(document).ready(function () {
    let excelData = [];
    let dataTable;
    let errorCount = 0;
    
    // Verificar que DataTables esté disponible
    if (!$.fn.DataTable) {
        console.error('DataTables no está disponible. Asegúrese de que la biblioteca esté cargada correctamente.');
    } else {
        console.log('DataTables está disponible correctamente');
    }

    // El botón Subir enviará el formulario directamente sin previsualización
    // No prevenimos el envío automático del formulario para que funcione normalmente

    // Asegurarse de que el evento click esté correctamente asignado al botón de previsualización
    $(document).on('click', '#previewBtn', function(e) {
        e.preventDefault();
        console.log('Botón de previsualización clickeado');
        previewExcelData();
    });


    // Asegurarse de que el modal se reinicie completamente al cerrarse
    $('#previewModal').on('hidden.bs.modal', function () {
        // Limpiar la tabla
        $('#previewTableBody').empty();

        // Destruir DataTable si existe
        try {
            if ($ && $.fn && $.fn.DataTable && typeof $.fn.DataTable.isDataTable === 'function' && $.fn.DataTable.isDataTable('#previewTable')) {
                $('#previewTable').DataTable().destroy();
            }
        } catch (error) {
            console.error('Error al verificar o destruir DataTable:', error);
        }

        // Ocultar contador de errores
        $('#errorCounter').hide();
    });


    function previewExcelData() {
        console.log('Función previewExcelData ejecutada');
        const fileInput = document.getElementById('excelFile');
        if (!fileInput.files.length) {
            alert('Por favor, seleccione un archivo Excel');
            return;
        }

        const file = fileInput.files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            console.log('Archivo Excel cargado correctamente');
            // Reiniciar variables
            excelData = [];
            errorCount = 0;

            // Limpiar tabla anterior
            $('#previewTableBody').empty();

            // Destruir DataTable si ya existe
            try {
                if ($.fn && $.fn.DataTable && typeof $.fn.DataTable.isDataTable === 'function' && $.fn.DataTable.isDataTable('#previewTable')) {
                    $('#previewTable').DataTable().destroy();
                }
            } catch (error) {
                console.error('Error al verificar o destruir DataTable:', error);
            }

            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
            const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });

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
            try {
                if ($ && $.fn && $.fn.DataTable && typeof $.fn.DataTable.isDataTable === 'function' && $.fn.DataTable.isDataTable('#previewTable')) {
                    $('#previewTable').DataTable().clear().destroy();
                }
            } catch (error) {
                console.error('Error al verificar o destruir DataTable:', error);
            }

            // Inicializar DataTable
            dataTable = $('#previewTable').DataTable({
                pageLength: 10,
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    },
                    "buttons": {
                        "copy": "Copiar",
                        "colvis": "Visibilidad"
                    }
                },
                dom: '<"top"lf>rt<"bottom"ip><"clear">'
            });

            try {
                // Mostrar modal usando jQuery para evitar problemas con Bootstrap
                $('#previewModal').modal('show');
                console.log('Modal mostrado con jQuery');
            } catch (error) {
                console.error('Error al mostrar modal con jQuery:', error);
                
                try {
                    // Intentar con Bootstrap como respaldo
                    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
                    previewModal.show();
                    console.log('Modal mostrado con Bootstrap');
                } catch (bootstrapError) {
                    console.error('Error al mostrar modal con Bootstrap:', bootstrapError);
                    alert('Hubo un problema al mostrar la previsualización. Por favor, intente de nuevo.');
                }
            }

            // Validar datos
            validateExcelData();
        };

        reader.readAsArrayBuffer(file);
    }

    // Actualizar datos cuando se edita una celda
    $(document).on('blur', '.editable', function () {
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
        $('#previewTableBody tr').each(function () {
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
            $('.areas-list li strong').each(function () {
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
        $('#previewTableBody tr').each(function () {
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
    $('#submitExcelData').click(function () {
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
        const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
        const blob = new Blob([wbout], { type: 'application/octet-stream' });

        // Crear FormData y enviar
        const formData = new FormData();
        formData.append('file', new File([blob], 'inscripciones_editadas.xlsx', { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }));
        formData.append('_token', $('input[name="_token"]').val());

        $.ajax({
            url: $('form.excel-actions').attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                // Ocultar overlay de carga con una transición suave
                $('#loadingOverlay').fadeOut(300);

                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();

                // Mostrar mensaje de éxito
                let successMessage = "La inscripción se ha completado con éxito.";
                if (response && response.success) {
                    successMessage = response.success;
                }

                $('#successText').text(successMessage);
                $('#successMessage').fadeIn(500);

                // Recargar página después de mostrar el mensaje (tiempo aumentado para mejor visibilidad)
                setTimeout(function () {
                    $('#successMessage').fadeOut(500, function () {
                        location.reload();
                    });
                }, 4000);
            },
            error: function (xhr) {
                // Ocultar overlay de carga con una transición suave
                $('#loadingOverlay').fadeOut(300);

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
                        <div class="modal-dialog modal-dialog-centered">
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