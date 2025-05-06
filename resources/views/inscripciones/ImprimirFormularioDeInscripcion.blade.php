@push('styles')
    <link rel="stylesheet" href="{{ asset('css/inscripcion/ImprimirFormularioDeInscripcion.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/ImprimirFormularioDeInscripcion.js') }}"></script>
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

    <!-- IDs ocultos -->
    <div id="data-ids" 
        data-estudiante-id="{{ $ids['estudiante_id'] }}"
        data-tutor-id="{{ $ids['tutor_id'] }}"
        data-inscripcion-id="{{ $ids['inscripcion_id'] }}"
        data-convocatoria-id="{{ $ids['convocatoria_id'] }}"
        data-delegacion-id="{{ $ids['delegacion_id'] }}"
        data-grado-id="{{ $ids['grado_id'] }}"
        style="display: none;">
    </div>

    <div class="container-fluid mt-2 px-2">
        <!-- Botones de acción -->
        <div class="payment-actions mb-2 text-center">
            <h4>CODIGO DE INSCRIPCION UNICO DEL ESTUDIANTE "{{ $codigoInscripcion}}"
            </h4>
            <button class="btn btn-sm btn-primary me-1 export-button pdf py-1 px-2" id="exportPdf"">
                <i class="fas fa-print me-1"></i> Imprimir Formulario

            </button>'
        </div>

        <!-- SECCIÓN UNIFICADA: Información de Inscripción del Estudiante -->
        <div class="card shadow-sm mb-2 border-0">
            <div class="section-header bg-gradient-primary text-white py-1 px-2 rounded-top">
                <h5 class="mb-0"><i class="fas fa-user-graduate me-1"></i>Información de Inscripción del Estudiante</h5>
                    <span class="align-content-end">ESTADO:</span>
                    <span class="align-content-end badge bg-warning text-dark">{{ strtoupper($inscripcion['status']) }}</span>
                
            </div>
            <div class="section-content p-2 bg-white rounded-bottom border">
                <!-- Datos personales del estudiante -->
                <div class="mb-3 pb-3 border-bottom">
                    <h6 class="text-primary mb-2"><i class="fas fa-id-card me-1"></i>Datos Personales</h6>
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
                            <span class="info-value badge bg-primary">{{ $estudiante['grado'] }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Numero de Contacto:</span>
                            <span class="info-value">{{ $inscripcion['numero_contacto'] }}</span>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Datos de la inscripción -->
                <div class="row">
                    <!-- Columna izquierda: Boleta y fechas -->
                    <div class="col-md-6 border-end">
                        <h6 class="text-primary mb-2"><i class="fas fa-receipt me-1"></i>Datos de Boleta</h6>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Código boleta:</span>
                                <span class="info-value {{ $codigoOrden ? 'text-success fw-bold' : 'text-warning' }}">
                                    {{ $codigoOrden ?? 'Genera la Boleta' }}
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Fecha generación:</span>
                                <span class="info-value">{{ $fechaGeneracion }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Fecha Vencimiento:</span>
                                <span class="info-value">{{ $fechaVencimiento }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna derecha: Convocatoria y Estado -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-2"><i class="fas fa-info-circle me-1"></i>Datos de Convocatoria</h6>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Convocatoria:</span>
                                <span class="info-value">{{ $convocatoria['nombre'] }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email de contacto:</span>
                                <span class="info-value">{{ $convocatoria['contacto'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TERCERA SECCIÓN: Datos de Tutores -->
        <div class="card shadow-sm mb-2 border-0">
            <div class="section-header bg-gradient-success text-white py-1 px-2 rounded-top">
                <h5 class="mb-0"><i class="fas fa-chalkboard-teacher me-1"></i>Datos de Tutores</h5>
            </div>
            <div class="card-body p-1">
                @foreach($tutores as $tutor)
                <div class="tutor-block mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="tutor-header d-flex justify-content-between align-items-center pb-1 mb-1">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-user-tie me-1"></i> 
                            Tutor {{ $loop->iteration }}: {{ $tutor['nombre'] }} {{ $tutor['apellido_paterno'] }} {{ $tutor['apellido_materno'] }}
                        </h6>
                        <span class="badge bg-light text-dark">C.I.: {{ $tutor['ci'] }}</span>
                    </div>
                    <div class="row g-1">
                        <div class="col-md-6">
                            <div class="tutor-details bg-light p-2 rounded">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item mb-1">
                                            <span class="detail-label"><i class="fas fa-briefcase me-1"></i> Profesión:</span>
                                            <span class="detail-value">{{ $tutor['profesion'] }}</span>
                                        </div>
                                        <div class="detail-item mb-1">
                                            <span class="detail-label"><i class="fas fa-phone me-1"></i> Teléfono:</span>
                                            <span class="detail-value">{{ $tutor['telefono'] }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item mb-1">
                                            <span class="detail-label"><i class="fas fa-envelope me-1"></i> Email:</span>
                                            <span class="detail-value">{{ $tutor['email'] }}</span>
                                        </div>
                                        <div class="detail-item mb-1">
                                            <span class="detail-label"><i class="fas fa-id-card me-1"></i> Relación:</span>
                                            <span class="detail-value">{{ $tutor['relacion'] ?? 'Tutor' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="areas-section mt-2">
                                    <span class="section-title"><i class="fas fa-tasks me-1"></i> Áreas a cargo:</span>
                                    <ul class="list-unstyled ps-2 mb-0">
                                        @foreach($tutor['areas'] as $area)
                                        <li class="py-0">
                                            <i class="fas fa-circle-notch fa-xs text-primary me-1"></i>
                                            {{ $area['nombre'] }} <span class="text-muted">({{ $area['categoria'] }})</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="colegio-info bg-light p-2 rounded">
                                <span class="section-title"><i class="fas fa-school me-1"></i> Colegio/Unidad:</span>
                                <div class="colegio-details ps-2 mt-1">
                                    <div class="colegio-nombre fw-bold">{{ $tutor['colegio']['nombre'] }}</div>
                                    <div class="text-muted">{{ $tutor['colegio']['dependencia'] }}</div>
                                    <div class="text-muted">{{ $tutor['colegio']['departamento'] }}, {{ $tutor['colegio']['provincia'] }}</div>
                                    <div class="mt-1">
                                        <i class="fas fa-map-marker-alt fa-xs me-1"></i>
                                        <span>{{ $tutor['colegio']['direccion'] }}</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-phone fa-xs me-1"></i>
                                        <span>{{ $tutor['colegio']['telefono'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- CUARTA SECCIÓN: Áreas Inscritas y Resumen de Pago -->
        <div>
            <!-- Áreas Inscritas -->
            <div >
                <div class="card shadow-sm border-0 h-100">
                    <div class="section-header bg-gradient-warning text-dark py-1 px-2 rounded-top">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-1"></i>Áreas Inscritas</h5>
                    </div>
                    <div class="section-content p-0 bg-white rounded-bottom border">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-2" style="width: 40%">Área</th>
                                        <th style="width: 25%">Categoría</th>
                                        <th style="width: 25%">Modalidad</th>
                                        <th class="text-end pe-2" style="width: 10%">Precio (Bs.)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inscripciones as $inscripcion)
                                    <tr>
                                        <td class="ps-2">{{ $inscripcion['area'] }}</td>
                                        <td>{{ $inscripcion['categoria'] }}</td>
                                        <td>{{ $inscripcion['modalidad'] }}</td>
                                        <td class="text-end pe-2 fw-bold">{{ number_format($inscripcion['precio'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="total-section mt-2 pt-2 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="total-label fw-bold">TOTAL A PAGAR:</span>
                                <span class="total-amount bg-danger text-white py-1 px-2 rounded fw-bold">Bs. {{ number_format($totalPagar, 2) }}</span>
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
            window.location.href = "{{ route('inscripcionEstudiante.ImprimirFormulario.pdf') }}";
        });
    });
</script>