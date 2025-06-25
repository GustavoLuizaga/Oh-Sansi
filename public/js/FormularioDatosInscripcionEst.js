// TUVE QUE COMENTAR ESTA PARTE DE ALERTA DE CONFIRMACION DE INSCRIPCION REALIZADA CORRECTAMENTE YA QUE CAUSA CONFLICTOS CON LA SIGUEITEN DOCUMENT EVENTLISTENNER
// document.addEventListener('DOMContentLoaded', () => {
//     document.querySelectorAll('.alert').forEach(alert => {
//         // Agregar botón de cierre
//         const closeBtn = document.createElement('button');
//         closeBtn.className = 'alert-close';
//         closeBtn.innerHTML = '×';
//         closeBtn.onclick = () => alert.remove();
//         alert.appendChild(closeBtn);

//         // Cierre automático después de 5s
//         setTimeout(() => {
//             alert.style.opacity = '0';
//             setTimeout(() => alert.remove(), 300);
//         }, 5000);
//     });
// });

//JS del modal de editar y todo lo relacionado al OCR, todo lo relacionado a subir imagenes
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('comprobantePagoFile');
    const dropArea = document.querySelector('.file-drop-area');
    const filePreview = document.querySelector('.file-preview');
    const imagePreview = document.querySelector('.image-preview');
    const pdfPreview = document.querySelector('.pdf-preview');
    const imgElement = document.querySelector('.img-preview');
    const pdfCanvas = document.getElementById('pdf-preview-canvas');
    const fileName = document.querySelector('.file-name');
    const removeBtn = document.querySelector('.btn-remove-file');
    const feedbackArea = document.querySelector('.file-feedback');
    
    // Variables globales
    let codigoComprobante = null;
    let estadoOCR = 0; // 0 = no procesado, 1 = éxito, 2 = error
    let confirmacionAceptada = false;
    let correccionManual = null;

    // Elementos de confirmación
    const confirmacionSection = document.querySelector('.numero-confirmacion');
    const textoConfirmacion = document.querySelector('.confirmacion-texto');
    const inputManual = document.getElementById('inputCorreccionManual');

    // Función para mostrar errores
    function mostrarError(mensaje) {
        feedbackArea.textContent = mensaje;
        feedbackArea.style.display = 'block';
        feedbackArea.className = 'file-feedback text-danger';
    }

    // Función para mostrar mensajes de proceso
    function mostrarProceso(mensaje) {
        feedbackArea.textContent = mensaje;
        feedbackArea.style.display = 'block';
        feedbackArea.className = 'file-feedback text-info';
    }

    // Función para extraer número de comprobante del texto (mejorada)
    function extraerNumeroComprobante(texto) {
        // Buscar en los primeros 150 caracteres (ampliado para PDF)
        const textoBusqueda = texto.substring(0, 150);
        
        // Patrones de búsqueda más amplios y específicos
        const patrones = [
            // Buscar patrones con palabras clave seguidas de números de 6-7 dígitos
            /(Nro|No\.?|Numero?|Comprobante|Boleta|Recibo)[\s:.\-#]*([0-9]{6,7})/i,
            // Buscar números de exactamente 7 dígitos precedidos por espacios o caracteres especiales
            /[\s\-#:.]([0-9]{7})[\s\-#:.\n\r]?/g,
            // Buscar números de exactamente 6 dígitos precedidos por espacios o caracteres especiales
            /[\s\-#:.]([0-9]{6})[\s\-#:.\n\r]?/g,
            // Buscar al inicio de línea números de 6-7 dígitos
            /^([0-9]{6,7})[\s\-#:.\n\r]?/gm,
            // Patrón más general para números de 6-7 dígitos
            /([0-9]{6,7})/g
        ];
        
        console.log("Texto a analizar:", textoBusqueda);
        
        for (let i = 0; i < patrones.length; i++) {
            const patron = patrones[i];
            console.log(`Probando patrón ${i + 1}:`, patron);
            
            // Resetear el índice del patrón para búsquedas globales
            patron.lastIndex = 0;
            
            let match;
            const matches = [];
            
            // Para patrones globales, obtener todas las coincidencias
            if (patron.flags.includes('g')) {
                while ((match = patron.exec(textoBusqueda)) !== null) {
                    matches.push(match);
                    // Evitar bucle infinito en patrones que coinciden con string vacío
                    if (match.index === patron.lastIndex) {
                        patron.lastIndex++;
                    }
                }
            } else {
                // Para patrones no globales, obtener la primera coincidencia
                match = textoBusqueda.match(patron);
                if (match) {
                    matches.push(match);
                }
            }
            
            console.log(`Coincidencias encontradas para patrón ${i + 1}:`, matches);
            
            // Procesar todas las coincidencias encontradas
            for (const coincidencia of matches) {
                // Extraer el número (puede estar en grupo 1 o 2 dependiendo del patrón)
                const numero = coincidencia[2] || coincidencia[1] || coincidencia[0];
                
                if (numero) {
                    // Limpiar el número pero mantener los ceros iniciales
                    const numeroLimpio = numero.replace(/[^\d]/g, ''); // Solo eliminar no-dígitos
                    
                    console.log(`Número extraído: "${numero}" -> Limpio: "${numeroLimpio}"`);
                    
                    // Validar que tenga entre 6 y 7 dígitos
                    if (numeroLimpio.length >= 6 && numeroLimpio.length <= 7) {
                        console.log(`Número válido encontrado: "${numeroLimpio}"`);
                        
                        // Si tiene 6 dígitos, agregar un cero al inicio para hacerlo de 7
                        if (numeroLimpio.length === 6) {
                            const numeroCompleto = '0' + numeroLimpio;
                            console.log(`Número de 6 dígitos convertido a 7: "${numeroCompleto}"`);
                            return numeroCompleto;
                        }
                        
                        // Si ya tiene 7 dígitos, devolverlo tal como está (preservando ceros iniciales)
                        return numeroLimpio;
                    }
                }
            }
        }
        
        console.log("No se encontró ningún número de comprobante válido");
        return null;
    }

    // Función auxiliar para validar formato de número de comprobante
    function validarFormatoComprobante(numero) {
        // Debe ser exactamente 7 dígitos
        return /^[0-9]{7}$/.test(numero);
    }

    // Función auxiliar para formatear número de comprobante (opcional)
    function formatearNumeroComprobante(numero) {
        // Asegurar que siempre tenga 7 dígitos con ceros a la izquierda
        return numero.toString().padStart(7, '0');
    }

    // Función para procesar OCR en imágenes
    async function processImageWithOCR(imageUrl) {
        console.log("Iniciando OCR para imagen...");
        mostrarProceso("Procesando imagen...");
        
        const btnSubir = document.getElementById('btnSubirComprobante');
        btnSubir.disabled = true;
        
        try {
            const worker = await Tesseract.createWorker('spa');
            const { data: { text } } = await worker.recognize(imageUrl);
            console.log("Texto extraído de imagen:", text);
            
            const numeroDetectado = extraerNumeroComprobante(text);
            
            if (numeroDetectado) {
                codigoComprobante = numeroDetectado;
                estadoOCR = 1;
                console.log("Número detectado en imagen:", codigoComprobante);
                feedbackArea.style.display = 'none';
                mostrarConfirmacion();
            } else {
                throw new Error("En la imagen no se detectó ningún Nro. Comprobante. Vuelve a subir una imagen con más calidad.");
            }
            
            await worker.terminate();
        } catch (error) {
            console.error("Error en OCR de imagen:", error);
            manejarErrorOCR(error.message);
        }
    }

    // Función para procesar OCR en PDFs
    async function processPDFWithOCR(file) {
        console.log("Iniciando OCR para PDF...");
        mostrarProceso("Procesando PDF...");
        
        const btnSubir = document.getElementById('btnSubirComprobante');
        btnSubir.disabled = true;
        
        try {
            const arrayBuffer = await file.arrayBuffer();
            const pdf = await pdfjsLib.getDocument({
                data: new Uint8Array(arrayBuffer)
            }).promise;

            // Verificar que sea de una sola página
            if (pdf.numPages > 1) {
                throw new Error("El PDF debe tener exactamente 1 página. El archivo seleccionado tiene " + pdf.numPages + " páginas.");
            }

            // Mostrar previsualización
            await mostrarPreviewPDF(pdf);

            // Procesar OCR
            const page = await pdf.getPage(1);
            const viewport = page.getViewport({ scale: 2.0 }); // Mayor escala para mejor OCR
            const canvas = document.createElement('canvas');
            
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            
            await page.render({
                canvasContext: canvas.getContext('2d'),
                viewport
            }).promise;

            // Ejecutar OCR
            const worker = await Tesseract.createWorker('spa');
            const { data: { text } } = await worker.recognize(canvas);
            console.log("Texto extraído de PDF:", text);
            
            const numeroDetectado = extraerNumeroComprobante(text);
            
            if (numeroDetectado) {
                codigoComprobante = numeroDetectado;
                estadoOCR = 1;
                console.log("Número detectado en PDF:", codigoComprobante);
                feedbackArea.style.display = 'none';
                mostrarConfirmacion();
            } else {
                throw new Error("En el PDF no se detectó ningún Nro. Comprobante. Asegúrate de que el documento sea legible y contenga el número de comprobante.");
            }
            
            await worker.terminate();
            
        } catch (error) {
            console.error("Error en OCR de PDF:", error);
            manejarErrorOCR(error.message);
        }
    }

    // Función para mostrar previsualización de PDF
    async function mostrarPreviewPDF(pdf) {
        try {
            const page = await pdf.getPage(1);
            const viewport = page.getViewport({ scale: 0.4 });
            
            pdfCanvas.height = viewport.height;
            pdfCanvas.width = viewport.width;
            pdfCanvas.style.maxWidth = '100%';
            pdfCanvas.style.height = 'auto';
            
            await page.render({
                canvasContext: pdfCanvas.getContext('2d'),
                viewport
            }).promise;
            
        } catch (error) {
            console.error("Error al mostrar previsualización de PDF:", error);
            mostrarError("Error al generar previsualización del PDF");
        }
    }

    // Función para mostrar confirmación
    function mostrarConfirmacion() {
        textoConfirmacion.innerHTML = `El número detectado es <strong>${codigoComprobante}</strong>, ¿es correcto?`;
        confirmacionSection.style.display = 'block';
        
        // Resetear estados de confirmación
        confirmacionAceptada = false;
        correccionManual = null;
        inputManual.value = '';
        document.querySelector('.correccion-manual').style.display = 'none';
    }

    // Función para manejar errores de OCR
    function manejarErrorOCR(mensaje) {
        estadoOCR = 2;
        mostrarError(mensaje);
        codigoComprobante = null;
        confirmacionSection.style.display = 'none';
    }

    // Función para manejar archivos
    async function handleFiles(files) {
        // Resetear estados
        feedbackArea.style.display = 'none';
        estadoOCR = 0;
        codigoComprobante = null;
        confirmacionAceptada = false;
        correccionManual = null;
        confirmacionSection.style.display = 'none';
        inputManual.value = '';
        
        if (files.length > 0) {
            const file = files[0];
            const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            const maxSize = 5 * 1024 * 1024;

            if (!validTypes.includes(file.type)) {
                mostrarError('Formato de archivo no válido. Use PDF, JPG o PNG.');
                fileInput.value = '';
                return;
            }

            if (file.size > maxSize) {
                mostrarError('El archivo excede el límite de 5MB');
                fileInput.value = '';
                return;
            }
            
            fileName.textContent = file.name;
            dropArea.style.display = 'none';
            filePreview.style.display = 'block';
            
            if (file.type === 'application/pdf') {
                // Manejar PDF
                pdfPreview.style.display = 'block';
                imagePreview.style.display = 'none';
                
                // Procesar PDF con OCR
                await processPDFWithOCR(file);
                
            } else if (file.type.startsWith('image/')) {
                // Manejar imagen
                imagePreview.style.display = 'block';
                pdfPreview.style.display = 'none';
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgElement.src = e.target.result;
                    processImageWithOCR(e.target.result);
                };
                reader.readAsDataURL(file);
            }
        }
    }

    // Eventos de confirmación
    document.querySelector('.btn-confirmar-si').addEventListener('click', function() {
        confirmacionAceptada = true;
        document.querySelector('.correccion-manual').style.display = 'none';
        document.getElementById('btnSubirComprobante').disabled = false;
    });

    document.querySelector('.btn-confirmar-no').addEventListener('click', function() {
        confirmacionAceptada = false;
        document.querySelector('.correccion-manual').style.display = 'block';
        document.getElementById('btnSubirComprobante').disabled = true;
    });

    // Validación input manual
    inputManual.addEventListener('input', function(e) {
        const valor = e.target.value.replace(/\D/g, '');
        e.target.value = valor;
        
        if (valor.length === 7) {
            e.target.classList.remove('is-invalid');
            correccionManual = valor;
            document.getElementById('btnSubirComprobante').disabled = false;
        } else {
            e.target.classList.add('is-invalid');
            correccionManual = null;
            document.getElementById('btnSubirComprobante').disabled = true;
        }
    });

    // Event listeners para file input
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    // Drag and drop events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
        }, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, function() {
            this.classList.add('is-active');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, function() {
            this.classList.remove('is-active');
        }, false);
    });

    dropArea.addEventListener('drop', function(e) {
        handleFiles(e.dataTransfer.files);
    }, false);

    // Remove file event
    removeBtn.addEventListener('click', function() {
        fileInput.value = '';
        filePreview.style.display = 'none';
        imagePreview.style.display = 'none';
        pdfPreview.style.display = 'none';
        dropArea.style.display = 'block';
        feedbackArea.style.display = 'none';
        codigoComprobante = null;
        estadoOCR = 0;
        confirmacionSection.style.display = 'none';
        confirmacionAceptada = false;
        correccionManual = null;
        inputManual.value = '';
        document.getElementById('btnSubirComprobante').disabled = true;
    });

    // Envío del formulario
    document.getElementById('comprobantePagoForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btnSubir = document.getElementById('btnSubirComprobante');
        btnSubir.disabled = true;
        
        if (!fileInput.files.length) {
            mostrarError('Por favor, selecciona un archivo.');
            btnSubir.disabled = false;
            return;
        }
        
        // Obtener tanto OCRNumber como UserNumber
        const ocrNumber = codigoComprobante;
        let userNumber;
        
        // Si hay corrección manual, ese es el userNumber, sino es igual al OCR
        if (correccionManual && correccionManual.length === 7) {
            userNumber = correccionManual;
        } else {
            userNumber = ocrNumber;
        }

        const formData = new FormData(this);
        formData.append('ocr_number', ocrNumber);
        formData.append('user_number', userNumber);
        formData.append('estado_ocr', estadoOCR);

        try {
            const response = await fetch('/inscripcion/estudiante/comprobante/procesar-boleta', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (!response.ok) {
                let errorMsg = data.message || 'Error desconocido';
                if (response.status === 422 && data.errors) {
                    errorMsg = Object.values(data.errors).join('\n');
                }
                throw new Error(errorMsg);
            }
            
            // *** ADAPTACIÓN DEL IF DE ÉXITO ***
            // Cerrar el modal
            const modal = document.getElementById('SubirComprobantePago');
            const bootstrapModal = bootstrap.Modal.getInstance(modal);
            if (bootstrapModal) {
                bootstrapModal.hide();
            }
            
            // Solo guardar en sessionStorage si hubo corrección manual del usuario
            // (cuando el usuario ingresó manualmente el número de comprobante)
            if (correccionManual && correccionManual.length === 7) {
                // Solo aquí guardamos en sessionStorage porque hubo intervención manual del usuario
                sessionStorage.setItem('comprobanteSubidoExitoEstudiante', 'true');
                
                // Mostrar alerta de éxito con mensaje específico para corrección manual
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success';
                alertDiv.style.position = 'fixed';
                alertDiv.style.top = '20px';
                alertDiv.style.right = '20px';
                alertDiv.style.zIndex = '9999';
                alertDiv.style.padding = '15px 25px';
                alertDiv.style.borderRadius = '5px';
                alertDiv.style.backgroundColor = '#d4edda';
                alertDiv.style.color = '#155724';
                alertDiv.style.border = '1px solid #c3e6cb';
                alertDiv.innerHTML = '<strong>¡Éxito!</strong> Tu comprobante se pasará a revisión por parte del admin.';

                const closeBtn = document.createElement('button');
                closeBtn.className = 'btn-close';
                closeBtn.style.position = 'absolute';
                closeBtn.style.top = '5px';
                closeBtn.style.right = '10px';
                closeBtn.style.border = 'none';
                closeBtn.style.background = 'transparent';
                closeBtn.style.fontSize = '18px';
                closeBtn.style.cursor = 'pointer';
                closeBtn.innerHTML = '×';
                closeBtn.onclick = () => {
                    alertDiv.remove();
                    window.location.href = '/inscripcion/estudiante/imprimirFormularioInscripcion';
                };
                alertDiv.appendChild(closeBtn);

                document.body.appendChild(alertDiv);

                // Redirigir después de 3 segundos
                setTimeout(() => {
                    alertDiv.remove();
                    window.location.href = '/inscripcion/estudiante/imprimirFormularioInscripcion';
                }, 3000);
                
            } else {
                // Si fue procesamiento automático (OCR exitoso sin corrección manual)
                // NO guardamos en sessionStorage, solo mostramos mensaje breve
                const mensajeDiv = document.createElement('div');
                mensajeDiv.className = 'alert alert-success';
                mensajeDiv.textContent = 'Comprobante procesado exitosamente.';
                mensajeDiv.style.position = 'fixed';
                mensajeDiv.style.top = '20px';
                mensajeDiv.style.right = '20px';
                mensajeDiv.style.zIndex = '9999';
                mensajeDiv.style.padding = '10px 20px';
                mensajeDiv.style.borderRadius = '5px';
                mensajeDiv.style.backgroundColor = '#d4edda';
                mensajeDiv.style.color = '#155724';
                mensajeDiv.style.border = '1px solid #c3e6cb';
                document.body.appendChild(mensajeDiv);
                
                setTimeout(() => {
                    mensajeDiv.remove();
                    window.location.href = '/inscripcion/estudiante/imprimirFormularioInscripcion';
                }, 2000);
            }
            
        } catch (error) {
            console.error('Error:', error);
            mostrarError(error.message);
            btnSubir.disabled = false;
        }
    });
});


//JS que servira para cargar dinamicamente los selectores de categorias y areas
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
        eliminarTutor(tutorBlock);
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

// Función para eliminar un tutor con solicitud AJAX (actualizada)
function eliminarTutor(tutorBlock) {
    // Verificar si es el último tutor
    const tutorBlocks = document.querySelectorAll('.tutor-block');
    if (tutorBlocks.length <= 1) {
        alert('Debe haber al menos un tutor');
        return;
    }

    // Obtener el token del tutor
    const tokenInput = tutorBlock.querySelector('.tutor-token');
    const token = tokenInput ? tokenInput.value.trim() : tutorBlock.getAttribute('data-tutor-token');
    
    if (!token) {
        alert('No se pudo obtener el token del tutor. Asegúrese de que el token esté verificado.');
        return;
    }

    // Confirmar eliminación
    if (!confirm('¿Está seguro de que desea eliminar este tutor? Esta acción eliminará todas las inscripciones y datos relacionados.')) {
        return;
    }

    // Mostrar indicador de carga
    const eliminarBtn = tutorBlock.querySelector('.btn-eliminar-tutor');
    const originalContent = eliminarBtn.innerHTML;
    eliminarBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    eliminarBtn.disabled = true;

    // Realizar solicitud AJAX
    fetch(`/inscripcion/estudiante/informacion/eliminartutor/${encodeURIComponent(token)}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Eliminar el bloque del tutor del DOM
            tutorBlock.remove();
            tutorCount--;
            
            // Mostrar el botón de agregar tutor si hay menos de 2
            if (tutorCount < 2) {
                addTutorBtn.style.display = 'block';
            }
            
            // Renumerar los tutores restantes
            renumerarTutores();
            
            // Actualizar el estado del botón de agregar tutor
            updateAddTutorButtonState();
            
            // Mostrar mensaje de éxito
            alert('Tutor eliminado correctamente');
            
            // Log para debug
            console.log('Registros eliminados:', data.data?.registros_eliminados);
        } else {
            throw new Error(data.message || 'Error desconocido al eliminar el tutor');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        let errorMessage = 'Error al eliminar el tutor';
        if (error.message.includes('404')) {
            errorMessage = 'Tutor no encontrado. Puede que ya haya sido eliminado.';
        } else if (error.message.includes('403')) {
            errorMessage = 'No tiene permisos para eliminar este tutor.';
        } else if (error.message.includes('500')) {
            errorMessage = 'Error interno del servidor. Intente nuevamente.';
        }
        
        alert(errorMessage);
    })
    .finally(() => {
        // Restaurar botón original
        if (eliminarBtn) {
            eliminarBtn.innerHTML = originalContent;
            eliminarBtn.disabled = false;
        }
    });
}

// Función adicional para verificar si se puede eliminar un tutor
function verificarEliminacionTutor(token) {
    return fetch(`/inscripcion/estudiante/verificar-eliminacion-tutor/${encodeURIComponent(token)}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            return {
                puedeEliminar: data.puede_eliminar,
                registrosRelacionados: data.registros_relacionados
            };
        }
        throw new Error(data.message);
    })
    .catch(error => {
        console.error('Error al verificar eliminación:', error);
        return { puedeEliminar: false, error: error.message };
    });
}

// Función mejorada para eliminar tutor con verificación previa
function eliminarTutorConVerificacion(tutorBlock) {
    const tutorBlocks = document.querySelectorAll('.tutor-block');
    if (tutorBlocks.length <= 1) {
        alert('Debe haber al menos un tutor');
        return;
    }

    const tokenInput = tutorBlock.querySelector('.tutor-token');
    const token = tokenInput ? tokenInput.value.trim() : tutorBlock.getAttribute('data-tutor-token');
    
    if (!token) {
        alert('No se pudo obtener el token del tutor');
        return;
    }

    // Verificar si se puede eliminar
    verificarEliminacionTutor(token)
        .then(result => {
            if (result.puedeEliminar) {
                let mensaje = '¿Está seguro de que desea eliminar este tutor?';
                
                if (result.registrosRelacionados) {
                    const { inscripciones, areas } = result.registrosRelacionados;
                    if (inscripciones > 0 || areas > 0) {
                        mensaje += `\n\nEsto eliminará:\n- ${inscripciones} inscripción(es)\n- ${areas} área(s) asignada(s)`;
                    }
                }
                
                if (confirm(mensaje)) {
                    eliminarTutor(tutorBlock);
                }
            } else {
                alert('No se puede eliminar este tutor: ' + (result.error || 'Razón desconocida'));
            }
        })
        .catch(error => {
            console.error('Error en verificación:', error);
            // Proceder con eliminación básica si falla la verificación
            if (confirm('¿Está seguro de que desea eliminar este tutor?')) {
                eliminarTutor(tutorBlock);
            }
        });
}

// Función para renumerar tutores después de eliminar uno
function renumerarTutores() {
    const tutorBlocks = document.querySelectorAll('.tutor-block');
    tutorBlocks.forEach((block, index) => {
        const header = block.querySelector('.tutor-header h3');
        if (header) {
            header.textContent = `Tutor ${index + 1}`;
        }
        
        // Actualizar nombres de campos
        const areaSelects = block.querySelectorAll('.area-select');
        const categoriaSelects = block.querySelectorAll('.categoria-select');
        
        areaSelects.forEach((select, areaIndex) => {
            select.name = `tutor_areas_${index + 1}_${areaIndex + 1}`;
        });
        
        categoriaSelects.forEach((select, areaIndex) => {
            select.name = `tutor_categorias_${index + 1}_${areaIndex + 1}`;
        });
    });
}

// Función para inicializar la verificación de tokens
function initializeTokenVerification() {
    const tokenInputs = document.querySelectorAll('.tutor-token');
    tokenInputs.forEach(input => {
        input.addEventListener('input', function() {
            const button = this.closest('.token-verification-container').querySelector('.btn-verificar-token');
            if (this.value.trim().length >= 6) {
                button.style.display = 'inline-block';
            } else {
                button.style.display = 'none';
            }
        });
    });
}

// Función para validar token del tutor
function validateTutorToken(tokenInput) {
    const token = tokenInput.value.trim();
    if (token.length < 6) {
        alert('El token debe tener al menos 6 caracteres');
        return;
    }

    // Mostrar indicador de carga
    const button = tokenInput.closest('.token-verification-container').querySelector('.btn-verificar-token');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
    button.disabled = true;

    fetch('/inscripcion/estudiante/verificar-token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ token: token })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Token válido, actualizar información del tutor
            const tutorBlock = tokenInput.closest('.tutor-block');
            tutorBlock.setAttribute('data-tutor-token', token);
            
            // Actualizar información de delegación
            const delegacionElement = tutorBlock.querySelector('.tutor-delegacion');
            const delegacionInput = tutorBlock.querySelector('.idDelegacion-input');
            
            if (delegacionElement && delegacionInput) {
                delegacionElement.textContent = data.tutor.colegio.nombre;
                delegacionInput.value = data.tutor.colegio.id;
            }
            
            // Cargar áreas disponibles
            loadAreasForTutor(tutorBlock, data.tutor.areas);
            
            // Ocultar botón de verificar
            button.style.display = 'none';
            tokenInput.readOnly = true;
            tokenInput.style.backgroundColor = '#e9ecef';
            
            alert('Token verificado correctamente');
        } else {
            alert('Token inválido: ' + (data.message || 'Token no encontrado'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al verificar el token');
    })
    .finally(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

// Función para cargar áreas de un tutor
function loadAreasForTutor(tutorBlock, areas) {
    const areaSelects = tutorBlock.querySelectorAll('.area-select');
    
    areaSelects.forEach(select => {
        // Limpiar opciones existentes
        select.innerHTML = '<option value="">Seleccione un área</option>';
        
        // Agregar nuevas opciones
        areas.forEach(area => {
            const option = document.createElement('option');
            option.value = area.id;
            option.textContent = area.nombre;
            select.appendChild(option);
        });
    });
}

// Función para cargar categorías basadas en el área seleccionada
function loadCategorias(areaSelect) {
    const areaId = areaSelect.value;
    const categoriaSelect = areaSelect.closest('.info-row').querySelector('.categoria-select');
    
    if (!areaId) {
        categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
        return;
    }
    
    // Realizar petición AJAX para obtener categorías
    fetch(`/inscripcion/estudiante/categorias/${areaId}`)
    .then(response => response.json())
    .then(data => {
        categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
        
        if (data.success && data.categorias) {
            data.categorias.forEach(categoria => {
                const option = document.createElement('option');
                option.value = categoria.id;
                option.textContent = categoria.nombre;
                categoriaSelect.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.error('Error al cargar categorías:', error);
        categoriaSelect.innerHTML = '<option value="">Error al cargar categorías</option>';
    });
}

// Función para cargar grados basados en la categoría seleccionada
function loadGrados(categoriaSelect) {
    const categoriaId = categoriaSelect.value;
    const gradoSelect = document.getElementById('idGrado');
    
    if (!categoriaId) {
        return;
    }
    
    // Realizar petición AJAX para obtener grados compatibles
    fetch(`/inscripcion/estudiante/grados/${categoriaId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.grados) {
            // Filtrar opciones del select de grado
            const options = gradoSelect.querySelectorAll('option');
            options.forEach(option => {
                if (option.value === '') return; // Mantener opción vacía
                
                const isCompatible = data.grados.some(grado => grado.nombre === option.value);
                option.style.display = isCompatible ? 'block' : 'none';
                
                if (!isCompatible && option.selected) {
                    gradoSelect.value = '';
                }
            });
        }
    })
    .catch(error => {
        console.error('Error al cargar grados:', error);
    });
}

// Función para agregar un nuevo bloque de tutor
function addTutorBlock() {
    const newTutorHtml = `
        <div class="tutor-block">
            <div class="tutor-header">
                <h3>Tutor ${tutorCount}</h3>
                <button type="button" class="btn-eliminar-tutor" title="Eliminar tutor">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="input-grupo">
                <label>Token del Tutor</label>
                <div class="input-with-icon token-verification-container">
                    <input 
                        type="text" 
                        class="tutor-token" 
                        name="tutor_tokens[]"
                        placeholder="Token del Tutor" 
                        required
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
                            No verificado
                        </div>
                        <input 
                            type="hidden" 
                            class="idDelegacion-input" 
                            name="tutor_delegaciones[]"
                        >
                    </div>
                </div>
                
                <div class="areas-container">
                    <div class="area-block">
                        <div class="info-row">
                            <div class="info-group">
                                <label>Área</label>
                                <select 
                                    class="area-select" 
                                    name="tutor_areas_${tutorCount}_1" 
                                    required
                                >
                                    <option value="">Seleccione un área</option>
                                </select>
                            </div>
                            <div class="input-grupo">
                                <label>Categoría</label>
                                <select 
                                    class="categoria-select" 
                                    name="tutor_categorias_${tutorCount}_1" 
                                    required
                                >
                                    <option value="">Seleccione una categoría</option>
                                </select>
                            </div>
                            <button type="button" class="btn-eliminar-area" title="Eliminar área">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn-add-area">
                    <i class="fas fa-plus"></i> Agregar Área
                </button>
            </div>
        </div>
    `;
    
    tutorContainer.insertAdjacentHTML('beforeend', newTutorHtml);
    
    // Inicializar contador de áreas para el nuevo tutor
    areaCount[tutorCount] = 1;
    
    // Inicializar verificación de token para el nuevo tutor
    initializeTokenVerification();
}

// Función para agregar un nuevo bloque de área
function addAreaBlock(tutorBlock) {
    const tutorIndex = Array.from(tutorContainer.children).indexOf(tutorBlock) + 1;
    const areasContainer = tutorBlock.querySelector('.areas-container');
    const currentAreas = areasContainer.querySelectorAll('.area-block').length;
    
    if (currentAreas >= 2) {
        alert('Máximo 2 áreas por tutor');
        return;
    }
    
    const newAreaIndex = currentAreas + 1;
    
    const newAreaHtml = `
        <div class="area-block">
            <div class="info-row">
                <div class="info-group">
                    <label>Área</label>
                    <select 
                        class="area-select" 
                        name="tutor_areas_${tutorIndex}_${newAreaIndex}" 
                        required
                    >
                        <option value="">Seleccione un área</option>
                    </select>
                </div>
                <div class="input-grupo">
                    <label>Categoría</label>
                    <select 
                        class="categoria-select" 
                        name="tutor_categorias_${tutorIndex}_${newAreaIndex}" 
                        required
                    >
                        <option value="">Seleccione una categoría</option>
                    </select>
                </div>
                <button type="button" class="btn-eliminar-area" title="Eliminar área">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    areasContainer.insertAdjacentHTML('beforeend', newAreaHtml);
    
    // Actualizar contador de áreas
    areaCount[tutorIndex] = newAreaIndex;
    
    // Si el tutor ya está verificado, cargar las áreas disponibles
    const token = tutorBlock.getAttribute('data-tutor-token');
    if (token) {
        // Recargar áreas para el nuevo select
        loadAreasFromToken(tutorBlock, token);
    }
}

// Función para eliminar un bloque de área
function removeAreaBlock(areaBlock) {
    const tutorBlock = areaBlock.closest('.tutor-block');
    const areasContainer = tutorBlock.querySelector('.areas-container');
    const areas = areasContainer.querySelectorAll('.area-block');
    
    if (areas.length <= 1) {
        alert('Debe haber al menos un área por tutor');
        return;
    }
    
    areaBlock.remove();
    
    // Renumerar áreas restantes
    renumerarAreas(tutorBlock);
}

// Función para renumerar áreas después de eliminar una
function renumerarAreas(tutorBlock) {
    const tutorIndex = Array.from(tutorContainer.children).indexOf(tutorBlock) + 1;
    const areaBlocks = tutorBlock.querySelectorAll('.area-block');
    
    areaBlocks.forEach((block, index) => {
        const areaSelect = block.querySelector('.area-select');
        const categoriaSelect = block.querySelector('.categoria-select');
        
        if (areaSelect) {
            areaSelect.name = `tutor_areas_${tutorIndex}_${index + 1}`;
        }
        if (categoriaSelect) {
            categoriaSelect.name = `tutor_categorias_${tutorIndex}_${index + 1}`;
        }
    });
    
    // Actualizar contador
    areaCount[tutorIndex] = areaBlocks.length;
}

// Función para cargar áreas desde token
function loadAreasFromToken(tutorBlock, token) {
    fetch(`/inscripcion/estudiante/tutor-areas/${token}`)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.areas) {
            loadAreasForTutor(tutorBlock, data.areas);
        }
    })
    .catch(error => {
        console.error('Error al cargar áreas del tutor:', error);
    });
}

// Función de validación del formulario
function validateForm(event) {
    event.preventDefault();
    
    // Validar que todos los campos requeridos estén llenos
    const requiredFields = document.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.style.borderColor = '#dc3545';
        } else {
            field.style.borderColor = '';
        }
    });
    
    // Validar que cada tutor tenga al menos un área
    const tutorBlocks = document.querySelectorAll('.tutor-block');
    tutorBlocks.forEach(block => {
        const areaSelects = block.querySelectorAll('.area-select');
        let hasValidArea = false;
        
        areaSelects.forEach(select => {
            if (select.value) {
                hasValidArea = true;
            }
        });
        
        if (!hasValidArea) {
            isValid = false;
            alert('Cada tutor debe tener al menos un área seleccionada');
        }
    });
    
    if (isValid) {
        // Enviar formulario
        document.getElementById('inscriptionForm').submit();
    } else {
        alert('Por favor, complete todos los campos requeridos');
    }
    
    return false;
}
// Inicializar el contador de áreas para el primer tutor

});