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

/*JS DEL MODAL */
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
    
    // Función para manejar archivos
function handleFiles(files) {
    feedbackArea.style.display = 'none';
    
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
            } else if (file.type.startsWith('image/')) {
                // Crear vista previa para imágenes
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgElement.src = e.target.result;
                    imagePreview.style.display = 'block';
                    pdfPreview.style.display = 'none';
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
    });
    
    // Validar formulario
    document.getElementById('comprobantePagoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!fileInput.files.length) {
            feedbackArea.textContent = 'Por favor, selecciona un archivo.';
            feedbackArea.style.display = 'block';
            return;
        }
        
        // Aquí iría la lógica para enviar el formulario
        //alert('¡Comprobante subido correctamente!');
        
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('SubirComprobantePago'));
        modal.hide();
    });
});