/**
 * Validación avanzada de inscripciones para el sistema de inscripción de estudiantes
 * 
 * Este script se encarga de validar completamente los datos antes de enviarlos al servidor:
 * - Verifica que todos los datos sean válidos
 * - Verifica edad mínima (5 años)
 * - Verifica que las áreas y categorías pertenezcan al mismo delegado
 * - Verifica que los grados correspondan a las categorías
 * - Verifica que las categorías correspondan a las áreas
 * - Verifica usuarios existentes (CI y Email)
 */

// Objeto global para mantener cachés y datos de validación
const InscripcionValidator = {
    areasValidas: {},
    categoriasValidas: {},
    gradosValidos: {},
    usuariosVerificados: {},
    errorCount: 0,
    advancedErrorMessages: [], // Para errores que no están asociados a celdas específicas
    delegadoDelegacion: null,
    delegadoAreas: [],
    
    /**
     * Inicializa el validador
     */
    initialize: function() {
        this.errorCount = 0;
        this.advancedErrorMessages = [];
        this.loadDelegadoInfo();
        this.setupEventHandlers();
        console.log('Validador de inscripción inicializado');
    },
    
    /**
     * Carga información del delegado (áreas asignadas, delegación)
     */
    loadDelegadoInfo: function() {
        // Obtener la información del delegado desde los atributos del body
        const body = document.querySelector('body');
        const convocatoriaId = body.getAttribute('data-convocatoria-id');
        const delegacionNombre = body.getAttribute('data-delegacion-nombre');
        
        this.delegadoDelegacion = delegacionNombre;
        
        // Cargar áreas del delegado mediante AJAX
        $.ajax({
            url: '/api/tutor/areas',
            type: 'GET',
            data: {
                convocatoria_id: convocatoriaId
            },
            dataType: 'json',
            success: function(response) {
                InscripcionValidator.delegadoAreas = response.areas || [];
                console.log('Áreas del delegado cargadas:', InscripcionValidator.delegadoAreas);
                
                // Cargar las categorías disponibles para cada área
                InscripcionValidator.loadCategorias(convocatoriaId);
            },
            error: function(error) {
                console.error('Error al cargar áreas del delegado:', error);
            }
        });
    },
    
    /**
     * Carga las categorías disponibles para cada área
     */
    loadCategorias: function(convocatoriaId) {
        $.ajax({
            url: '/api/categorias/por-areas',
            type: 'GET',
            data: {
                convocatoria_id: convocatoriaId,
                areas: this.delegadoAreas.map(area => area.id)
            },
            dataType: 'json',
            success: function(response) {
                InscripcionValidator.categoriasValidas = response.categorias || {};
                console.log('Categorías disponibles cargadas');
                
                // Cargar los grados disponibles para cada categoría
                InscripcionValidator.loadGrados();
            },
            error: function(error) {
                console.error('Error al cargar categorías:', error);
            }
        });
    },
    
    /**
     * Carga los grados disponibles para cada categoría
     */
    loadGrados: function() {
        const categoriaIds = [];
        for (const areaId in this.categoriasValidas) {
            this.categoriasValidas[areaId].forEach(categoria => {
                categoriaIds.push(categoria.id);
            });
        }
        
        $.ajax({
            url: '/api/grados/por-categorias',
            type: 'GET',
            data: {
                categorias: categoriaIds
            },
            dataType: 'json',
            success: function(response) {
                InscripcionValidator.gradosValidos = response.grados || {};
                console.log('Grados disponibles cargados');
            },
            error: function(error) {
                console.error('Error al cargar grados:', error);
            }
        });
    },
    
    /**
     * Configura manejadores de eventos 
     */
    setupEventHandlers: function() {
        // Controlador para el botón de confirmar inscripción
        $(document).on('click', '#submitExcelData', this.handleSubmitExcelData);
    },
    
    /**
     * Verifica que un estudiante cumpla con la edad mínima (5 años)
     */
    verificarEdadMinima: function(fechaNacimiento) {
        if (!fechaNacimiento) return false;
        
        try {
            // Parsear la fecha de nacimiento
            const fecha = new Date(fechaNacimiento);
            if (isNaN(fecha)) return false;
            
            // Calcular edad
            const hoy = new Date();
            let edad = hoy.getFullYear() - fecha.getFullYear();
            const m = hoy.getMonth() - fecha.getMonth();
            
            if (m < 0 || (m === 0 && hoy.getDate() < fecha.getDate())) {
                edad--;
            }
            
            return edad >= 5;
        } catch (e) {
            console.error('Error al validar edad:', e);
            return false;
        }
    },
    
    /**
     * Verifica si un CI ya existe en la base de datos
     */
    verificarCIExistente: function(ci) {
        return new Promise((resolve, reject) => {
            // Si ya verificamos este CI, usar el resultado en caché
            if (this.usuariosVerificados[ci]) {
                resolve(this.usuariosVerificados[ci]);
                return;
            }
            
            $.ajax({
                url: '/api/usuarios/verificar-ci',
                type: 'GET',
                data: {
                    ci: ci
                },
                dataType: 'json',
                success: function(response) {
                    // Guardar en caché
                    InscripcionValidator.usuariosVerificados[ci] = response;
                    resolve(response);
                },
                error: function(error) {
                    console.error('Error al verificar CI:', error);
                    reject(error);
                }
            });
        });
    },
    
    /**
     * Verifica si un email ya existe en la base de datos
     */
    verificarEmailExistente: function(email) {
        return new Promise((resolve, reject) => {
            // Si ya verificamos este email, usar el resultado en caché
            if (this.usuariosVerificados[email]) {
                resolve(this.usuariosVerificados[email]);
                return;
            }
            
            $.ajax({
                url: '/api/usuarios/verificar-email',
                type: 'GET',
                data: {
                    email: email
                },
                dataType: 'json',
                success: function(response) {
                    // Guardar en caché
                    InscripcionValidator.usuariosVerificados[email] = response;
                    resolve(response);
                },
                error: function(error) {
                    console.error('Error al verificar email:', error);
                    reject(error);
                }
            });
        });
    },
    
    /**
     * Verifica que un área pertenezca a las áreas del delegado
     */
    verificarAreaDelegado: function(nombreArea) {
        return this.delegadoAreas.some(area => 
            area.nombre.toLowerCase() === nombreArea.toLowerCase());
    },
    
    /**
     * Verifica que la categoría corresponda al área
     */
    verificarCategoriaArea: function(nombreArea, nombreCategoria) {
        const area = this.delegadoAreas.find(a => 
            a.nombre.toLowerCase() === nombreArea.toLowerCase());
        
        if (!area) return false;
        
        const categoriasDeArea = this.categoriasValidas[area.id] || [];
        return categoriasDeArea.some(categoria => 
            categoria.nombre.toLowerCase() === nombreCategoria.toLowerCase());
    },
    
    /**
     * Verifica que el grado corresponda a la categoría
     */
    verificarGradoCategoria: function(nombreCategoria, nombreGrado) {
        // Encontrar la categoría
        let categoriaId = null;
        
        for (const areaId in this.categoriasValidas) {
            const categoriaEncontrada = this.categoriasValidas[areaId].find(categoria =>
                categoria.nombre.toLowerCase() === nombreCategoria.toLowerCase());
            
            if (categoriaEncontrada) {
                categoriaId = categoriaEncontrada.id;
                break;
            }
        }
        
        if (!categoriaId) return false;
        
        // Verificar si el grado pertenece a esta categoría
        const gradosDeCategoria = this.gradosValidos[categoriaId] || [];
        return gradosDeCategoria.some(grado => 
            grado.grado.toLowerCase() === nombreGrado.toLowerCase());
    },
    
    /**
     * Realiza todas las validaciones necesarias antes de la inscripción
     */
    validarDatosCompletos: function() {
        const self = this;
        this.errorCount = 0;
        this.advancedErrorMessages = [];
        
        // Obtener filas de la tabla
        const filas = $('#previewTableBody tr');
        if (filas.length === 0) {
            this.advancedErrorMessages.push('No hay estudiantes para inscribir.');
            return false;
        }
        
        // Crear un mapa para verificar inscripciones duplicadas
        const estudiantesPorArea = new Map();
        
        // Validar cada fila
        filas.each(function() {
            const row = $(this);
            const rowIndex = row.data('row');
            const rowData = excelData[rowIndex] || [];
            
            // Obtener índices de columnas importantes
            const includeDelegacion = rowData.length >= 12 && 
                    typeof rowData[11] !== 'undefined' && 
                    rowData[11] !== null && 
                    rowData[11] !== '';
            
            const indexMapping = {
                nombre: 0,
                apellidoPaterno: 1,
                apellidoMaterno: 2,
                ci: 3,
                email: 4,
                fechaNacimiento: 5,
                genero: 6,
                area: 7,
                categoria: 8,
                grado: 9,
                numeroContacto: 10,
                delegacion: includeDelegacion ? 11 : null,
                nombreTutor: includeDelegacion ? 12 : 11,
                emailTutor: includeDelegacion ? 13 : 12,
                modalidad: includeDelegacion ? 14 : 13,
                codigoInvitacion: includeDelegacion ? 15 : 14
            };
            
            // Verificar edad mínima
            const fechaNacimiento = rowData[indexMapping.fechaNacimiento];
            if (fechaNacimiento && !self.verificarEdadMinima(fechaNacimiento)) {
                // Marcar error en la celda
                row.find(`td .editable[data-col="${indexMapping.fechaNacimiento}"]`)
                    .addClass('invalid-cell')
                    .attr('title', 'La edad mínima debe ser de 5 años.');
                self.errorCount++;
            }
            
            // Verificar área del delegado
            const area = rowData[indexMapping.area];
            if (area && !self.verificarAreaDelegado(area)) {
                // Marcar error en la celda
                row.find(`td .editable[data-col="${indexMapping.area}"]`)
                    .addClass('invalid-cell')
                    .attr('title', 'El área no está asignada a este delegado.');
                self.errorCount++;
            }
            
            // Verificar categoría corresponde al área
            const categoria = rowData[indexMapping.categoria];
            if (area && categoria && !self.verificarCategoriaArea(area, categoria)) {
                // Marcar error en la celda
                row.find(`td .editable[data-col="${indexMapping.categoria}"]`)
                    .addClass('invalid-cell')
                    .attr('title', `La categoría '${categoria}' no corresponde al área '${area}'.`);
                self.errorCount++;
            }
            
            // Verificar grado corresponde a la categoría
            const grado = rowData[indexMapping.grado];
            if (categoria && grado && !self.verificarGradoCategoria(categoria, grado)) {
                // Marcar error en la celda
                row.find(`td .editable[data-col="${indexMapping.grado}"]`)
                    .addClass('invalid-cell')
                    .attr('title', `El grado '${grado}' no corresponde a la categoría '${categoria}'.`);
                self.errorCount++;
            }
            
            // Verificar que no se duplique la inscripción de un estudiante a la misma área
            const ci = rowData[indexMapping.ci];
            const areaInscripcion = rowData[indexMapping.area];
            
            if (ci && areaInscripcion) {
                const key = `${ci}_${areaInscripcion}`;
                if (estudiantesPorArea.has(key)) {
                    // Ya existe una inscripción para este estudiante en esta área
                    const filaAnterior = estudiantesPorArea.get(key);
                    
                    // Marcar error en ambas filas
                    row.find(`td .editable[data-col="${indexMapping.ci}"]`)
                        .addClass('invalid-cell')
                        .attr('title', `Ya existe una inscripción para este CI en el área '${areaInscripcion}' (fila ${filaAnterior + 1}).`);
                    
                    $(`#previewTableBody tr[data-row="${filaAnterior}"]`)
                        .find(`td .editable[data-col="${indexMapping.ci}"]`)
                        .addClass('invalid-cell')
                        .attr('title', `Ya existe una inscripción para este CI en el área '${areaInscripcion}' (fila ${rowIndex + 1}).`);
                    
                    self.errorCount += 2;
                } else {
                    estudiantesPorArea.set(key, rowIndex);
                }
            }
        });
        
        // Verificar si hay errores
        if (this.errorCount > 0 || this.advancedErrorMessages.length > 0) {
            // Actualizar contador de errores
            updateErrorCounter();
            return false;
        }
        
        return true;
    },
    
    /**
     * Verifica si los usuarios ya existen en la base de datos
     */
    verificarUsuariosExistentes: async function() {
        // Obtener todos los CI y emails
        const cisYEmails = [];
        $('#previewTableBody tr').each(function() {
            const row = $(this);
            const rowIndex = row.data('row');
            const rowData = excelData[rowIndex] || [];
            
            // Determinar si incluye delegación
            const includeDelegacion = rowData.length >= 12 && 
                typeof rowData[11] !== 'undefined' && 
                rowData[11] !== null && 
                rowData[11] !== '';
            
            // Obtener CI y email
            const ci = rowData[3];
            const email = rowData[4];
            
            if (ci && email) {
                cisYEmails.push({ ci, email, rowIndex });
            }
        });
        
        // Verificar cada CI y email
        const promesas = [];
        for (const { ci, email, rowIndex } of cisYEmails) {
            promesas.push(
                this.verificarCIExistente(ci).then(resultado => ({ ci, resultado, rowIndex }))
            );
            promesas.push(
                this.verificarEmailExistente(email).then(resultado => ({ email, resultado, rowIndex }))
            );
        }
        
        try {
            const resultados = await Promise.all(promesas);
            let erroresUsuarios = 0;
            
            // Procesar resultados
            for (const resultado of resultados) {
                if (resultado.ci) {
                    const { ci, resultado: resCi, rowIndex } = resultado;
                    
                    if (resCi.existe && !resCi.esEstudiante) {
                        // CI existe pero no es estudiante
                        $(`#previewTableBody tr[data-row="${rowIndex}"]`)
                            .find(`td .editable[data-col="3"]`)
                            .addClass('invalid-cell')
                            .attr('title', `El CI ${ci} existe en el sistema pero no tiene el rol de estudiante.`);
                        erroresUsuarios++;
                    }
                }
                
                if (resultado.email) {
                    const { email, resultado: resEmail, rowIndex } = resultado;
                    
                    if (resEmail.existe && !resEmail.esEstudiante) {
                        // Email existe pero no es estudiante
                        $(`#previewTableBody tr[data-row="${rowIndex}"]`)
                            .find(`td .editable[data-col="4"]`)
                            .addClass('invalid-cell')
                            .attr('title', `El email ${email} existe en el sistema pero no tiene el rol de estudiante.`);
                        erroresUsuarios++;
                    }
                }
            }
            
            if (erroresUsuarios > 0) {
                // Actualizar contador de errores
                this.errorCount += erroresUsuarios;
                updateErrorCounter();
                return false;
            }
            
            return true;
        } catch (error) {
            console.error('Error al verificar usuarios:', error);
            this.advancedErrorMessages.push('Error al verificar usuarios existentes.');
            return false;
        }
    },
    
    /**
     * Muestra los errores avanzados de validación
     */
    mostrarErroresAvanzados: function() {
        // Crear o actualizar el contenedor de errores avanzados
        if ($('#advancedErrorContainer').length === 0) {
            $('.alert.alert-warning').after(
                '<div id="advancedErrorContainer" class="alert alert-danger mb-3">' +
                '<i class="fas fa-exclamation-triangle"></i> ' +
                '<strong>Errores de validación avanzada:</strong>' +
                '<ul id="advancedErrorList"></ul>' +
                '</div>'
            );
        } else {
            $('#advancedErrorList').empty();
        }
        
        // Mostrar u ocultar según corresponda
        if (this.advancedErrorMessages.length > 0) {
            this.advancedErrorMessages.forEach(error => {
                $('#advancedErrorList').append(`<li>${error}</li>`);
            });
            $('#advancedErrorContainer').show();
            return true; // Hay errores
        } else {
            $('#advancedErrorContainer').hide();
            return false; // No hay errores
        }
    },
    
    /**
     * Manejador para el botón de confirmar inscripción
     */    handleSubmitExcelData: async function() {
        // Verificar errores básicos
        if (errorCount > 0) {
            alert(`Por favor, corrija los ${errorCount} errores antes de continuar.`);
            return;
        }
        
        // Verificar que haya una convocatoria seleccionada
        const convocatoriaId = $('#excel-convocatoria-dropdown').val();
        if (!convocatoriaId) {
            alert('Por favor, seleccione una convocatoria antes de continuar.');
            return;
        }

        // Validar grupos
        const groupErrors = validateGroups();
        if (showGroupErrors(groupErrors)) {
            return; // Si hay errores de grupo, no continuar
        }
        
        // Validar datos completos
        if (!InscripcionValidator.validarDatosCompletos()) {
            // Si hay errores avanzados, mostrarlos
            InscripcionValidator.mostrarErroresAvanzados();
            
            if (InscripcionValidator.errorCount > 0) {
                alert(`Por favor, corrija los ${InscripcionValidator.errorCount} errores avanzados antes de continuar.`);
            }
            return;
        }
        
        // Verificar usuarios existentes
        const usuariosValidos = await InscripcionValidator.verificarUsuariosExistentes();
        
        if (!usuariosValidos) {
            // Si hay errores en usuarios, mostrarlos
            InscripcionValidator.mostrarErroresAvanzados();
            
            if (InscripcionValidator.errorCount > 0) {
                alert(`Por favor, corrija los ${InscripcionValidator.errorCount} errores de verificación de usuarios antes de continuar.`);
            }
            return;
        }

        // Todo está correcto, continuar con la inscripción
        // Cerrar el modal de previsualización
        const previewModal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
        previewModal.hide();

        // Mostrar overlay de carga
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
        }

        // Preparar los datos para enviar al servidor
        const excelDataToSend = [];
        $('#previewTableBody tr').each(function() {
            const rowIndex = $(this).data('row');
            if (typeof rowIndex !== 'undefined' && excelData[rowIndex]) {
                excelDataToSend.push(excelData[rowIndex]);
            }
        });

        // Obtener la convocatoria seleccionada
        // Enviar datos al servidor
        $.ajax({
            url: '/register/lista/save',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                idConvocatoria: convocatoriaId || $('#excel-convocatoria-dropdown').val(), // Asegurar que siempre tenemos una convocatoria
                excelData: JSON.stringify(excelDataToSend)
            },
            success: function(response) {
                // Ocultar overlay de carga
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }

                if (response.success) {
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        title: 'Éxito',
                        text: response.message || 'Inscripciones completadas correctamente.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Recargar la página o redirigir si es necesario
                        window.location.reload();
                    });
                } else {
                    // Mostrar errores
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Ocurrió un error al procesar las inscripciones.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(error) {
                // Ocultar overlay de carga
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }

                console.error('Error al enviar datos:', error);
                
                // Mostrar mensaje de error
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al enviar los datos. Por favor intente nuevamente.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
};

// Inicializar el validador cuando el DOM esté listo
$(document).ready(function() {
    InscripcionValidator.initialize();
});
