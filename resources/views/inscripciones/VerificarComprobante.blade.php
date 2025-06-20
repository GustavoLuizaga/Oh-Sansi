


<x-app-layout>
<x-slot name="header">
    <div class="Comprobante-header">
        <h1><i class="fas fa-file-alt"></i> &nbsp; {{ __('Verificación de Comprobantes') }}</h1>
    </div>
</x-slot>

<style>
    :root {
        --primary-color: #1a365d;
        --primary-light: #2d5a87;
        --hover-color: #f8f9fa;
        --border-color: #dee2e6;
        --danger-color: #dc3545;
    }

    .Comprobante-header {
        background-color: var(--primary-color);
        color: white;
        padding: 0.5rem 2rem;
        border-radius: 0.375rem;
    }

    .Comprobante-header h1 {
        font-size: 1rem;
        margin: 0;
    }

    /* Modal ajustado más compacto */
    .modal-lg {
        max-width: 700px;
    }

    .modal-body {
        padding: 1rem;
    }

    /* Información compacta */
    .row.g-3 {
        margin: 0;
        margin-bottom: 1rem;
    }

    .col-md-4 .mb-3 {
        margin-bottom: 0.5rem !important;
    }

    .form-label {
        margin-bottom: 0.25rem;
        font-size: 0.8rem;
    }

    .fw-medium {
        margin-bottom: 0;
        font-size: 0.9rem;
    }

    .fw-bold.fs-4 {
        font-size: 1.1rem !important;
        margin-bottom: 0;
    }

    /* Contenedor de previsualización optimizado */
    .preview-container {
        max-height: 300px;
        overflow-y: auto;
        background-color: var(--hover-color);
        border-radius: 6px;
        padding: 8px;
        margin-bottom: 0.5rem;
    }

    .file-preview {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 150px;
    }

    /* Preview de imágenes más compacto */
    .image-preview img {
        border-radius: 4px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        object-fit: contain;
        max-width: 100%;
        max-height: 280px;
        height: auto;
    }

    /* PDF simplificado */
    .pdf-preview {
        text-align: center;
    }

    .pdf-preview canvas {
        max-width: 100%;
        max-height: 280px;
        height: auto;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        background: white;
    }

    /* Controles PDF simplificados */
    .pdf-controls {
        margin-top: 8px;
        text-align: center;
    }

    .pdf-controls button {
        margin: 0 3px;
        padding: 2px 8px;
        font-size: 0.8rem;
    }

    .page-info {
        margin: 5px 0;
        font-size: 0.8rem;
        color: #666;
    }

    /* Loading spinner más pequeño */
    .loading-spinner {
        display: inline-block;
        width: 30px;
        height: 30px;
        border: 3px solid #f3f3f3;
        border-radius: 50%;
        border-top: 3px solid var(--primary-color);
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Error de preview más compacto */
    .preview-error {
        color: var(--danger-color);
        text-align: center;
        padding: 15px;
    }

    .preview-error i {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }

    /* Áreas de decisión más compactas */
    #area-decision, #area-procesado {
        border-top: 1px solid var(--border-color);
        padding-top: 1rem;
        margin-top: 1rem;
    }

    #area-decision h5, #area-procesado h5 {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }

    .d-flex.gap-2 {
        gap: 0.5rem !important;
    }

    .btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }

    /* Badges más compactos */
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    .badge.px-3.py-2 {
        padding: 0.4rem 0.8rem !important;
    }

    /* Texto informativo más pequeño */
    .text-muted.small {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        margin-bottom: 0;
    }

    /* Motivo de rechazo compacto */
    #motivo-rechazo {
        margin-top: 0.75rem;
    }

    #motivo-rechazo .form-label {
        font-size: 0.85rem;
    }

    #motivo-rechazo textarea {
        font-size: 0.85rem;
        padding: 0.5rem;
    }

    /* Responsividad mejorada */
    @media (max-width: 768px) {
        .modal-lg {
            max-width: 95%;
            margin: 1rem auto;
        }
        
        .preview-container {
            max-height: 250px;
        }
        
        .image-preview img,
        .pdf-preview canvas {
            max-height: 230px;
        }
        
        .col-md-4 {
            margin-bottom: 0.5rem;
        }
    }
</style>

<div class="py-4">
    <div class="container">
        <div class="card mb-4">
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="estudiantes-tab" data-bs-toggle="tab" data-bs-target="#estudiantes" type="button" role="tab" aria-controls="estudiantes" aria-selected="true">Comprobantes de Estudiantes</button>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content" id="myTabContent">
                    <!-- Tab Estudiantes -->
                    <div class="tab-pane fade show active" id="estudiantes" role="tabpanel" aria-labelledby="estudiantes-tab">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                            <h3 class="fs-5 fw-semibold mb-3 mb-md-0">Verificar Comprobantes de Estudiantes</h3>
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Buscar estudiante...">
                                </div>
                                <select id="filtro-estado-estudiantes" class="form-select">
                                    <option value="todos">Todos los estados</option>
                                    <option value="pendiente">Pendientes</option>
                                    <option value="aprobado">Aprobados</option>
                                    <option value="rechazado">Rechazados</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">IDBoleta OrdenPago</th>
                                        <th scope="col">Estudiante</th>
                                        <th scope="col">Nombre del Archivo</th>
                                        <th scope="col">Fecha de Subida</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Nro. Comprobante Subido</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($boletas as $idBoleta => $grupo)
                                        <tr>
                                            <td>{{ $idBoleta }}</td>
                                            <td>
                                                {{ $grupo->pluck('nombre_completo')->unique()->implode(', ') }}
                                            </td>
                                            <!-- Nombre del archivo sin la ruta -->
                                            <td>{{ basename($grupo->first()->RutaComprobante) }}</td>
                                            <td>
                                                @if ($grupo->first()->fecha_actualizacion_verificacion)
                                                    {{ \Carbon\Carbon::parse($grupo->first()->fecha_actualizacion_verificacion)->format('d/m/Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $status = $grupo->first()->status;
                                                    switch (strtolower($status)) {
                                                        case 'pendiente':
                                                            $badgeClass = 'bg-warning text-dark';
                                                            $statusText = 'Pendiente';
                                                            break;
                                                        case 'aprobado':
                                                            $badgeClass = 'bg-success';
                                                            $statusText = 'Aprobado';
                                                            break;
                                                        case 'rechazado':
                                                            $badgeClass = 'bg-danger';
                                                            $statusText = 'Rechazado';
                                                            break;
                                                        default:
                                                            $badgeClass = 'bg-secondary';
                                                            $statusText = 'Desconocido';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                            </td>
                                            <td>{{ $grupo->first()->CodigoComprobante }}</td>
                                            <td>
                                                <button class="btn btn-primary btn-sm btn-revisar" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#revisar-modal" 
                                                        data-id="{{ $idBoleta }}"
                                                        data-estudiantes="{{ $grupo->pluck('nombre_completo')->unique()->implode(', ') }}"
                                                        data-archivo="{{ basename($grupo->first()->RutaComprobante) }}"
                                                        data-fecha="{{ $grupo->first()->fecha_actualizacion_verificacion ? \Carbon\Carbon::parse($grupo->first()->fecha_actualizacion_verificacion)->format('d/m/Y') : 'N/A' }}"
                                                        data-estado="{{ strtolower($grupo->first()->status) }}"
                                                        data-nro-comprobante="{{ $grupo->first()->CodigoComprobante ?? 'N/A' }}"
                                                        data-imagen="{{ route('comprobante.mostrar', $idBoleta) }}">
                                                    <i class="fas fa-eye me-1"></i> Revisar
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal para revisar comprobante -->
<div class="modal fade" id="revisar-modal" tabindex="-1" aria-labelledby="revisar-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revisar-modal-label">Revisar Comprobante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Primera sección: Datos del comprobante -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small">ID:</label>
                            <p class="fw-medium" id="modal-id">001</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Usuario:</label>
                            <p class="fw-medium" id="modal-usuario">Carlos Martínez</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Tipo de Usuario:</label>
                            <p class="fw-medium" id="modal-tipo">Estudiante</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Archivo:</label>
                            <p class="fw-medium" id="modal-archivo">comprobante_001.jpg</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Fecha de Subida:</label>
                            <p class="fw-medium" id="modal-fecha">12/05/2025</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Estado Actual:</label>
                            <p class="fw-medium" id="modal-estado">
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Nro. Comprobante subido por el usuario:</label>
                            <h1 class="fw-bold fs-4 text-primary" id="modal-nro-comprobante">1234567</h1>
                        </div>
                    </div>
                </div>

                <!-- Segunda sección: Vista previa del comprobante -->
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label text-muted small">Vista previa del comprobante:</label>
                        <div class="preview-container border rounded bg-light">
                            <!-- Indicador de tipo de archivo -->
                            <div class="text-center mb-2">
                                <span id="file-type-indicator" class="file-type-indicator d-none"></span>
                            </div>
                            
                            <!-- Área de carga/loading -->
                            <div id="preview-loading" class="file-preview d-none">
                                <div class="text-center">
                                    <div class="loading-spinner"></div>
                                    <p class="mt-2">Cargando archivo...</p>
                                </div>
                            </div>
                            
                            <!-- Preview para imágenes -->
                            <div id="image-preview-container" class="file-preview d-none">
                                <img id="preview-comprobante" 
                                    src="" 
                                    alt="Vista previa del comprobante" 
                                    class="img-fluid mx-auto d-block">
                            </div>
                            
                            <!-- Preview para PDFs -->
                            <div id="pdf-preview-container" class="file-preview d-none">
                                <div class="pdf-preview">
                                    <canvas id="pdf-canvas"></canvas>
                                    <div class="pdf-controls">
                                        <button type="button" id="prev-page" class="btn btn-sm btn-outline-primary" disabled>
                                            <i class="fas fa-chevron-left"></i> Anterior
                                        </button>
                                        <span class="page-info">
                                            Página <span id="current-page">1</span> de <span id="total-pages">1</span>
                                        </span>
                                        <button type="button" id="next-page" class="btn btn-sm btn-outline-primary" disabled>
                                            Siguiente <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    {{-- <div class="mt-2">
                                        <button type="button" id="zoom-in" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                        <button type="button" id="zoom-out" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-search-minus"></i>
                                        </button>
                                        <button type="button" id="zoom-reset" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-expand-arrows-alt"></i> Ajustar
                                        </button>
                                    </div> --}}
                                </div>
                            </div>
                            
                            <!-- Error de preview -->
                            <div id="preview-error" class="preview-error d-none">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>No se pudo cargar la vista previa del archivo</p>
                                <small class="text-muted">Verifique que el archivo sea válido</small>
                            </div>
                        </div>
                        <div class="text-muted small mt-2">
                            * Recuerda verificar que el número de comprobante (7 dígitos) sea claramente visible.
                        </div>
                    </div>
                </div>

                <!-- Área de decisión para comprobantes pendientes -->
                <div id="area-decision" class="border-top pt-3 mt-3">
                        <button id="aceptar-btn" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> ACEPTAR COMPROBANTE
                        </button>
                        <button id="rechazar-btn" class="btn btn-danger">
                            <i class="fas fa-times me-1"></i> RECHAZAR COMPROBANTE
                        </button>
                    
                    
                    <!-- Motivo de rechazo (inicialmente oculto) -->
                    <div id="motivo-rechazo" class="mt-3 d-none">
                        <label for="motivo" class="form-label">Motivo del rechazo:</label>
                        <textarea id="motivo" class="form-control" rows="3" placeholder="Explique el motivo del rechazo..."></textarea>
                        <div class="mt-3">
                            <button id="confirmar-rechazo" class="btn btn-danger">
                                <i class="fas fa-check me-1"></i> CONFIRMAR RECHAZO
                            </button>
                            <button id="cancelar-rechazo" class="btn btn-light ms-2">
                                CANCELAR
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Área de visualización para comprobantes ya procesados -->
                <div id="area-procesado" class="d-none border-top pt-3 mt-3">
                    <h5 class="fw-semibold mb-3">Estado del comprobante:</h5>
                    <div id="estado-procesado" class="mb-3">
                        <span class="badge bg-success px-3 py-2">Aprobado el 11/05/2025</span>
                    </div>
                    <div id="motivo-procesado" class="mb-3 d-none">
                        <h6 class="fw-medium mb-2">Motivo del rechazo:</h6>
                        <p class="bg-light p-3 rounded border">El comprobante no corresponde a un pago válido para esta inscripción.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts necesarios -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('revisar-modal');
    let idBoletaActual = null;
    
    // Elementos para preview
    const previewLoading = document.getElementById('preview-loading');
    const imagePreviewContainer = document.getElementById('image-preview-container');
    const pdfPreviewContainer = document.getElementById('pdf-preview-container');
    const previewError = document.getElementById('preview-error');
    const fileTypeIndicator = document.getElementById('file-type-indicator');
    const imgPreview = document.getElementById('preview-comprobante');
    const pdfCanvas = document.getElementById('pdf-canvas');
    
    // Variables para PDF
    let currentPDF = null;
    let currentPage = 1;
    let totalPages = 1;
    let currentScale = 1.2;
    
    // Token CSRF para las solicitudes AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        idBoletaActual = button.getAttribute('data-id');
        
        // Obtener datos de los atributos data-*
        const estudiantes = button.getAttribute('data-estudiantes');
        const archivo = button.getAttribute('data-archivo');
        const fecha = button.getAttribute('data-fecha');
        const estado = button.getAttribute('data-estado');
        const nroComprobante = button.getAttribute('data-nro-comprobante');
        let archivoSrc = button.getAttribute('data-imagen');
        
        console.log('Datos del modal:', {
            id: idBoletaActual,
            estudiantes,
            archivo,
            fecha,
            estado,
            nroComprobante,
            archivoSrc
        });
    
        // Actualizar contenido del modal
        modal.querySelector('#modal-id').textContent = idBoletaActual;
        modal.querySelector('#modal-usuario').textContent = estudiantes;
        modal.querySelector('#modal-tipo').textContent = 'Estudiante';
        modal.querySelector('#modal-archivo').textContent = archivo;
        modal.querySelector('#modal-fecha').textContent = fecha;
        modal.querySelector('#modal-nro-comprobante').textContent = nroComprobante || 'N/A';
        
        // Actualizar estado (badge)
        const estadoBadge = modal.querySelector('#modal-estado .badge');
        estadoBadge.className = 'badge ' + getBadgeClass(estado);
        estadoBadge.textContent = getEstadoText(estado);
        
        // ✅ MOSTRAR U OCULTAR LAS ÁREAS DE DECISIÓN SEGÚN EL ESTADO
        const areaDecision = document.getElementById('area-decision');
        const areaProcesado = document.getElementById('area-procesado');
        const motivoProcesado = document.getElementById('motivo-procesado');
        const estadoProcesado = document.getElementById('estado-procesado');
        
        console.log('Estado del comprobante:', estado); // Debug
        
        if (estado === 'pendiente') {
            console.log('Mostrando área de decisión'); // Debug
            areaDecision.classList.remove('d-none');
            areaProcesado.classList.add('d-none');
        } else {
            console.log('Mostrando área procesado'); // Debug
            areaDecision.classList.add('d-none');
            areaProcesado.classList.remove('d-none');
            
            const fechaTexto = fecha !== 'N/A' ? ` el ${fecha}` : '';
            if (estado === 'aprobado') {
                estadoProcesado.innerHTML = `<span class="badge bg-success px-3 py-2">Aprobado${fechaTexto}</span>`;
                motivoProcesado.classList.add('d-none');
            } else if (estado === 'rechazado') {
                estadoProcesado.innerHTML = `<span class="badge bg-danger px-3 py-2">Rechazado${fechaTexto}</span>`;
                motivoProcesado.classList.add('d-none');
            }
        }
        
        // Cargar preview del archivo
        loadFilePreview(archivoSrc, archivo);
    });
    
    // Función para cargar preview del archivo
    async function loadFilePreview(fileSrc, fileName) {
        // Resetear preview
        resetPreview();
        
        if (!fileSrc) {
            showPreviewError();
            return;
        }
        
        // Mostrar loading
        showLoading();
        
        // Determinar tipo de archivo por extensión
        const fileExtension = getFileExtension(fileName);
        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension);
        const isPDF = fileExtension === 'pdf';
        
        try {
            if (isImage) {
                await loadImagePreview(fileSrc, fileName);
            } else if (isPDF) {
                await loadPDFPreview(fileSrc, fileName);
            } else {
                showPreviewError('Formato de archivo no soportado');
            }
        } catch (error) {
            console.error('Error al cargar preview:', error);
            showPreviewError('Error al cargar el archivo');
        }
    }
    
    // Función para cargar preview de imagen
    function loadImagePreview(imageSrc, fileName) {
        return new Promise((resolve, reject) => {
            // Agregar timestamp para evitar caché
            const separator = imageSrc.includes('?') ? '&' : '?';
            const srcWithTimestamp = imageSrc + separator + 'v=' + Date.now();
            
            const tempImg = new Image();
            
            tempImg.onload = function() {
                imgPreview.src = this.src;
                imgPreview.alt = "Comprobante " + fileName;
                
                showFileTypeIndicator('IMAGE', 'Imagen');
                showImagePreview();
                resolve();
            };
            
            tempImg.onerror = function() {
                reject(new Error('Error al cargar imagen'));
            };
            
            tempImg.src = srcWithTimestamp;
        });
    }
    
    // Función para cargar preview de PDF
    async function loadPDFPreview(pdfSrc, fileName) {
        try {
            // Agregar timestamp para evitar caché
            const separator = pdfSrc.includes('?') ? '&' : '?';
            const srcWithTimestamp = pdfSrc + separator + 'v=' + Date.now();
            
            // Cargar PDF
            const loadingTask = pdfjsLib.getDocument(srcWithTimestamp);
            currentPDF = await loadingTask.promise;
            totalPages = currentPDF.numPages;
            currentPage = 1;
            
            // Renderizar primera página
            await renderPDFPage(currentPage);
            
           
            showPDFPreview();
            updatePDFControls();
            
        } catch (error) {
            console.error('Error al cargar PDF:', error);
            throw error;
        }
    }
    
    // Función para renderizar página de PDF
    async function renderPDFPage(pageNum) {
        if (!currentPDF) return;
        
        try {
            const page = await currentPDF.getPage(pageNum);
            const viewport = page.getViewport({ scale: currentScale });
            
            // Configurar canvas
            pdfCanvas.width = viewport.width;
            pdfCanvas.height = viewport.height;
            
            // Renderizar
            const renderContext = {
                canvasContext: pdfCanvas.getContext('2d'),
                viewport: viewport
            };
            
            await page.render(renderContext).promise;
            currentPage = pageNum;
            updatePDFControls();
            
        } catch (error) {
            console.error('Error al renderizar página PDF:', error);
            throw error;
        }
    }
    
    // Funciones de utilidad para preview
    function resetPreview() {
        hideAllPreviews();
        if (fileTypeIndicator) {
            fileTypeIndicator.classList.add('d-none');
        }
        currentPDF = null;
        currentPage = 1;
        totalPages = 1;
        currentScale = 1.2;
    }
    
    function hideAllPreviews() {
        if (previewLoading) previewLoading.classList.add('d-none');
        if (imagePreviewContainer) imagePreviewContainer.classList.add('d-none');
        if (pdfPreviewContainer) pdfPreviewContainer.classList.add('d-none');
        if (previewError) previewError.classList.add('d-none');
    }
    
    function showLoading() {
        hideAllPreviews();
        if (previewLoading) previewLoading.classList.remove('d-none');
    }
    
    function showImagePreview() {
        hideAllPreviews();
        if (imagePreviewContainer) imagePreviewContainer.classList.remove('d-none');
    }
    
    function showPDFPreview() {
        hideAllPreviews();
        if (pdfPreviewContainer) pdfPreviewContainer.classList.remove('d-none');
    }
    
    function showPreviewError(message = 'No se pudo cargar la vista previa del archivo') {
        hideAllPreviews();
        if (previewError) {
            const errorText = previewError.querySelector('p');
            if (errorText) errorText.textContent = message;
            previewError.classList.remove('d-none');
        }
    }
    
    function showFileTypeIndicator(type, label) {
        if (fileTypeIndicator) {
            fileTypeIndicator.textContent = label;
            fileTypeIndicator.className = `file-type-indicator file-type-${type.toLowerCase()}`;
            fileTypeIndicator.classList.remove('d-none');
        }
    }
    
    function getFileExtension(fileName) {
        return fileName.split('.').pop().toLowerCase();
    }
    
    function updatePDFControls() {
        const currentPageEl = document.getElementById('current-page');
        const totalPagesEl = document.getElementById('total-pages');
        const prevPageBtn = document.getElementById('prev-page');
        const nextPageBtn = document.getElementById('next-page');
        
        if (currentPageEl) currentPageEl.textContent = currentPage;
        if (totalPagesEl) totalPagesEl.textContent = totalPages;
        
        if (prevPageBtn) prevPageBtn.disabled = currentPage <= 1;
        if (nextPageBtn) nextPageBtn.disabled = currentPage >= totalPages;
    }
    
    // Event listeners para controles de PDF
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const zoomInBtn = document.getElementById('zoom-in');
    const zoomOutBtn = document.getElementById('zoom-out');
    const zoomResetBtn = document.getElementById('zoom-reset');
    
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', async () => {
            if (currentPage > 1) {
                await renderPDFPage(currentPage - 1);
            }
        });
    }
    
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', async () => {
            if (currentPage < totalPages) {
                await renderPDFPage(currentPage + 1);
            }
        });
    }
    
    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', async () => {
            currentScale += 0.2;
            await renderPDFPage(currentPage);
        });
    }
    
    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', async () => {
            if (currentScale > 0.4) {
                currentScale -= 0.2;
                await renderPDFPage(currentPage);
            }
        });
    }
    
    if (zoomResetBtn) {
        zoomResetBtn.addEventListener('click', async () => {
            currentScale = 1.2;
            await renderPDFPage(currentPage);
        });
    }
    
    // Limpiar modal al cerrarse
    modal.addEventListener('hidden.bs.modal', function() {
        resetPreview();
        idBoletaActual = null;
        console.log('Modal cerrado y limpiado');
    });
    
    // ✅ EVENTO PARA EL BOTÓN DE ACEPTAR COMPROBANTE
    const aceptarBtn = document.getElementById('aceptar-btn');
    if (aceptarBtn) {
        aceptarBtn.addEventListener('click', function() {
            console.log('Botón aceptar clickeado, ID:', idBoletaActual); // Debug
            if (!idBoletaActual) return;
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Confirmar aprobación?',
                    text: "Esta acción aprobará el comprobante para todos los estudiantes asociados.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, aprobar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        aprobarComprobante(idBoletaActual);
                    }
                });
            } else {
                if (confirm('¿Está seguro de aprobar este comprobante?')) {
                    aprobarComprobante(idBoletaActual);
                }
            }
        });
    }
    
    // ✅ EVENTO PARA EL BOTÓN DE RECHAZAR COMPROBANTE
    const rechazarBtn = document.getElementById('rechazar-btn');
    if (rechazarBtn) {
        rechazarBtn.addEventListener('click', function() {
            console.log('Botón rechazar clickeado, ID:', idBoletaActual); // Debug
            if (!idBoletaActual) return;
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Confirmar rechazo?',
                    text: "Esta acción rechazará el comprobante para todos los estudiantes asociados.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, rechazar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        rechazarComprobante(idBoletaActual);
                    }
                });
            } else {
                if (confirm('¿Está seguro de rechazar este comprobante?')) {
                    rechazarComprobante(idBoletaActual);
                }
            }
        });
    }
    
    // ✅ FUNCIÓN PARA APROBAR COMPROBANTE MEDIANTE AJAX
    function aprobarComprobante(idBoleta) {
        const aceptarBtn = document.getElementById('aceptar-btn');
        const rechazarBtn = document.getElementById('rechazar-btn');
        
        if (!aceptarBtn || !rechazarBtn) return;
        
        const originalText = aceptarBtn.innerHTML;
        
        aceptarBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Procesando...';
        aceptarBtn.disabled = true;
        rechazarBtn.disabled = true;
        
        fetch(`/aprobar-comprobante/${idBoleta}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                modalInstance.hide();
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¡Comprobante aprobado!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.message);
                    window.location.reload();
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ha ocurrido un error al procesar la solicitud.');
        })
        .finally(() => {
            aceptarBtn.innerHTML = originalText;
            aceptarBtn.disabled = false;
            rechazarBtn.disabled = false;
        });
    }
    
    // ✅ FUNCIÓN PARA RECHAZAR COMPROBANTE MEDIANTE AJAX
    function rechazarComprobante(idBoleta) {
        const aceptarBtn = document.getElementById('aceptar-btn');
        const rechazarBtn = document.getElementById('rechazar-btn');
        
        if (!aceptarBtn || !rechazarBtn) return;
        
        const originalText = rechazarBtn.innerHTML;
        
        rechazarBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Procesando...';
        rechazarBtn.disabled = true;
        aceptarBtn.disabled = true;
        
        fetch(`/rechazar-comprobante/${idBoleta}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                modalInstance.hide();
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¡Comprobante rechazado!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.message);
                    window.location.reload();
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ha ocurrido un error al procesar la solicitud.');
        })
        .finally(() => {
            rechazarBtn.innerHTML = originalText;
            rechazarBtn.disabled = false;
            aceptarBtn.disabled = false;
        });
    }
    
    // ✅ FUNCIONES AUXILIARES CORREGIDAS
    function getBadgeClass(estado) {
        switch (estado) {
            case 'pendiente': return 'bg-warning text-dark';
            case 'aprobado': return 'bg-success';
            case 'rechazado': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }
    
    function getEstadoText(estado) {
        switch (estado) {
            case 'pendiente': return 'Pendiente';
            case 'aprobado': return 'Aprobado';
            case 'rechazado': return 'Rechazado';
            default: return 'Desconocido';
        }
    }
    
    // ✅ FILTROS DE BÚSQUEDA (SI EXISTEN EN LA PÁGINA)
    const filtroInput = document.querySelector('input[placeholder="Buscar estudiante..."]');
    if (filtroInput) {
        filtroInput.addEventListener('keyup', function() {
            const texto = this.value.toLowerCase();
            const tabla = document.querySelector('table');
            if (tabla) {
                const filas = tabla.querySelectorAll('tbody tr');
                
                filas.forEach(fila => {
                    const nombreEstudiante = fila.querySelector('td:nth-child(2)');
                    if (nombreEstudiante) {
                        if (nombreEstudiante.textContent.toLowerCase().includes(texto)) {
                            fila.style.display = '';
                        } else {
                            fila.style.display = 'none';
                        }
                    }
                });
            }
        });
    }
    
    // Filtro por estado
    const filtroEstado = document.getElementById('filtro-estado-estudiantes');
    if (filtroEstado) {
        filtroEstado.addEventListener('change', function() {
            const valor = this.value;
            const tabla = document.querySelector('table');
            if (tabla) {
                const filas = tabla.querySelectorAll('tbody tr');
                
                filas.forEach(fila => {
                    const estadoBadge = fila.querySelector('td:nth-child(5) .badge');
                    if (estadoBadge) {
                        const estadoTexto = estadoBadge.textContent.toLowerCase();
                        
                        if (valor === 'todos' || estadoTexto === valor) {
                            fila.style.display = '';
                        } else {
                            fila.style.display = 'none';
                        }
                    }
                });
            }
        });
    }
});
</script>

    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Configurar PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>

</x-app-layout>