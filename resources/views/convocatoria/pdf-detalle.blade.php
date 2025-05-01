<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tituloPDF }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.2;
            color: #333;
            margin: 0;
            padding: 10px;
            font-size: 9px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #1a365d;
            padding-bottom: 3px;
        }

        .header h1 {
            color: #1a365d;
            margin: 0 0 3px 0;
            font-size: 14px;
        }

        .estado {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 8px;
        }

        .section {
            margin-bottom: 8px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e2e8f0;
        }

        .section-title {
            color: #2c5282;
            font-size: 11px;
            font-weight: bold;
            margin: 0 0 3px 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
        }

        .info-item {
            margin-bottom: 3px;
        }

        .info-label {
            font-weight: bold;
            color: #4a5568;
            margin-right: 3px;
        }

        .full-width {
            grid-column: span 4;
        }

        .area-container {
            margin-bottom: 5px;
        }

        .area-title {
            font-size: 10px;
            font-weight: bold;
            color: #1a365d;
            margin: 0 0 3px 0;
            padding-bottom: 1px;
        }

        .categoria-item {
            margin-bottom: 4px;
            padding: 3px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 2px;
        }

        .categoria-title {
            font-size: 9px;
            font-weight: bold;
            color: #2c5282;
            margin: 0 0 2px 0;
            display: inline-block;
        }

        .precio-info {
            display: inline-flex;
            gap: 8px;
            margin: 0 0 0 10px;
            font-size: 9px;
        }

        .grados-list {
            display: inline-flex;
            gap: 3px;
            margin-left: 10px;
        }

        .grado-badge {
            background-color: #e2e8f0;
            padding: 0 3px;
            border-radius: 4px;
            font-size: 8px;
        }

        .footer {
            text-align: center;
            font-size: 8px;
            color: #718096;
            margin-top: 10px;
            position: fixed;
            bottom: 5px;
            left: 0;
            right: 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $convocatoria->nombre }}
    <!--<div class="estado estado-{{ strtolower($convocatoria->estado) }}">{{ strtoupper($convocatoria->estado) }}</div>-->
        </h1>
    </div>

    <div class="section">
        <h2 class="section-title">Información General</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Inicio:</span>
                {{ \Carbon\Carbon::parse($convocatoria->fechaInicio)->format('d/m/Y') }}
            </div>
            <div class="info-item">
                <span class="info-label">Fin:</span>
                {{ \Carbon\Carbon::parse($convocatoria->fechaFin)->format('d/m/Y') }}
            </div>
            <div class="info-item">
                <span class="info-label">Pago:</span>
                {{ $convocatoria->metodoPago }}
            </div>
            <div class="info-item">
                <span class="info-label">Estado:</span>
                {{ $convocatoria->estado }}
            </div>
            <div class="info-item full-width">
                <span class="info-label">Descripción:</span>
                {{ $convocatoria->descripcion }}
            </div>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Áreas y Categorías</h2>
        @foreach($areasConCategorias as $area)
        <div class="area-container">
            <h3 class="area-title">{{ $area->nombre }}</h3>
            @foreach($area->categorias as $categoria)
            <div class="categoria-item">
                <h4 class="categoria-title">{{ $categoria->nombre }}</h4>
                @php
                $precios = DB::table('convocatoriaAreaCategoria')
                ->where('idConvocatoria', $convocatoria->idConvocatoria)
                ->where('idArea', $area->idArea)
                ->where('idCategoria', $categoria->idCategoria)
                ->first(['precioIndividual', 'precioDuo', 'precioEquipo']);
                @endphp
                <div class="precio-info">
                    @if($precios->precioIndividual)<span class="precio-label">Ind: {{ number_format($precios->precioIndividual, 2) }}Bs</span>@endif
                    @if($precios->precioDuo)<span class="precio-label">Dúo: {{ number_format($precios->precioDuo, 2) }}Bs</span>@endif
                    @if($precios->precioEquipo)<span class="precio-label">Eq: {{ number_format($precios->precioEquipo, 2) }}Bs</span>@endif
                </div>
                <div class="grados-list">
                    @foreach($categoria->grados as $grado)
                    <span class="grado-badge">{{ $grado->nombre }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <div class="section">
        <h2 class="section-title">Requisitos y Contacto</h2>
        <div class="info-grid">
            <div class="info-item full-width">
                <span class="info-label">Requisitos:</span>
                {{ $convocatoria->requisitos }}
            </div>
            <div class="info-item full-width">
                <span class="info-label">Contacto:</span>
                {{ $convocatoria->contacto }}
            </div>
        </div>
    </div>

    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>