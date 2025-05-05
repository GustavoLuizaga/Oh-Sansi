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

    <!-- Actions Container -->
    <div class="actions-container mb-1">
        <div class="button-group">
            <a href="{{ route('inscripcion.estudiante') }}" class="add-button py-1 px-2">
                <i class="fas fa-arrow-left"></i> Volver Formulario de Inscripción
            </a>
        </div>
        
        <div class="export-buttons">
            <button type="button" class="export-button pdf py-1 px-2" id="exportPdf">
                <i class="fas fa-file-pdf"></i> Generar orden de pago
            </button>
            
            <button type="button" class="export-button excel py-1 px-2" id="exportExcel">
                <i class="fas fa-file-excel"></i> Subir comprobante de pago
            </button>
        </div>
    </div>

    
        <div class="container mt-4">
            <!-- IDs Principales -->
            <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
                <h3>IDs Principales</h3>
                <p><strong>Estudiante ID:</strong> {{ $ids['estudiante_id'] }}</p>
                <p><strong>Tutor ID:</strong> {{ $ids['tutor_id'] }}</p>
                <p><strong>Inscripción ID:</strong> {{ $ids['inscripcion_id'] }}</p>
                <p><strong>Convocatoria ID:</strong> {{ $ids['convocatoria_id'] }}</p>
                <p><strong>Delegación ID:</strong> {{ $ids['delegacion_id'] }}</p>
                <p><strong>Grado ID:</strong> {{ $ids['grado_id'] }}</p>
            </div>
    
            <!-- Información del Estudiante -->
            <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
                <h3>Estudiante (ID: {{ $estudiante['id'] }})</h3>
                <p><strong>Nombre:</strong> {{ $estudiante['nombre'] }} {{ $estudiante['apellido_paterno'] }} {{ $estudiante['apellido_materno'] }}</p>
                <p><strong>CI:</strong> {{ $estudiante['ci'] }}</p>
                <p><strong>Grado:</strong> {{ $estudiante['grado'] }} (ID: {{ $inscripcion['grado_id'] }})</p>
                <p><strong>Fecha nacimiento:</strong> {{ $estudiante['fecha_nacimiento'] }}</p>
                <p><strong>Género:</strong> {{ $estudiante['genero'] }}</p>
            </div>
    
            <!-- Información de Tutores -->
            <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
                <h3>Tutores</h3>
                @foreach($tutores as $tutor)
                <div style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #ccc;">
                    <p><strong>Tutor ID:</strong> {{ $tutor['id'] }}</p>
                    <p><strong>Nombre:</strong> {{ $tutor['nombre'] }} {{ $tutor['apellido_paterno'] }} {{ $tutor['apellido_materno'] }}</p>
                    <p><strong>CI:</strong> {{ $tutor['ci'] }}</p>
                    <p><strong>Profesión:</strong> {{ $tutor['profesion'] }}</p>
                    <p><strong>Teléfono:</strong> {{ $tutor['telefono'] }}</p>
                    <p><strong>Email:</strong> {{ $tutor['email'] }}</p>
                    
                    <div style="margin-top: 10px;">
                        <h4>Áreas Inscritas</h4>
                        @foreach($tutor['areas'] as $area)
                        <p>
                            <strong>Área ID:</strong> {{ $area['id'] }} - {{ $area['nombre'] }} | 
                            <strong>Categoría ID:</strong> {{ $area['categoria_id'] }} - {{ $area['categoria'] }} | 
                            <strong>Detalle ID:</strong> {{ $area['detalle_inscripcion_id'] }} | 
                            <strong>Fecha:</strong> {{ $area['fecha_registro'] }}
                        </p>
                        @endforeach
                    </div>
                    
                    <div style="margin-top: 10px;">
                        <h4>Colegio/Unidad</h4>
                        <p><strong>ID:</strong> {{ $tutor['colegio']['id'] }}</p>
                        <p><strong>Nombre:</strong> {{ $tutor['colegio']['nombre'] }}</p>
                        <p><strong>Dependencia:</strong> {{ $tutor['colegio']['dependencia'] }}</p>
                        <p><strong>Departamento:</strong> {{ $tutor['colegio']['departamento'] }}</p>
                        <p><strong>Provincia:</strong> {{ $tutor['colegio']['provincia'] }}</p>
                        <p><strong>Dirección:</strong> {{ $tutor['colegio']['direccion'] }}</p>
                        <p><strong>Teléfono:</strong> {{ $tutor['colegio']['telefono'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
    
            <!-- Información de Convocatoria -->
            <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
                <h3>Convocatoria (ID: {{ $convocatoria['id'] }})</h3>
                <p><strong>Nombre:</strong> {{ $convocatoria['nombre'] }}</p>
                <p><strong>Fecha límite:</strong> {{ $convocatoria['fecha_limite'] }}</p>
                <p><strong>Método de pago:</strong> {{ $convocatoria['metodo_pago'] }}</p>
                <p><strong>Contacto:</strong> {{ $convocatoria['contacto'] }}</p>
            </div>
    
            <!-- Información de Inscripción -->
            <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
                <h3>Inscripción (ID: {{ $inscripcion['id'] }})</h3>
                <p><strong>Fecha:</strong> {{ $inscripcion['fecha'] }}</p>
                <p><strong>Número contacto:</strong> {{ $inscripcion['numero_contacto'] }}</p>
                <p><strong>Estado:</strong> {{ $inscripcion['status'] }}</p>
                <p><strong>Grado ID:</strong> {{ $inscripcion['grado_id'] }}</p>
            </div>
    
            <!-- Áreas Inscritas -->
            <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
                <h3>Áreas Inscritas</h3>
                @foreach($inscripciones as $inscripcion)
                <div style="margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px dotted #ccc;">
                    <p><strong>Área ID:</strong> {{ $inscripcion['area_id'] }} - {{ $inscripcion['area'] }}</p>
                    <p><strong>Categoría ID:</strong> {{ $inscripcion['categoria_id'] }} - {{ $inscripcion['categoria'] }}</p>
                    <p><strong>Detalle Inscripción ID:</strong> {{ $inscripcion['detalle_inscripcion_id'] }}</p>
                    <p><strong>Modalidad:</strong> {{ $inscripcion['modalidad'] }}</p>
                    <p><strong>Fecha registro:</strong> {{ $inscripcion['fecha_registro'] }}</p>
                    <p><strong>Precio:</strong> Bs. {{ number_format($inscripcion['precio'], 2) }}</p>
                </div>
                @endforeach
                <p><strong>Total a pagar:</strong> Bs. {{ number_format($totalPagar, 2) }}</p>
            </div>
    
            <!-- Información General -->
            <div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">
                <h3>Información General</h3>
                <p><strong>Código de orden:</strong> {{ $codigoOrden ?? 'N/A' }}</p>
                <p><strong>Fecha generación:</strong> {{ $fechaGeneracion }}</p>
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
