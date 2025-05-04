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
            <button type="button" class="export-button pdf py-1 px-2">
                <i class="fas fa-file-pdf"></i> Generar orden de pago
            </button>

            <button type="button" class="export-button excel py-1 px-2">
                <i class="fas fa-file-excel"></i> Subir comprobante de pago
            </button>
        </div>
    </div>

    

    <!-- Table -->
    <table class="estudiantes-table">
        <thead>
            <tr>
                <th>CI</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Estado</th>
                
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>

    
</x-app-layout>