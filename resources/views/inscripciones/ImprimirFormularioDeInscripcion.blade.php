@push('styles')
    <link rel="stylesheet" href="{{ asset('css/inscripcion/FormularioDatosInscripcionEst.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/FormularioDatosInscripcionEst.js') }}"></script>
@endpush
<x-app-layout>
    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-1">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Header Section -->
    <div class="estudiantes-header py-2">
        <h1><i class="fas fa-user-plus"></i> Datos de Inscripción del Postulante</h1>
    </div>

   
        <!-- IDs ocultos para uso en JS -->
        <div id="data-ids" 
        data-estudiante-id="{{ $ids['estudiante_id'] }}"
        data-tutor-id="{{ $ids['tutor_id'] }}"
        data-inscripcion-id="{{ $ids['inscripcion_id'] }}"
        data-convocatoria-id="{{ $ids['convocatoria_id'] }}"
        data-delegacion-id="{{ $ids['delegacion_id'] }}"
        data-grado-id="{{ $ids['grado_id'] }}"
        style="display: none;">
    </div>

    <div class="container-fluid mt-3 px-3">
        <!-- PRIMERA SECCIÓN: Información General y Datos del Estudiante -->
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body p-0">
                <div class="row g-0">
                    <!-- Información General -->
                    <div class="col-md-6 pe-md-2">
                        <div class="info-section h-100">
                            <div class="section-header bg-gradient-primary text-white py-2 px-3 rounded-top">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información General</h5>
                            </div>
                            <div class="section-content p-3 bg-white rounded-bottom border">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span class="info-label">Convocatoria:</span>
                                        <span class="info-value">{{ $convocatoria['nombre'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Fecha límite:</span>
                                        <span class="info-value">{{ $convocatoria['fecha_limite'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Método pago:</span>
                                        <span class="info-value">{{ $convocatoria['metodo_pago'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Contacto:</span>
                                        <span class="info-value">{{ $inscripcion['numero_contacto'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Código boleta:</span>
                                        <span class="info-value {{ $codigoOrden ? 'text-success fw-bold' : 'text-warning' }}">
                                            {{ $codigoOrden ?? 'Genera la Boleta' }}
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Fecha generación Boleta:</span>
                                        <span class="info-value">{{ $fechaGeneracion }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Fecha Vencimiento Boleta:</span>
                                        <span class="info-value">{{ $fechaVencimiento }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Estado:</span>
                                        <span class="badge bg-warning text-dark py-2 px-5">{{ strtoupper($inscripcion['status']) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Datos del Estudiante -->
                    <div class="col-md-6 ps-md-2 mt-3 mt-md-0">
                        <div class="info-section h-100">
                            <div class="section-header bg-gradient-info text-white py-2 px-3 rounded-top">
                                <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Datos del Estudiante</h5>
                            </div>
                            <div class="section-content p-3 bg-white rounded-bottom border">
                                <div class="info-grid">
                                    <div class="info-item full-width">
                                        <span class="info-label">Nombre completo:</span>
                                        <span class="info-value">{{ $estudiante['nombre'] }} {{ $estudiante['apellido_paterno'] }} {{ $estudiante['apellido_materno'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">C.I.:</span>
                                        <span class="info-value">{{ $estudiante['ci'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Nacimiento:</span>
                                        <span class="info-value">{{ $estudiante['fecha_nacimiento'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Género:</span>
                                        <span class="info-value">{{ $estudiante['genero'] }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Grado:</span>
                                        <span class="info-value badge bg-primary py-1">{{ $estudiante['grado'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEGUNDA SECCIÓN: Datos de Tutores -->
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-header bg-gradient-success text-white py-2 px-3 rounded-top">
                <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Datos de Tutores</h5>
            </div>
            <div class="card-body p-0">
                <div class="row g-0">
                    @foreach($tutores as $tutor)
                    <div class="col-md-6">
                        <div class="tutor-block p-3 {{ !$loop->last ? 'border-end-md' : '' }}">
                            <div class="tutor-header d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
                                <h6 class="mb-0 fw-bold">
                                    <i class="fas fa-user-tie me-1 text-muted"></i> 
                                    Tutor {{ $loop->iteration }}: {{ $tutor['nombre'] }} {{ $tutor['apellido_paterno'] }} {{ $tutor['apellido_materno'] }}
                                </h6>
                                <span class="badge bg-light text-dark small">C.I.: {{ $tutor['ci'] }}</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="tutor-details bg-light p-2 rounded">
                                        <div class="detail-item mb-2">
                                            <span class="detail-label"><i class="fas fa-briefcase me-1 text-muted"></i> Profesión:</span>
                                            <span class="detail-value">{{ $tutor['profesion'] }}</span>
                                        </div>
                                        <div class="detail-item mb-2">
                                            <span class="detail-label"><i class="fas fa-phone me-1 text-muted"></i> Teléfono:</span>
                                            <span class="detail-value">{{ $tutor['telefono'] }}</span>
                                        </div>
                                        <div class="detail-item mb-2">
                                            <span class="detail-label"><i class="fas fa-envelope me-1 text-muted"></i> Email:</span>
                                            <span class="detail-value">{{ $tutor['email'] }}</span>
                                        </div>
                                        <div class="areas-section mt-3">
                                            <span class="section-title"><i class="fas fa-tasks me-1 text-muted"></i> Áreas a cargo:</span>
                                            <ul class="list-unstyled ps-3 mb-0">
                                                @foreach($tutor['areas'] as $area)
                                                <li class="py-1">
                                                    <i class="fas fa-circle-notch fa-xs text-primary me-1"></i>
                                                    {{ $area['nombre'] }} <span class="text-muted">({{ $area['categoria'] }})</span>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="colegio-info bg-light p-2 rounded h-100">
                                        <span class="section-title"><i class="fas fa-school me-1 text-muted"></i> Colegio/Unidad:</span>
                                        <div class="colegio-details ps-3 mt-2">
                                            <div class="colegio-nombre fw-bold">{{ $tutor['colegio']['nombre'] }}</div>
                                            <div class="text-muted small">{{ $tutor['colegio']['dependencia'] }}</div>
                                            <div class="text-muted small">{{ $tutor['colegio']['departamento'] }}, {{ $tutor['colegio']['provincia'] }}</div>
                                            <div class="mt-2">
                                                <i class="fas fa-map-marker-alt fa-xs me-1 text-muted"></i>
                                                <span class="small">{{ $tutor['colegio']['direccion'] }}</span>
                                            </div>
                                            <div>
                                                <i class="fas fa-phone fa-xs me-1 text-muted"></i>
                                                <span class="small">{{ $tutor['colegio']['telefono'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- TERCERA SECCIÓN: Áreas Inscritas y Resumen de Pago -->
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body p-0">
                <div class="row g-0">
                    <!-- Áreas Inscritas -->
                    <div >
                        <div class="info-section h-100">
                            <div class="section-header bg-gradient-warning text-dark py-2 px-3 rounded-top">
                                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Áreas Inscritas</h5>
                            </div>
                            <div class="section-content p-0 bg-white rounded-bottom border">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-3" style="width: 40%">Área</th>
                                                <th style="width: 25%">Categoría</th>
                                                <th style="width: 25%">Modalidad</th>
                                                <th class="text-end pe-3" style="width: 10%">Precio (Bs.)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inscripciones as $inscripcion)
                                            <tr>
                                                <td class="ps-3">{{ $inscripcion['area'] }}</td>
                                                <td>{{ $inscripcion['categoria'] }}</td>
                                                <td>{{ $inscripcion['modalidad'] }}</td>
                                                <td class="text-end pe-3 fw-bold">{{ number_format($inscripcion['precio'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Resumen de Pago -->
                        <div class="col-md-4 ps-md-2 mt-3 mt-md-0">
                            <div class="info-section h-100">
                                <div class="section-header bg-gradient-danger text-white py-2 px-3 rounded-top">
                                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Resumen de Pago</h5>
                                </div>
                                <div class="section-content p-3 bg-white rounded-bottom border">
                                    <div class="payment-summary">
                                        <div class="summary-item d-flex justify-content-between mb-2">
                                            <span class="summary-label">Total áreas:</span>
                                            <span class="summary-value">{{ count($inscripciones) }} <span class="text-muted small">áreas</span></span>
                                        </div>                                    
                                        <div class="total-section mt-4 pt-3 border-top">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="total-label fw-bold">TOTAL A PAGAR:</span>
                                                <span class="total-amount bg-danger text-white py-1 px-2 rounded fw-bold">Bs. {{ number_format($totalPagar, 2) }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="payment-actions mt-3 pt-2 text-center">
                                            <button class="btn btn-sm btn-primary me-1">
                                                <i class="fas fa-print me-1"></i> Imprimir
                                            </button>
                                            <button class="btn btn-sm btn-success">
                                                <i class="fas fa-file-pdf me-1"></i> Exportar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Export PDF button
        document.getElementById('exportPdf').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = "{{ route('inscripcionEstudiante.exportar.pdf') }}";
        });
        // Export Excel button
        // document.getElementById('exportExcel').addEventListener('click', function(e) {
        //     e.preventDefault();
        //     window.location.href = "{{ route('areasCategorias.exportar.excel') }}";
        // }); 
        
    });
</script>