<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/delegacion/informacion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/delegacion/modal.css') }}">
    <div class="delegacion-info-container">
        <div class="delegacion-info-header">
            <h1>{{ $delegacion->nombre }}</h1>
            
            <div class="action-buttons">
                <a href="{{ route('delegaciones.editar', $delegacion->codigo_sie) }}" class="action-button edit-button">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="#" class="action-button delete-button" data-id="{{ $delegacion->codigo_sie }}" data-nombre="{{ $delegacion->nombre }}">
                    <i class="fas fa-trash"></i> Eliminar
                </a>
                <a href="{{ route('delegaciones') }}" class="action-button back-button">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="delegacion-info-content">
            <div class="info-section">
                <h2>Información General</h2>
                
                <div class="info-item">
                    <span class="info-label">Código SIE:</span>
                    <span class="info-value">{{ $delegacion->codigo_sie }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value">{{ $delegacion->nombre }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Dependencia:</span>
                    <span class="info-value">{{ $delegacion->dependencia }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Teléfono:</span>
                    <span class="info-value">{{ $delegacion->telefono ?: 'No especificado' }}</span>
                </div>
            </div>
            
            <div class="info-section">
                <h2>Ubicación</h2>
                
                <div class="info-item">
                    <span class="info-label">Departamento:</span>
                    <span class="info-value">{{ $delegacion->departamento }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Provincia:</span>
                    <span class="info-value">{{ $delegacion->provincia }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Municipio:</span>
                    <span class="info-value">{{ $delegacion->municipio }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Zona:</span>
                    <span class="info-value">{{ $delegacion->zona ?: 'No especificada' }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Dirección:</span>
                    <span class="info-value">{{ $delegacion->direccion }}</span>
                </div>
            </div>
            
            <div class="info-section">
                <h2>Responsable</h2>
                
                <div class="info-item">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value">{{ $delegacion->responsable_nombre }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Correo Electrónico:</span>
                    <span class="info-value">{{ $delegacion->responsable_email }}</span>
                </div>
            </div>
        </div>
        
        <div class="timestamps">
            <div>Creado: {{ date('d/m/Y H:i', strtotime($delegacion->created_at)) }}</div>
            <div>Última modificación: {{ date('d/m/Y H:i', strtotime($delegacion->updated_at)) }}</div>
        </div>
    </div>
    
    <!-- Include the modal outside the container -->
    @include('delegaciones.modal')
</x-app-layout>