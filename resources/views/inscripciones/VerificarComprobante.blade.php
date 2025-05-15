<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verificación de Comprobantes') }}
        </h2>
    </x-slot>
    
    <style>
        /* Estilos personalizados para complementar Tailwind */
        .active {
            border-bottom: 2px solid #4f46e5;
            color: #4f46e5;
        }
        
        .tab-transition {
            transition: all 0.3s ease-in-out;
        }
        
        .modal-fade {
            transition: opacity 0.3s ease-in-out;
        }
        
        .hover-zoom:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }
        
        .img-preview {
            cursor: pointer;
        }
        
        .img-preview:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary {
            background-color: #4f46e5;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .btn-primary:hover {
            background-color: #4338ca;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
        }
        
        .badge-pendiente {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .badge-aprobado {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .badge-rechazado {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        .badge-dudoso {
            background-color: #ffedd5;
            color: #9a3412;
        }
        
        .table-hover tr:hover {
            background-color: #f9fafb;
        }
        
        .table-striped tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .table-container {
            overflow-x: auto;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
        }
        
        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }
        
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
        }
        
        .modal-content {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 0.5rem;
            animation: modal-pop 0.3s ease-out forwards;
        }
        
        @keyframes modal-pop {
            0% {
                opacity: 0;
                transform: scale(0.95);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-icon svg {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.5rem;
        }
        
        .tooltip {
            position: relative;
            display: inline-block;
        }
        
        .tooltip .tooltip-text {
            visibility: hidden;
            width: 120px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
        
        /* Estados de aprobación con mejor visibilidad */
        .estado-pendiente {
            background-color: #fef3c7;
            color: #92400e;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            border: 1px solid #f59e0b;
        }
        
        .estado-aprobado {
            background-color: #dcfce7;
            color: #166534;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            border: 1px solid #10b981;
        }
        
        .estado-rechazado {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            border: 1px solid #ef4444;
        }
        
        .estado-dudoso {
            background-color: #ffedd5;
            color: #9a3412;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            border: 1px solid #f97316;
        }
        
        /* Animaciones suaves para los botones */
        .btn {
            transition: all 0.2s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>

<main class="py-4">
        <div class="container">
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Resumen de estadísticas -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="stats-icon bg-primary bg-opacity-10 text-primary me-3">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-0 small">Total Comprobantes</p>
                                            <h4 class="fw-bold mb-0">6</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="stats-icon bg-warning bg-opacity-10 text-warning me-3">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-0 small">Pendientes</p>
                                            <h4 class="fw-bold mb-0">3</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="stats-icon bg-success bg-opacity-10 text-success me-3">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-0 small">Aprobados</p>
                                            <h4 class="fw-bold mb-0">2</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="stats-icon bg-danger bg-opacity-10 text-danger me-3">
                                            <i class="fas fa-times"></i>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-0 small">Rechazados</p>
                                            <h4 class="fw-bold mb-0">1</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="estudiantes-tab" data-bs-toggle="tab" data-bs-target="#estudiantes" type="button" role="tab" aria-controls="estudiantes" aria-selected="true">Comprobantes de Estudiantes</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tutores-tab" data-bs-toggle="tab" data-bs-target="#tutores" type="button" role="tab" aria-controls="tutores" aria-selected="false">Comprobantes de Tutores</button>
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
                                            <th scope="col">ID</th>
                                            <th scope="col">Estudiante</th>
                                            <th scope="col">Nombre del Archivo</th>
                                            <th scope="col">Fecha de Subida</th>
                                            <th scope="col">Estado</th>
                                            <th scope="col">Nro. Comprobante</th>
                                            <th scope="col">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Estudiante 1 - Pendiente -->
                                        <tr>
                                            <td>001</td>
                                            <td>Carlos Martínez</td>
                                            <td>comprobante_001.jpg</td>
                                            <td>12/05/2025</td>
                                            <td>
                                                <span class="badge badge-pendiente">Pendiente</span>
                                            </td>
                                            <td>1234567</td>
                                            <td>
                                                <button class="btn btn-primary btn-sm btn-revisar" data-bs-toggle="modal" data-bs-target="#revisar-modal" data-id="001" data-tipo="estudiante">
                                                    <i class="fas fa-eye me-1"></i> Revisar
                                                </button>
                                            </td>
                                        </tr>
                                        <!-- Estudiante 2 - Pendiente (Lectura dudosa OCR) -->
                                        <tr>
                                            <td>002</td>
                                            <td>Ana López</td>
                                            <td>pago_ana.pdf</td>
                                            <td>12/05/2025</td>
                                            <td>
                                                <span class="badge badge-dudoso">Pendiente</span>
                                            </td>
                                            <td>¿7854321?</td>
                                            <td>
                                                <button class="btn btn-primary btn-sm btn-revisar" data-bs-toggle="modal" data-bs-target="#revisar-modal" data-id="002" data-tipo="estudiante">
                                                    <i class="fas fa-eye me-1"></i> Revisar
                                                </button>
                                            </td>
                                        </tr>
                                        <!-- Estudiante 3 - Aprobado -->
                                        <tr>
                                            <td>003</td>
                                            <td>María Sánchez</td>
                                            <td>comprobante_maria.png</td>
                                            <td>11/05/2025</td>
                                            <td>
                                                <span class="badge badge-aprobado">Aprobado</span>
                                            </td>
                                            <td>5467890</td>
                                            <td>
                                                <button class="btn btn-secondary btn-sm btn-revisar" data-bs-toggle="modal" data-bs-target="#revisar-modal" data-id="003" data-tipo="estudiante">
                                                    <i class="fas fa-eye me-1"></i> Ver detalle
                                                </button>
                                            </td>
                                        </tr>
                                        <!-- Estudiante 4 - Rechazado -->
                                        <tr>
                                            <td>004</td>
                                            <td>Pablo Gómez</td>
                                            <td>recibo_pago.jpg</td>
                                            <td>10/05/2025</td>
                                            <td>
                                                <span class="badge badge-rechazado">Rechazado</span>
                                            </td>
                                            <td>9876543</td>
                                            <td>
                                                <button class="btn btn-secondary btn-sm btn-revisar" data-bs-toggle="modal" data-bs-target="#revisar-modal" data-id="004" data-tipo="estudiante">
                                                    <i class="fas fa-eye me-1"></i> Ver detalle
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <p class="text-muted small">Mostrando <span class="fw-medium">1</span> a <span class="fw-medium">4</span> de <span class="fw-medium">4</span> resultados</p>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-sm">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#">Siguiente</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>

                        <!-- Tab Tutores -->
                        <div class="tab-pane fade" id="tutores" role="tabpanel" aria-labelledby="tutores-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3 class="fs-5 fw-semibold">Verificar Comprobantes de Tutores</h3>
                                <select id="filtro-estado-tutores" class="form-select" style="width: auto;">
                                    <option value="todos">Todos los estados</option>
                                    <option value="pendiente">Pendientes</option>
                                    <option value="aprobado">Aprobados</option>
                                    <option value="rechazado">Rechazados</option>
                                </select>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Tutor</th>
                                            <th scope="col">Nombre del Archivo</th>
                                            <th scope="col">Fecha de Subida</th>
                                            <th scope="col">Estado</th>
                                            <th scope="col">Nro. Comprobante</th>
                                            <th scope="col">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Tutor 1 - Pendiente -->
                                        <tr>
                                            <td>T001</td>
                                            <td>Prof. Laura Ramírez</td>
                                            <td>comprobante_laura.pdf</td>
                                            <td>13/05/2025</td>
                                            <td>
                                                <span class="badge badge-pendiente">Pendiente</span>
                                            </td>
                                            <td>8765432</td>
                                            <td>
                                                <button class="btn btn-primary btn-sm btn-revisar" data-bs-toggle="modal" data-bs-target="#revisar-modal" data-id="T001" data-tipo="tutor">
                                                    <i class="fas fa-eye me-1"></i> Revisar
                                                </button>
                                            </td>
                                        </tr>
                                        <!-- Tutor 2 - Aprobado -->
                                        <tr>
                                            <td>T002</td>
                                            <td>Prof. Roberto Méndez</td>
                                            <td>pago_roberto.jpg</td>
                                            <td>10/05/2025</td>
                                            <td>
                                                <span class="badge badge-aprobado">Aprobado</span>
                                            </td>
                                            <td>2345678</td>
                                            <td>
                                                <button class="btn btn-secondary btn-sm btn-revisar" data-bs-toggle="modal" data-bs-target="#revisar-modal" data-id="T002" data-tipo="tutor">
                                                    <i class="fas fa-eye me-1"></i> Ver detalle
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal para revisar comprobante -->
    <div class="modal fade" id="revisar-modal" tabindex="-1" aria-labelledby="revisar-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="revisar-modal-label">Revisar Comprobante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
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
                                    <span class="badge badge-pendiente">Pendiente</span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Nro. Comprobante Detectado (OCR):</label>
                                <p class="fw-medium" id="modal-nro-comprobante">1234567</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Vista previa del comprobante:</label>
                            <div class="preview-container">
                                <img id="preview-comprobante" src="/api/placeholder/400/320" alt="Vista previa del comprobante" />
                            </div>
                            <p class="text-muted small mt-2">* Recuerda verificar que el número de comprobante (7 dígitos) sea claramente visible en la esquina superior derecha.</p>
                        </div>
                    </div>

                    <!-- Área de decisión para comprobantes pendientes -->
                    <div id="area-decision" class="border-top pt-3 mt-3">
                        <h5 class="fw-semibold mb-3">Decisión:</h5>
                        <div class="d-flex flex-column flex-md-row gap-2">
                            <button id="aceptar-btn" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> ACEPTAR COMPROBANTE
                            </button>
                            <button id="rechazar-btn" class="btn btn-danger">
                                <i class="fas fa-times me-1"></i> RECHAZAR COMPROBANTE
                            </button>
                        </div>
                        
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
                            <span class="badge badge-aprobado px-3 py-2">Aprobado el 11/05/2025</span>
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
</x-app-layout>
