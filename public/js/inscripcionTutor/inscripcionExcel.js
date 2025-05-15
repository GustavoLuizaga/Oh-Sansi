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
    
    // Añadir soporte de depuración
    window.debugExcelColumns = function() {
        console.log('Depuración de columnas Excel iniciada');
        
        const columnMap = {};
        $('#previewTable thead th').each(function(index) {
            columnMap[index] = $(this).text().trim();
        });
        
        console.log('Mapa de columnas:', columnMap);
        
        // Verificar primera fila
        if (excelData && excelData.length > 0) {
            console.log('Primera fila de datos:', excelData[0]);
        }
        
        return columnMap;
    };
    
    // Agregar estilos para las celdas inválidas
    const styleElement = document.createElement('style');
    styleElement.textContent = `
        .invalid-cell {
            background-color: #ffdddd !important;
            border: 1px solid #ff6666 !important;
            position: relative;
        }
        .invalid-cell:after {
            content: '⚠️';
            position: absolute;
            top: 0;
            right: 2px;
            font-size: 10px;
            color: #cc0000;
        }
        .error-row {
            background-color: #fff8f8 !important;
        }
        [data-bs-toggle="tooltip"] {
            cursor: help;
        }
    `;
    document.head.appendChild(styleElement);
    
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
            excelData = jsonData;            // Agregar contador de errores antes de la tabla
            if (!$('#errorCounter').length) {
                $('.alert.alert-info').after(
                    '<div id="errorCounter" class="alert alert-warning mb-3" style="display: none;">' +
                    '<i class="fas fa-exclamation-triangle"></i> ' +
                    '<span id="errorCountText">Errores encontrados: 0 filas con errores.</span>' +
                    '</div>'
                );
            }
            
            // Agregar información sobre reglas de validación
            if (!$('#validationRules').length) {
                $('.alert.alert-info').after(                    '<div id="validationRules" class="alert alert-info mb-3">' +
                    '<i class="fas fa-info-circle"></i> ' +
                    '<strong>Reglas de validación:</strong>' +
                    '<ul class="mb-0 ps-3 small">' +
                    '<li>Nombre y apellidos: solo letras (sin números ni caracteres especiales)</li>' +
                    '<li>CI: exactamente 7 dígitos numéricos</li>' +
                    '<li>Email: formato válido con dominio @gmail.com</li>' +
                    '<li>Género: solo "M" o "F"</li>' +
                    '<li>Número de contacto: exactamente 8 dígitos numéricos</li>' +
                    '<li>Modalidad: solo "Individual", "Duo" o "Equipo"</li>' +
                    '</ul>' +
                    '<button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Cerrar"></button>' +
                    '</div>'
                );
                
                // Permitir cerrar la información de validación
                $('#validationRules .btn-close').on('click', function() {
                    $('#validationRules').slideUp();
                });
            }// Llenar tabla con datos
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
                // Definir las columnas base que queremos mostrar
                let baseColumns = [
                    { name: 'Nombre' },
                    { name: 'Apellido Paterno' },
                    { name: 'Apellido Materno' },
                    { name: 'CI' },
                    { name: 'Email' },
                    { name: 'Fecha Nacimiento' },
                    { name: 'Género' },
                    { name: 'Área' },
                    { name: 'Categoría' },
                    { name: 'Grado' },
                    { name: 'Número Contacto' }
                ];
                
                // Verificar si existe la columna Delegación/Colegio en los datos
                const hasDelegacion = row.length >= 12 && (
                    typeof row[11] !== 'undefined' && 
                    row[11] !== null && 
                    row[11] !== ''
                );
                
                // Si existe la columna Delegación, agregarla a las columnas base
                if (hasDelegacion) {
                    baseColumns.push({ name: 'Delegación/Colegio' });
                }
                
                // Agregar el resto de las columnas
                baseColumns = baseColumns.concat([
                    { name: 'Nombre Tutor' },
                    { name: 'Email Tutor' },
                    { name: 'Modalidad' },
                    { name: 'Código Invitación' }
                ]);
                
                // Crear el mapeo de columnas dinámicamente con los índices correctos
                const columnMapping = baseColumns.map((col, idx) => {
                    return {
                        index: idx,
                        name: col.name
                    };
                });
                
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
                drawCallback: function() {
                    // Depurar columnas después de dibujar la tabla
                    setTimeout(function() {
                        if (typeof window.debugExcelColumns === 'function') {
                            console.log('Verificando columnas después de inicializar DataTable');
                            window.debugExcelColumns();
                        }
                    }, 500);
                },
                columnDefs: [
                    { width: "60px", targets: 0 }, // Columna de acciones
                    { width: "50px", targets: 1 }, // Número de fila
                    { width: "150px", targets: [2, 3, 4] }, // Nombre y apellidos
                    { width: "120px", targets: [5] }, // CI
                    { width: "180px", targets: [6] }, // Email estudiante
                    { width: "180px", targets: _getColumnIndex('Email Tutor') }, // Email tutor
                    { width: "120px", targets: _getColumnIndex('Modalidad') }, // Modalidad
                    { width: "120px", targets: _getColumnIndex('Código Invitación') } // Código
                ],
                
                // Función auxiliar para obtener índice de columna por nombre
                initComplete: function(settings, json) {
                    console.log('DataTable inicializada completamente');
                },
                
                // Nota: La función _getColumnIndex está definida más abajo
                
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
                        
                        // Inicializar tooltips para mensajes de error
                        try {
                            // Remover cualquier tooltip existente
                            $('[data-bs-toggle="tooltip"]').tooltip('dispose');
                            
                            // Agregar atributo para activar tooltips en celdas con errores
                            $('.invalid-cell').attr('data-bs-toggle', 'tooltip');
                            
                            // Inicializar tooltips de Bootstrap
                            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl, {
                                    placement: 'top',
                                    trigger: 'hover focus',
                                    container: '#previewModal'
                                });
                            });
                        } catch (tooltipError) {
                            console.error('Error al inicializar tooltips:', tooltipError);
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
    }    // Actualizar datos cuando se edita una celda
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
        
        // Actualizar tooltips para las celdas con error
        try {
            // Remover tooltip anterior si existía
            if ($(this).hasClass('invalid-cell')) {
                $(this).attr('data-bs-toggle', 'tooltip');
                
                // Reinicializar tooltip
                var tooltip = new bootstrap.Tooltip($(this)[0], {
                    placement: 'top',
                    trigger: 'hover focus',
                    container: '#previewModal'
                });
            } else {
                // Si ya no hay error, remover tooltip
                if ($(this).attr('data-bs-toggle') === 'tooltip') {
                    var tooltipInstance = bootstrap.Tooltip.getInstance($(this)[0]);
                    if (tooltipInstance) {
                        tooltipInstance.dispose();
                    }
                    $(this).removeAttr('data-bs-toggle');
                }
            }
        } catch (tooltipError) {
            console.error('Error al actualizar tooltips:', tooltipError);
        }
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
    }    // Función para validar una fila
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
        } else {            // Validar nombre (solo letras)
            if (!validateCell(0, /^[A-Za-záéíóúÁÉÍÓÚüÜñÑ\s]+$/, 'El nombre debe contener solo letras')) {
                // Ya se ha marcado el error en validateCell
            }
            
            // Validar apellido paterno (solo letras)
            if (!validateCell(1, /^[A-Za-záéíóúÁÉÍÓÚüÜñÑ\s]+$/, 'El apellido paterno debe contener solo letras')) {
                // Ya se ha marcado el error en validateCell
            }
            
            // Validar apellido materno (solo letras) - si está presente
            if (rowData[2] && !validateCell(2, /^[A-Za-záéíóúÁÉÍÓÚüÜñÑ\s]+$/, 'El apellido materno debe contener solo letras')) {
                // Ya se ha marcado el error en validateCell
            }
            
            // Validar CI (7 dígitos numéricos)
            if (!validateCell(3, /^\d{7}$/, 'El CI debe contener exactamente 7 dígitos numéricos')) {
                // Ya se ha marcado el error en validateCell
            }
            
            // Validar email (debe ser una dirección @gmail.com)
            if (!validateCell(4, /^[a-zA-Z0-9._%+-]+@gmail\.com$/, 'El email debe tener formato válido y ser @gmail.com')) {
                // Ya se ha marcado el error en validateCell
            }
            
            // Validar género (solo 'M' o 'F')
            if (rowData[6] && !['M', 'F'].includes(rowData[6].toString().toUpperCase())) {
                isValid = false;
                errorMessage = 'El género debe ser "M" o "F"';
                row.find(`[data-col="6"]`)
                   .addClass('invalid-cell')
                   .attr('title', errorMessage);
            } else {
                row.find(`[data-col="6"]`)
                   .removeClass('invalid-cell')
                   .attr('title', '');
            }
            
            // Validar número de contacto (8 dígitos numéricos)
            if (!validateCell(10, /^\d{8}$/, 'El número de contacto debe contener exactamente 8 dígitos numéricos')) {
                // Ya se ha marcado el error en validateCell
            }              // Obtener los índices correctos de cada columna basados en los encabezados de la tabla
            // Esto es más confiable que usar índices fijos
            let tutorEmailIndex = -1;
            let modalidadIndex = -1;
            let codigoIndex = -1;
            
            // Determinar los índices de las columnas dinámicamente
            $('#previewTable thead th').each(function(index) {
                const headerText = $(this).text().trim();
                if (headerText === 'Email Tutor') {
                    tutorEmailIndex = index - 2; // Restar 2 por las columnas de acciones y número
                } else if (headerText === 'Modalidad') {
                    modalidadIndex = index - 2;
                } else if (headerText === 'Código Invitación') {
                    codigoIndex = index - 2;
                }
            });
            
            // Usar índices de respaldo si no se pudieron determinar dinámicamente
            if (tutorEmailIndex === -1) {
                // Método anterior como respaldo
                const hasDelegacionValidation = rowData.length >= 12 && 
                    typeof rowData[11] !== 'undefined' && 
                    rowData[11] !== null && 
                    rowData[11] !== '';
                    
                tutorEmailIndex = hasDelegacionValidation ? 13 : 12;
                modalidadIndex = hasDelegacionValidation ? 14 : 13;
                codigoIndex = hasDelegacionValidation ? 15 : 14;
                
                console.log(`Usando índices de respaldo: Email Tutor=${tutorEmailIndex}, Modalidad=${modalidadIndex}, Código=${codigoIndex}`);
            } else {
                console.log(`Índices dinámicos: Email Tutor=${tutorEmailIndex}, Modalidad=${modalidadIndex}, Código=${codigoIndex}`);
            }
            
            // Validar email del tutor (debe ser una dirección @gmail.com)
            if (rowData[tutorEmailIndex] && !validateCell(tutorEmailIndex, /^[a-zA-Z0-9._%+-]+@gmail\.com$/, 'El email del tutor debe tener formato válido y ser @gmail.com')) {
                // Ya se ha marcado el error en validateCell
            }
            
            // Validar modalidad (si existe)
            if (rowData[modalidadIndex] && !['individual', 'duo', 'equipo'].includes(rowData[modalidadIndex].toString().toLowerCase())) {
                isValid = false;
                errorMessage = 'La modalidad debe ser "Individual", "Duo" o "Equipo"';
                row.find(`[data-col="${modalidadIndex}"]`)
                   .addClass('invalid-cell')
                   .attr('title', errorMessage);
            } else {
                row.find(`[data-col="${modalidadIndex}"]`)
                   .removeClass('invalid-cell')
                   .attr('title', '');
            }
        }
          // Usar los mismos índices que se determinaron anteriormente
        // Si ya tenemos índices dinámicos, usarlos; si no, determinarlos ahora
        if (modalidadIndex === undefined || codigoIndex === undefined) {
            // Determinar los índices de las columnas dinámicamente
            let foundModalidad = false;
            let foundCodigo = false;
            
            $('#previewTable thead th').each(function(index) {
                const headerText = $(this).text().trim();
                if (headerText === 'Modalidad') {
                    modalidadIndex = index - 2; // Restar 2 por las columnas de acciones y número
                    foundModalidad = true;
                } else if (headerText === 'Código Invitación') {
                    codigoIndex = index - 2;
                    foundCodigo = true;
                }
            });
            
            // Si no se encontraron en los encabezados, usar el método de respaldo
            if (!foundModalidad || !foundCodigo) {
                const hasDelegacion = rowData.length >= 12 && 
                    typeof rowData[11] !== 'undefined' && 
                    rowData[11] !== null && 
                    rowData[11] !== '';
                
                modalidadIndex = hasDelegacion ? 14 : 13;
                codigoIndex = hasDelegacion ? 15 : 14;
            }
        }
        
        console.log(`Validando modalidad (${modalidadIndex}): ${rowData[modalidadIndex]}, código (${codigoIndex}): ${rowData[codigoIndex]}`);
        
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
        }        // Función auxiliar para validar una celda específica y mostrar error visual
        function validateCell(index, regex, errorMsg, highlightRow = true) {
            if (rowData[index] && !regex.test(rowData[index].toString())) {
                isValid = false;
                errorMessage = errorMsg;
                
                // Resaltar la celda específica con el error
                row.find(`[data-col="${index}"]`)
                   .addClass('invalid-cell')
                   .attr('title', errorMsg);
                
                return false;
            } else {
                // Eliminar resaltado de error si la celda es válida
                row.find(`[data-col="${index}"]`)
                   .removeClass('invalid-cell')
                   .attr('title', '');
                return true;
            }
        }
        
        // Actualizar estado visual de la fila
        if (isValid) {
            // Limpiar clases de error en toda la fila
            row.removeClass('error-row').attr('title', 'Fila válida');
            row.find('.editable').removeClass('invalid-cell');
        } else {
            row.addClass('error-row').attr('title', errorMessage);
        }

        return isValid;
    }    // Validar grupos de invitación
    function validateGroups() {
        const groups = {};
        let groupErrors = [];
        
        // Determinar primero los índices correctos de modalidad y código
        let groupModalidadIndex = -1;
        let groupCodigoIndex = -1;
        
        // Obtener los índices de las columnas dinámicamente
        $('#previewTable thead th').each(function(index) {
            const headerText = $(this).text().trim();
            if (headerText === 'Modalidad') {
                groupModalidadIndex = index - 2; // Restar 2 por las columnas de acciones y número
            } else if (headerText === 'Código Invitación') {
                groupCodigoIndex = index - 2;
            }
        });
        
        console.log(`Índices para grupos: Modalidad=${groupModalidadIndex}, Código=${groupCodigoIndex}`);

        // Agrupar por código de invitación
        $('#previewTableBody tr').each(function () {
            const rowIndex = $(this).data('row');
            const rowData = excelData[rowIndex] || [];
            
            // Si no se encontraron en los encabezados, usar el método de respaldo
            let modalidadIndex = groupModalidadIndex;
            let codigoIndex = groupCodigoIndex;
            
            if (modalidadIndex === -1 || codigoIndex === -1) {
                const hasDelegacion = rowData.length >= 12 && 
                    typeof rowData[11] !== 'undefined' && 
                    rowData[11] !== null && 
                    rowData[11] !== '';
                
                modalidadIndex = hasDelegacion ? 14 : 13;
                codigoIndex = hasDelegacion ? 15 : 14;
            }
            
            if (rowData[modalidadIndex] && rowData[codigoIndex]) { // Si tiene modalidad y código
                const modalidad = rowData[modalidadIndex].toString().toLowerCase();
                const codigo = rowData[codigoIndex].toString();

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
    $('#submitExcelData').click(async function () {
        try {
            // La lógica de validación y envío ahora se maneja a través del validador
            // Esto está definido en validacion-inscripcion.js
            
            // El método handleSubmitExcelData ya está asociado al botón en validacion-inscripcion.js
            // Esto es solo una capa de protección adicional
            if (typeof InscripcionValidator !== 'undefined' && InscripcionValidator.handleSubmitExcelData) {
                // La función se encargará de todo el proceso de validación y envío
                console.log('Procesando inscripción con sistema de validación avanzado...');
            } else {
                console.error('Sistema de validación no inicializado correctamente');
                alert('Error: El sistema de validación no está disponible. Por favor, recargue la página.');
            }
        } catch (error) {
            console.error('Error al procesar inscripción:', error);
            alert('Ocurrió un error al procesar la inscripción: ' + error.message);
        }
        loadingOverlay.style.display = 'flex';

        // Crear un nuevo archivo Excel con los datos editados
        const wb = XLSX.utils.book_new();        // Determinar si hay datos de Delegación/Colegio en alguna fila
        let hasDelegacion = false;
        for (let i = 0; i < excelData.length; i++) {
            if (excelData[i] && excelData[i].length >= 12 && 
                typeof excelData[i][11] !== 'undefined' && 
                excelData[i][11] !== null && 
                excelData[i][11] !== '') {
                hasDelegacion = true;
                break;
            }
        }
        
        // Definir encabezados basados en la presencia de la columna Delegación
        let headers = [
            'Nombre', 'Apellido Paterno', 'Apellido Materno', 'CI', 'Email',
            'Fecha Nacimiento', 'Género', 'Área', 'Categoría', 'Grado',
            'Número Contacto'
        ];
        
        // Agregar la columna de Delegación si existe
        if (hasDelegacion) {
            headers.push('Delegación/Colegio');
        }
        
        // Agregar el resto de los encabezados
        headers = headers.concat([
            'Nombre Tutor', 'Email Tutor', 'Modalidad', 'Código Invitación'
        ]);
        
        // Prepara los datos para el archivo final
        const processedData = excelData.map(row => {
            // Solo necesitamos procesar si hay una columna de delegación pero está vacía
            // En caso contrario, usamos los datos tal como están
            if (!hasDelegacion && row.length > 11) {
                // Si no estamos incluyendo delegación pero hay datos en esa posición,
                // debemos eliminar ese índice para que los datos posteriores se alineen correctamente
                const newRow = [...row];
                newRow.splice(11, 1);
                return newRow.slice(0, 16);
            } else {
                // Si estamos incluyendo delegación o no hay datos en esa posición,
                // simplemente tomamos los datos tal como están
                return row.slice(0, hasDelegacion ? 17 : 16);
            }
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
        // Determinar si se debe incluir la columna de Delegación
        // Verificamos si hay alguna fila existente con delegación
        let includeDelegacion = false;
        for (let i = 0; i < excelData.length; i++) {
            if (excelData[i] && excelData[i].length >= 12 && 
                typeof excelData[i][11] !== 'undefined' && 
                excelData[i][11] !== null && 
                excelData[i][11] !== '') {
                includeDelegacion = true;
                break;
            }
        }
        
        // Definir las columnas base que queremos mostrar
        let baseColumns = [
            { name: 'Nombre' },
            { name: 'Apellido Paterno' },
            { name: 'Apellido Materno' },
            { name: 'CI' },
            { name: 'Email' },
            { name: 'Fecha Nacimiento' },
            { name: 'Género' },
            { name: 'Área' },
            { name: 'Categoría' },
            { name: 'Grado' },
            { name: 'Número Contacto' }
        ];
        
        // Si se incluye la delegación, agregarla a las columnas base
        if (includeDelegacion) {
            baseColumns.push({ name: 'Delegación/Colegio' });
        }
        
        // Agregar el resto de las columnas
        baseColumns = baseColumns.concat([
            { name: 'Nombre Tutor' },
            { name: 'Email Tutor' },
            { name: 'Modalidad' },
            { name: 'Código Invitación' }
        ]);
        
        // Crear el mapeo de columnas dinámicamente con los índices correctos
        const columnMapping = baseColumns.map((col, idx) => {
            return {
                index: idx,
                name: col.name
            };
        });
        
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