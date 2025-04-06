<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/convocatoria/ver.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <div class="p-6">
        <!-- Back Button -->
        <a href="{{ route('convocatoria') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Volver a Convocatorias
        </a>
        
        <div class="convocatoria-detail-container">
            <div class="detail-header">
                <h1><i class="fas fa-clipboard-list"></i> {{ $convocatoria->nombre }}</h1>
                <span class="estado-badge estado-{{ strtolower($convocatoria->estado) }}">
                    <i class="fas fa-circle"></i> {{ strtoupper($convocatoria->estado) }}
                </span>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                @if($convocatoria->estado != 'Cancelada')
                    <a href="{{ route('convocatorias.editar', $convocatoria->idConvocatoria) }}" class="btn-action">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endif
                
                @if($convocatoria->estado == 'Borrador')
                    <a href="#" class="btn-action btn-publish" onclick="event.preventDefault(); if(confirm('¿Está seguro de publicar esta convocatoria?')) document.getElementById('publish-form').submit();">
                        <i class="fas fa-check-circle"></i> Publicar
                    </a>
                    <form id="publish-form" action="{{ route('convocatorias.publicar', $convocatoria->idConvocatoria) }}" method="POST" style="display: none;">
                        @csrf
                        @method('PUT')
                    </form>
                @endif
                
                @if($convocatoria->estado != 'Cancelada')
                    <a href="#" class="btn-action btn-cancel" onclick="event.preventDefault(); if(confirm('¿Está seguro de cancelar esta convocatoria?')) document.getElementById('cancel-form').submit();">
                        <i class="fas fa-ban"></i> Cancelar
                    </a>
                    <form id="cancel-form" action="{{ route('convocatorias.cancelar', $convocatoria->idConvocatoria) }}" method="POST" style="display: none;">
                        @csrf
                        @method('PUT')
                    </form>
                @endif
            </div>
            
            <!-- Información General -->
            <div class="detail-section">
                <h2 class="section-title">Información General</h2>
                
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Fecha de Inicio:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($convocatoria->fechaInicio)->format('d M, Y') }}</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Fecha de Fin:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($convocatoria->fechaFin)->format('d M, Y') }}</span>
                    </div>
                    
                    <div class="info-item full-width">
                        <span class="info-label">Descripción:</span>
                        <p class="info-value description">{{ $convocatoria->descripcion }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Método de Pago -->
            <div class="detail-section">
                <h2 class="section-title">Método de Pago</h2>
                <p>{{ $convocatoria->metodoPago }}</p>
            </div>
            
            <!-- Áreas y Categorías -->
            <div class="detail-section">
                <h2 class="section-title">Áreas y Categorías</h2>
                
                @foreach($areasConCategorias as $area)
                <div class="area-container">
                    <h3 class="area-title">{{ $area->nombre }}</h3>
                    
                    <div class="categorias-list">
                        @foreach($area->categorias as $categoria)
                        <div class="categoria-item">
                            <h4 class="categoria-title">{{ $categoria->nombre }}</h4>
                            
                            <div class="grados-list">
                                @foreach($categoria->grados as $grado)
                                <span class="grado-badge">{{ $grado->nombre }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Requisitos -->
            <div class="detail-section">
                <h2 class="section-title">Requisitos</h2>
                <p>{{ $convocatoria->requisitos }}</p>
            </div>
            
            <!-- Contacto de Soporte -->
            <div class="detail-section">
                <h2 class="section-title">Contacto de Soporte</h2>
                <p>{{ $convocatoria->contacto }}</p>
            </div>
        </div>
    </div>
</x-app-layout>