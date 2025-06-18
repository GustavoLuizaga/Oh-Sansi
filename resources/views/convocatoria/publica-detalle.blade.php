<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalles de Convocatoria - Oh! Sansi</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="/css/welcome.css">
    <link rel="stylesheet" href="/css/barraNavegacionPrincipal.css">
    <link rel="stylesheet" href="/css/contentFooter.css">
    <link rel="stylesheet" href="/css/convocatoria/publica-detalle.css">
    <link rel="stylesheet" href="/css/custom.css">
</head>

<body class="antialiased">
    @include('layouts/BarraNavegacionPrincipal')
    
    <div class="conv-detail-wrapper">
        <!-- Back Button -->
        <a href="{{ route('convocatoria.publica') }}" class="conv-back-btn">
            <i class="fas fa-arrow-left"></i> Volver a Convocatorias
        </a>
        
        <div class="conv-detail-container">
            <div class="conv-detail-header">
                <h1 class="conv-title"><i class="fas fa-clipboard-list"></i> {{ $convocatoria->nombre }}</h1>
                <span class="conv-status-badge conv-status-{{ strtolower($convocatoria->estado) }}">
                    <i class="fas fa-circle"></i> {{ strtoupper($convocatoria->estado) }}
                </span>
            </div>
            
            <!-- Action Buttons -->
            <div class="conv-action-buttons">
                <a href="{{ route('convocatorias.exportarPdf.UnaConvocatoria', $convocatoria->idConvocatoria) }}" class="conv-btn conv-btn-pdf">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </a>
            </div>
            
            <!-- Información General -->
            <div class="conv-info-section">
                <h2 class="conv-section-title">Información General</h2>
                
                <div class="conv-info-grid">
                    <div class="conv-info-item">
                        <span class="conv-info-label">Fecha de Inicio:</span>
                        <span class="conv-info-value">{{ \Carbon\Carbon::parse($convocatoria->fechaInicio)->format('d M, Y') }}</span>
                    </div>
                    
                    <div class="conv-info-item">
                        <span class="conv-info-label">Fecha de Fin:</span>
                        <span class="conv-info-value">{{ \Carbon\Carbon::parse($convocatoria->fechaFin)->format('d M, Y') }}</span>
                    </div>
                    
                    <div class="conv-info-item conv-info-full">
                        <span class="conv-info-label">Descripción:</span>
                        <span class="conv-info-value">{{ $convocatoria->descripcion }}</span>
                    </div>
                    
                    <div class="conv-info-item">
                        <span class="conv-info-label">Método de Pago:</span>
                        <span class="conv-info-value">{{ $convocatoria->metodoPago }}</span>
                    </div>
                    
                    <div class="conv-info-item">
                        <span class="conv-info-label">Contacto:</span>
                        <span class="conv-info-value">{{ $convocatoria->contacto }}</span>
                    </div>
                    
                    <div class="conv-info-item conv-info-full">
                        <span class="conv-info-label">Requisitos:</span>
                        <span class="conv-info-value">{{ $convocatoria->requisitos }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Áreas y Categorías -->
            <div class="conv-areas-section">
                <h2 class="conv-section-title"><i class="fas fa-layer-group"></i> Áreas y Categorías</h2>
                
                @if(count($areasConCategorias) > 0)
                    <div class="conv-areas-grid">
                        @foreach($areasConCategorias as $area)
                            <div id="area-{{ $area->idArea }}" class="conv-area-card">
                                <div class="conv-area-header">
                                    <h3 class="conv-area-title"><i class="fas fa-book"></i> {{ $area->nombre }}</h3>
                                </div>
                                <div class="conv-area-body">
                                    @if(count($area->categorias) > 0)
                                        @foreach($area->categorias as $categoria)
                                            <div class="conv-categoria-section">
                                                <h4 class="conv-categoria-title"><i class="fas fa-tag"></i> {{ $categoria->nombre }}</h4>
                                                @if(count($categoria->grados) > 0)
                                                    <div class="conv-grados-list">
                                                        @foreach($categoria->grados as $grado)
                                                            <span class="conv-grado-badge">{{ $grado->grado }}</span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="conv-no-grados">No hay grados asignados para esta categoría.</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="conv-no-categorias">No hay categorías asignadas para esta área.</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="conv-no-areas">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>No hay áreas asignadas a esta convocatoria.</p>
                    </div>
                @endif
            </div>

            <!-- Estilos para el resaltado -->
            <style>
                .conv-area-card {
                    transition: background-color 0.3s ease;
                }
                
                .conv-area-card.highlight {
                    background-color: #fff3cd;
                    animation: conv-highlight-fade 2s ease-out;
                }
                
                @keyframes conv-highlight-fade {
                    0% { background-color: #fff3cd; }
                    100% { background-color: transparent; }
                }
            </style>

            <!-- Script para el scroll automático -->
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const hash = window.location.hash;
                
                if (hash) {
                    setTimeout(() => {
                        const targetElement = document.querySelector(hash);
                        if (targetElement) {
                            targetElement.scrollIntoView({ behavior: 'smooth' });
                            targetElement.classList.add('highlight');
                            
                            setTimeout(() => {
                                targetElement.classList.remove('highlight');
                            }, 2000);
                        }
                    }, 100);
                }
            });
            </script>
        </div>
    </div>

    @include('layouts/contentFooter')

    <script src="/js/themeToggle.js"></script>
    <script src="/js/mobileMenu.js"></script>
    <script src="/js/contentFooter.js"></script>
</body>
</html>