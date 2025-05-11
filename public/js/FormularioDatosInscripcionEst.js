document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert').forEach(alert => {
        // Agregar botón de cierre
        const closeBtn = document.createElement('button');
        closeBtn.className = 'alert-close';
        closeBtn.innerHTML = '×';
        closeBtn.onclick = () => alert.remove();
        alert.appendChild(closeBtn);

        // Cierre automático después de 5s
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});



document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('comprobantePagoFile');
    const dropArea = document.querySelector('.file-drop-area');
    const filePreview = document.querySelector('.file-preview');
    const imagePreview = document.querySelector('.image-preview');
    const pdfPreview = document.querySelector('.pdf-preview');
    const imgElement = document.querySelector('.img-preview');
    const fileName = document.querySelector('.file-name');
    const removeBtn = document.querySelector('.btn-remove-file');
    const feedbackArea = document.querySelector('.file-feedback');
    
    // Variable global para el número de comprobante
    let codigoComprobante = null;
    let estadoOCR = 0; // 0 = no procesado, 1 = éxito, 2 = error

    // Función para mostrar errores
    function mostrarError(mensaje) {
        feedbackArea.textContent = mensaje;
        feedbackArea.style.display = 'block';
    }

    // Función para procesar OCR
    async function processImageWithOCR(imageUrl) {
        console.log("Iniciando OCR...");
        
        try {
            const worker = await Tesseract.createWorker('spa');
            const { data: { text } } = await worker.recognize(imageUrl);
            console.log("Texto extraído:", text);
            
            // Buscar el número de comprobante en los primeros 95 caracteres
            const textoBusqueda = text.substring(0, 95);
            const regex1 = /(Nro|No|Numero?)[\s:]*([0-9]{7})/i;
            const regex2 = /[0-9]{7}/;
            
            let match = textoBusqueda.match(regex1) || textoBusqueda.match(regex2);
            
            if (match) {
                // Extraer solo los números (eliminar posibles espacios o guiones)
                const numero = match[2] ? match[2] : match[0];
                codigoComprobante = parseInt(numero.replace(/\D/g, ''));
                estadoOCR = 1;
                console.log("Número de comprobante detectado:", codigoComprobante);
            } else {
                estadoOCR = 2;
                mostrarError("No se encontró el número de comprobante. Asegúrese que el texto 'Nro. XXXXXXX' sea visible en la imagen.");
                codigoComprobante = null;
            }
            
            await worker.terminate();
        } catch (error) {
            console.error("Error en OCR:", error);
            estadoOCR = 2;
            mostrarError("Error al procesar la imagen. Vuelva a subirla, asegurando que el texto sea claro.");
            codigoComprobante = null;
        }
    }

    // Función para manejar archivos
    async function handleFiles(files) {
        feedbackArea.style.display = 'none';
        estadoOCR = 0;
        codigoComprobante = null;
        
        if (files.length > 0) {
            const file = files[0];
            const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            const maxSize = 5 * 1024 * 1024; // 5MB

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
            
            // Mostrar nombre del archivo
            fileName.textContent = file.name;
            
            // Ocultar el área de drop y mostrar la previsualización
            dropArea.style.display = 'none';
            filePreview.style.display = 'block';
            
            // Mostrar previsualización según tipo de archivo
            if (file.type === 'application/pdf') {
                pdfPreview.style.display = 'block';
                imagePreview.style.display = 'none';
                mostrarError("Los archivos PDF no son soportados para OCR. Suba una imagen JPG o PNG.");
            } else if (file.type.startsWith('image/')) {
                // Crear vista previa para imágenes
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgElement.src = e.target.result;
                    imagePreview.style.display = 'block';
                    pdfPreview.style.display = 'none';
                    
                    // Procesar OCR
                    processImageWithOCR(e.target.result);
                };
                reader.readAsDataURL(file);
            }
        }
    }
    
    // Evento para cambio en input file
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });
    
    // Eventos de arrastrar y soltar
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
    
    // Eliminar archivo
    removeBtn.addEventListener('click', function() {
        fileInput.value = '';
        filePreview.style.display = 'none';
        imagePreview.style.display = 'none';
        pdfPreview.style.display = 'none';
        dropArea.style.display = 'block';
        feedbackArea.style.display = 'none';
        codigoComprobante = null;
        estadoOCR = 0;
    });
    
    // Validar formulario
    document.getElementById('comprobantePagoForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!fileInput.files.length) {
            mostrarError('Por favor, selecciona un archivo.');
            return;
        }
        
        // Esperar a que termine el OCR si es una imagen
        if (fileInput.files[0].type.startsWith('image/') && estadoOCR === 0) {
            mostrarError('Espere mientras procesamos la imagen...');
            return;
        }
        
        if (estadoOCR === 2) {
            mostrarError('No se pudo leer el número de comprobante. Suba una imagen más clara.');
            return;
        }
        
        // Crear FormData y agregar los datos
        const formData = new FormData(this);
        if (codigoComprobante) {
            formData.append('codigo_comprobante', codigoComprobante);
        }
        formData.append('estado_ocr', estadoOCR);
        
        // Enviar al servidor (usando Fetch API)
        try {
            // Mostrar mensaje de carga await fetch('/inscripcion/estudiante/comprobante/procesar-boleta', {
            const response = await fetch(window.location.origin + '/inscripcion/estudiante/comprobante/procesar-boleta', {
                method: 'POST',
                body: formData, // Ya incluye todos los campos necesarios
                headers: {
                    'Accept': 'application/json' // Para recibir respuestas JSON
                }
            });
            
            const result = await response.json();
            
            if (response.ok) {
                // Cerrar modal y mostrar mensaje de éxito
                bootstrap.Modal.getInstance(document.getElementById('SubirComprobantePago')).hide();
                alert(result.message || 'Comprobante subido correctamente');
                location.reload(); // Recargar para ver los cambios
            } else {
                mostrarError(result.message || 'Error al subir el comprobante');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarError('Error de conexión con el servidor');
        }
    });
});