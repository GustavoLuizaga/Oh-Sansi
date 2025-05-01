<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/delegado/ver-delegado.css') }}">

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-1">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Header Section -->
    <div class="delegado-header py-2">
        <h1><i class="fas fa-user-check"></i> {{ __('Detalles del Tutor') }}</h1>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
        <!-- Información del Tutor -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Información Personal -->
            <div class="info-section border-b md:border-b-0 md:border-r pb-4 md:pr-6">
                <h2 class="section-title"><i class="fas fa-user mr-2"></i>Información Personal</h2>
                <div class="info-grid">
                    <div class="info-label">CI:</div>
                    <div class="info-value">{{ $tutor->user->ci }}</div>
                    
                    <div class="info-label">Nombre Completo:</div>
                    <div class="info-value">{{ $tutor->user->name }} {{ $tutor->user->apellidoPaterno }} {{ $tutor->user->apellidoMaterno }}</div>
                    
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $tutor->user->email }}</div>
                    
                    <div class="info-label">Teléfono:</div>
                    <div class="info-value">{{ $tutor->telefono }}</div>
                    
                    <div class="info-label">Profesión:</div>
                    <div class="info-value">{{ $tutor->profesion }}</div>
                    
                    <div class="info-label">Género:</div>
                    <div class="info-value">{{ $tutor->user->genero == 'M' ? 'Masculino' : 'Femenino' }}</div>
                    
                    <div class="info-label">Fecha de Nacimiento:</div>
                    <div class="info-value">{{ date('d/m/Y', strtotime($tutor->user->fechaNacimiento)) }}</div>
                </div>
            </div>
            
            <!-- Información Académica -->
            <div class="info-section pt-4 md:pt-0 md:pl-6">
                <h2 class="section-title"><i class="fas fa-graduation-cap mr-2"></i>Información Académica</h2>
                <div class="info-grid">
                    <div class="info-label">Colegios:</div>
                    <div class="info-value">
                        <div class="colegio-selector mb-3">
                            <select id="colegio-selector" class="form-control">
                                <option value="">Seleccione un colegio</option>
                                @php
                                    $colegiosUnicos = $tutor->delegaciones->unique('idDelegacion');
                                @endphp
                                @foreach($colegiosUnicos as $delegacion)
                                    <option value="{{ $delegacion->idDelegacion }}">{{ $delegacion->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="info-label">Áreas de Tutoría:</div>
                    <div class="info-value">
                        <div class="areas-container">
                            @foreach($colegiosUnicos as $delegacion)
                                <div id="areas-colegio-{{ $delegacion->idDelegacion }}" class="areas-por-colegio" style="display: none;">
                                    <div class="colegio-title mb-2">
                                        <i class="fas fa-school mr-1"></i> <strong>{{ $delegacion->nombre }}</strong>
                                    </div>
                                    <div class="areas-list ml-4 mt-1">
                                        <div class="flex flex-wrap mt-1">
                                            @php
                                                $areasDelColegio = $tutor->areas()
                                                    ->wherePivot('idDelegacion', $delegacion->idDelegacion)
                                                    ->get();
                                            @endphp
                                            
                                            @if($areasDelColegio->count() > 0)
                                                @foreach($areasDelColegio as $area)
                                                    <div class="area-badge mr-3 mb-2">
                                                        <i class="fas fa-check-circle text-success mr-1"></i>
                                                        <span class="text-sm">{{ $area->nombre }}</span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-gray-500 text-sm">
                                                    <i class="fas fa-info-circle mr-1"></i> No hay áreas asignadas para este colegio
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div id="no-colegio-selected" class="text-gray-500 text-center py-3">
                                <i class="fas fa-info-circle mr-1"></i> Seleccione un colegio para ver sus áreas
                            </div>
                            <style>
                                .area-badge {
                                    display: inline-flex;
                                    align-items: center;
                                    background-color: #f0f9ff;
                                    border: 1px solid #bae6fd;
                                    border-radius: 0.375rem;
                                    padding: 0.25rem 0.5rem;
                                    color: #0369a1;
                                }
                                .text-success {
                                    color: #059669;
                                }
                            </style>
                        </div>
                    </div>
                    
                    <div class="info-label">Documento CV:</div>
                    <div class="info-value">
                        @if($tutor->linkRecurso)
                            <a href="{{ asset('storage/cvs/' . basename($tutor->linkRecurso)) }}" target="_blank" class="document-link">
                                <i class="fas fa-file-pdf"></i> Ver documento
                            </a>
                        @else
                            <span class="text-gray-500">No disponible</span>
                        @endif
                    </div>

                    <div class="info-label">Estado:</div>
                    <div class="info-value">
                        <span class="status-badge status-approved">Aprobado</span>
                    </div>

                    <div class="info-label">Rol de Director:</div>
                    <div class="info-value">
                        <span class="{{ $tutor->es_director ? 'status-badge status-director' : 'status-badge status-regular' }}">
                            {{ $tutor->es_director ? 'Director' : 'Tutor Regular' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Opciones de Director -->
        <div class="director-section mt-4 pt-4 border-t">
            <h2 class="section-title"><i class="fas fa-user-tie mr-2"></i>Rol de Director</h2>
            <div class="flex items-center mt-2">
                <div class="info-label mr-4">¿Es Director?</div>
                <form action="{{ route('delegado.toggle-director', $tutor->id) }}" method="POST" class="flex items-center">
                    @csrf
                    <label class="switch">
                        <input type="checkbox" name="es_director" {{ $tutor->es_director ? 'checked' : '' }} onchange="this.form.submit()">
                        <span class="slider round"></span>
                    </label>
                    <span class="ml-2 {{ $tutor->es_director ? 'text-green-600 font-medium' : 'text-gray-500' }}">
                        {{ $tutor->es_director ? 'Sí' : 'No' }}
                    </span>
                </form>
            </div>
            <p class="text-sm text-gray-500 mt-2">
                <i class="fas fa-info-circle mr-1"></i> Los directores tienen permisos adicionales para gestionar tutores y recursos.
            </p>
        </div>
    </div>

    <!-- Previsualización del PDF -->
    @if($tutor->linkRecurso)
    <div class="bg-white p-4 rounded-lg shadow-md mb-4">
        <h2 class="section-title mb-3"><i class="fas fa-file-pdf mr-2"></i>Previsualización del Documento</h2>
        <div class="pdf-preview">
            <iframe src="{{ asset('storage/cvs/' . basename($tutor->linkRecurso)) }}" class="pdf-iframe" title="Documento CV"></iframe>
        </div>
    </div>
    @endif

    <!-- Botones de Acción -->
    <div class="action-buttons">
        <a href="{{ route('delegado.editar', ['id' => $tutor->id]) }}" class="btn btn-primary">
            <i class="fas fa-edit mr-2"></i> Editar Tutor
        </a>

        <button type="button" data-tutor-id="{{ $tutor->id }}" class="btn btn-danger btn-eliminar">
            <i class="fas fa-trash-alt mr-2"></i> Eliminar Tutor
        </button>
        
        <a href="{{ route('delegado') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Volver a Tutores
        </a>
    </div>
</x-app-layout>

<!-- Modal de Confirmación de Eliminación -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3>Confirmar Eliminación</h3>
        <p>¿Está seguro que desea eliminar este tutor?</p>
        <div class="modal-actions">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="cancel-button" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="delete-button">Eliminar</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejo de botones de eliminación
    const deleteButtons = document.querySelectorAll('.btn-eliminar');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tutorId = this.getAttribute('data-tutor-id');
            confirmarEliminacion(tutorId);
        });
    });
    
    // Manejo del selector de colegios
    const colegioSelector = document.getElementById('colegio-selector');
    const noColegioSelected = document.getElementById('no-colegio-selected');
    const areasContainers = document.querySelectorAll('.areas-por-colegio');
    
    colegioSelector.addEventListener('change', function() {
        const selectedColegioId = this.value;
        
        // Ocultar todos los contenedores de áreas
        areasContainers.forEach(container => {
            container.style.display = 'none';
        });
        
        if (selectedColegioId) {
            // Mostrar el contenedor de áreas del colegio seleccionado
            const selectedAreasContainer = document.getElementById(`areas-colegio-${selectedColegioId}`);
            if (selectedAreasContainer) {
                selectedAreasContainer.style.display = 'block';
                noColegioSelected.style.display = 'none';
            }
        } else {
            // Mostrar mensaje de selección
            noColegioSelected.style.display = 'block';
        }
    });
});

function confirmarEliminacion(tutorId) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    form.action = `/delegado/eliminar/${tutorId}`;
    modal.style.display = 'flex';
}

function cerrarModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target == modal) {
        cerrarModal();
    }
}
</script>