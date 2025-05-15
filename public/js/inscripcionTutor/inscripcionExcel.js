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
            }            // Llenar tabla con datos
            jsonData.forEach((row, index) => {
                let rowHtml = `<tr data-row="${index}">`;
                
                // Columna de acciones con botón eliminar
                rowHtml += `<td>
                    <button type="button" class="btn btn-sm btn-danger delete-row-btn" title="Eliminar fila">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>`;
                
                rowHtml += `<td>${index + 1}</td>`; // Número de fila
                
                // Crear un array con 16 elementos (columnas de datos) inicializados como vacíos
                let rowData = Array(16).fill('');

                // Copiar los datos existentes del Excel
                for (let i = 0; i < Math.min(row.length, 16); i++) {
                    if (row[i] !== undefined) {
                        rowData[i] = row[i];
                    }
                }                // Agregar celdas editables para cada columna de datos
                // Definir las columnas que queremos mostrar con sus índices correctos, omitiendo la posición 11 (Delegación)
                const columnMapping = [
                    { index: 0, name: 'Nombre' },
                    { index: 1, name: 'Apellido Paterno' },
                    { index: 2, name: 'Apellido Materno' },
                    { index: 3, name: 'CI' },
                    { index: 4, name: 'Email' },
                    { index: 5, name: 'Fecha Nacimiento' },
                    { index: 6, name: 'Género' },
                    { index: 7, name: 'Área' },
                    { index: 8, name: 'Categoría' },
                    { index: 9, name: 'Grado' },
                    { index: 10, name: 'Número Contacto' },
                    { index: 12, name: 'Nombre Tutor' },
                    { index: 13, name: 'Email Tutor' },
                    { index: 14, name: 'Modalidad' },
                    { index: 15, name: 'Código Invitación' }
                ];
                
                for (let colIndex = 0; colIndex < columnMapping.length; colIndex++) {
                    const { index, name } = columnMapping[colIndex];
                    const value = rowData[index] || '';
                    // Sanitizar el valor para evitar problemas de HTML
                    const sanitizedValue = String(value)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;');
                    
                    rowHtml += `<td><div class="editable" contenteditable="true" data-col="${index}" title="${name}">${sanitizedValue}</div></td>`;
                }

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
            }            // Inicializar DataTable con opciones mínimas para evitar problemas de columnas
            dataTable = $('#previewTable').DataTable({
                pageLength: 10,
                searching: false, // Desactivamos búsqueda nativa y usamos nuestro propio buscador
                ordering: true,
                paging: true,
                info: true,
                autoWidth: false,
                scrollX: true, // Habilitar desplazamiento horizontal
                scrollY: '60vh', // Altura fija para el desplazamiento vertical
                scrollCollapse: true,
                fixedHeader: false,
                fixedColumns: false,
                stateSave: false,
                responsive: false, // Desactivamos responsive para evitar problemas
                // Configuración DOM mínima
                dom: 'lrtip',
                columnDefs: [
                    { width: "60px", targets: 0 }, // Columna de acciones
                    { width: "50px", targets: 1 }, // Número de fila
                    { width: "150px", targets: [2, 3, 4, 13] }, // Nombres y apellidos
                    { width: "120px", targets: [5, 12] }, // CI y números
                    { width: "180px", targets: [6, 14] }, // Emails
                    { width: "120px", targets: [7, 8, 9, 10, 11, 15, 16] } // Otras columnas
                ],
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
                }
            });            try {
                // Mostrar modal usando jQuery para evitar problemas con Bootstrap
                $('#previewModal').modal('show');
                console.log('Modal mostrado con jQuery');
                
                // Agregar un código para ejecutarse después de mostrar el modal
                $('#previewModal').on('shown.bs.modal', function() {
                    // Forzar reajuste de la tabla después de que el modal esté visible
                    setTimeout(function() {
                        // Verificar y corregir cualquier columna fantasma
                        $('#previewTable tr').each(function() {
                            const numHeaderCells = $('#previewTable thead tr:first th').length;
                            const rowCells = $(this).find('td, th').length;
                            
                            if (rowCells > numHeaderCells) {
                                // Eliminar celdas extras
                                console.log(`Encontrada fila con ${rowCells} celdas, eliminando las extras`);
                                $(this).find('td, th').slice(numHeaderCells).remove();
                            }
                        });
                        
                        // Recalcular anchos de columna
                        if ($.fn.dataTable.isDataTable('#previewTable')) {
                            $('#previewTable').DataTable().columns.adjust();
                        }
                    }, 300);
                });
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
        let errorMessage = '';        // Validar campos requeridos
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
        }        // Validar modalidad y código de invitación (ajustando índices por la eliminación de Delegación)
        // Modalidad ahora está en índice 14 (era 15 antes) y código de invitación en 15 (era 16 antes)
        const modalidadIndex = 14;
        const codigoIndex = 15;
        
        if (rowData[modalidadIndex]) {
            const modalidad = rowData[modalidadIndex].toString().toLowerCase();
            if ((modalidad === 'duo' || modalidad === 'equipo') && !rowData[codigoIndex]) {
                isValid = false;
                errorMessage = 'Falta el código de invitación para modalidad ' + rowData[modalidadIndex];
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
        }        // Actualizar estado visual de la fila
        if (isValid) {
            row.removeClass('error-row').attr('title', 'Fila válida');
        } else {
            row.addClass('error-row').attr('title', errorMessage);
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

        // Cerrar el modal de previsualización
        const previewModal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
        previewModal.hide();

        // Mostrar overlay de carga
        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.style.display = 'flex';

        // Crear un nuevo archivo Excel con los datos editados
        const wb = XLSX.utils.book_new();        // Agregar encabezados (sin la columna Delegación)
        const headers = [
            'Nombre', 'Apellido Paterno', 'Apellido Materno', 'CI', 'Email',
            'Fecha Nacimiento', 'Género', 'Área', 'Categoría', 'Grado',
            'Número Contacto', 'Nombre Tutor', 'Email Tutor',
            'Modalidad', 'Código Invitación'
        ];
        
        // Preparar los datos sin la columna Delegación (índice 11)
        const processedData = excelData.map(row => {
            // Crear una copia del array para manipular
            const newRow = [...row];
            
            // Si la columna Delegación existe, eliminarla
            if (newRow.length > 11) {
                newRow.splice(11, 1);
            }
            
            return newRow.slice(0, 16);
        });
        
        const wsData = [headers, ...processedData];
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
                // Ocultar overlay de carga
                loadingOverlay.style.display = 'none';

                // Mostrar mensaje de éxito
                const successMessage = document.getElementById('successMessage');
                const successText = document.getElementById('successText');
                successText.textContent = response.success || "La inscripción se ha completado con éxito.";
                
                successMessage.style.display = 'block';
                setTimeout(() => {
                    successMessage.style.display = 'none';
                    window.location.reload();
                }, 3000);
            },
            error: function (xhr) {
                // Ocultar overlay de carga
                loadingOverlay.style.display = 'none';

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

                // Crear y mostrar el modal de error
                const errorModalHTML = `
                    <div class="modal fade error-modal" id="errorModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-exclamation-circle"></i> Error en la inscripción
                                    </h5>
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
                    </div>`;

                // Remover modal anterior si existe
                const existingModal = document.getElementById('errorModal');
                if (existingModal) {
                    existingModal.remove();
                }

                // Agregar nuevo modal al DOM
                document.body.insertAdjacentHTML('beforeend', errorModalHTML);

                // Mostrar el modal
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            }
        });
    });
      // Función para agregar una nueva fila
    $(document).on('click', '#addRowBtn', function() {
        const rowCount = $('#previewTableBody tr').length;
        const newRowIndex = rowCount;
        
        // Crear un array vacío para la nueva fila en excelData
        if (!excelData[newRowIndex]) {
            excelData[newRowIndex] = Array(16).fill('');
        }
        
        let newRowHtml = `<tr data-row="${newRowIndex}" class="new-row">`;
        
        // Columna de acciones con botón eliminar
        newRowHtml += `<td>
            <button type="button" class="btn btn-sm btn-danger delete-row-btn" title="Eliminar fila">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>`;
        
        newRowHtml += `<td>${newRowIndex + 1}</td>`; // Número de fila        // Agregar celdas editables vacías para cada columna
        // Definir las columnas que queremos mostrar con sus índices correctos, omitiendo la posición 11 (Delegación)
        const columnMapping = [
            { index: 0, name: 'Nombre' },
            { index: 1, name: 'Apellido Paterno' },
            { index: 2, name: 'Apellido Materno' },
            { index: 3, name: 'CI' },
            { index: 4, name: 'Email' },
            { index: 5, name: 'Fecha Nacimiento' },
            { index: 6, name: 'Género' },
            { index: 7, name: 'Área' },
            { index: 8, name: 'Categoría' },
            { index: 9, name: 'Grado' },
            { index: 10, name: 'Número Contacto' },
            { index: 12, name: 'Nombre Tutor' },
            { index: 13, name: 'Email Tutor' },
            { index: 14, name: 'Modalidad' },
            { index: 15, name: 'Código Invitación' }
        ];
        
        for (let colIndex = 0; colIndex < columnMapping.length; colIndex++) {
            const { index, name } = columnMapping[colIndex];
            newRowHtml += `<td><div class="editable" contenteditable="true" data-col="${index}" title="${name}"></div></td>`;
        }
        
        newRowHtml += `</tr>`;
        $('#previewTableBody').append(newRowHtml);
        
        // Validar la nueva fila
        validateRow($('#previewTableBody tr').last());
        
        // Actualizar contador de errores
        updateErrorCounter();
        
        // Actualizar DataTable para incluir la nueva fila
        try {
            if ($.fn.DataTable.isDataTable('#previewTable')) {
                $('#previewTable').DataTable().draw();
            }
        } catch (error) {
            console.error('Error al actualizar DataTable:', error);
        }
    });
    
    // Función para eliminar una fila
    $(document).on('click', '.delete-row-btn', function() {
        if (confirm('¿Está seguro que desea eliminar esta fila?')) {
            const row = $(this).closest('tr');
            const rowIndex = row.data('row');
            
            // Eliminar fila de la tabla
            row.remove();
            
            // Eliminar datos de la fila en excelData
            if (excelData[rowIndex]) {
                excelData.splice(rowIndex, 1);
                
                // Actualizar índices de filas restantes
                $('#previewTableBody tr').each(function(index) {
                    $(this).attr('data-row', index);
                    $(this).find('td:nth-child(2)').text(index + 1); // Actualizar número de fila visible
                });
            }
            
            // Revalidar datos
            validateExcelData();
            
            // Actualizar DataTable
            try {
                if ($.fn.DataTable.isDataTable('#previewTable')) {
                    $('#previewTable').DataTable().draw();
                }
            } catch (error) {
                console.error('Error al actualizar DataTable:', error);
            }
        }
    });    // Implementar búsqueda en la tabla con resaltado de coincidencias
    $(document).on('keyup', '#table-search', function() {
        const searchTerm = $(this).val();
        
        // Quitar resaltados anteriores
        try {
            $('#previewTableBody').unmark();
        } catch (e) {
            console.log('Error al quitar resaltados:', e);
        }
        
        // Búsqueda manual para todos los casos
        if (!searchTerm || searchTerm.length === 0) {
            // Si no hay término de búsqueda, mostrar todas las filas
            $('#previewTableBody tr').show();
        } else {
            // Buscar en cada fila manualmente
            $('#previewTableBody tr').each(function() {
                const row = $(this);
                const text = row.text().toLowerCase();
                if (text.includes(searchTerm.toLowerCase())) {
                    row.show();
                } else {
                    row.hide();
                }
            });
            
            // Resaltar el texto encontrado si hay término de búsqueda
            if (searchTerm.length > 1) {
                try {
                    $('#previewTableBody').mark(searchTerm, {
                        "element": "span",
                        "className": "mark",
                        "separateWordSearch": false
                    });
                } catch (e) {
                    console.log('Error al resaltar texto:', e);
                }
            }
        }
    });
      // Limpiar búsqueda cuando se abre el modal
    $(document).on('shown.bs.modal', '#previewModal', function() {
        $('#table-search').val('');
        if ($ && $.fn && $.fn.DataTable && $.fn.DataTable.isDataTable('#previewTable')) {
            $('#previewTable').DataTable().search('').draw();
        }
    });    // Funcionalidad para limpiar la búsqueda con el botón
    $(document).on('click', '#clear-search', function() {
        $('#table-search').val('').focus();
        
        try {
            // Quitar resaltados
            $('#previewTableBody').unmark();
        } catch (e) {
            console.log('Error al quitar resaltados:', e);
        }
        
        // Mostrar todas las filas
        $('#previewTableBody tr').show();
    });
});