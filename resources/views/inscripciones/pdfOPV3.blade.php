<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Pago</title>
    <style>
        /* Estilos CSS adicionales para la boleta */
        .boleta-container {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .boleta-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        
        .boleta-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .boleta-subtitle {
            font-size: 18px;
            margin-top: 5px;
            color: #555;
        }
        
        .section {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .info-table th {
            text-align: left;
            padding: 8px;
            background-color: #f2f2f2;
            width: 30%;
        }
        
        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        .inscripciones-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .inscripciones-table th, 
        .inscripciones-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .inscripciones-table th {
            background-color: #f2f2f2;
        }
        
        .total-section {
            text-align: right;
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }
        
        .codigo-boleta {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px dashed #6c757d;
            font-weight: bold;
        }
        
        .contacto-info {
            margin-top: 20px;
            text-align: center;
            font-style: italic;
            color: #666;
        }
        
        .fecha-generacion {
            text-align: right;
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="boleta-container">
        <!-- Encabezado -->
        <div class="boleta-header">
            <div class="boleta-title">ORDEN DE PAGO</div>
            <div class="boleta-subtitle">OLIMPIADAS CIENTÍFICAS PLURINACIONALES</div>
        </div>
        
        <!-- Fecha de generación -->
        <div class="fecha-generacion">
            Fecha de generación: {{ $fechaGeneracion }}
        </div>
        
        <!-- Código de boleta -->
        @if($codigoOrden)
        <div class="codigo-boleta">
            Código de boleta: {{ $codigoOrden }}
        </div>
        @endif
        
        <!-- Sección 1: Información del Estudiante -->
        <div class="section">
            <div class="section-title">INFORMACIÓN DEL ESTUDIANTE</div>
            <table class="info-table">
                <tr>
                    <th>Nombre completo:</th>
                    <td>{{ $estudiante['nombre'] }} {{ $estudiante['apellido_paterno'] }} {{ $estudiante['apellido_materno'] }}</td>
                </tr>
                <tr>
                    <th>Carnet de Identidad:</th>
                    <td>{{ $estudiante['ci'] }}</td>
                </tr>
                <tr>
                    <th>Grado:</th>
                    <td>{{ $estudiante['grado'] }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Sección 2: Información de Tutores -->
        <div class="section">
            <div class="section-title">INFORMACIÓN DE TUTORES</div>
            @foreach($tutores as $tutor)
            <table class="info-table">
                <tr>
                    <th>Nombre completo:</th>
                    <td>{{ $tutor['nombre'] }} {{ $tutor['apellido_paterno'] }} {{ $tutor['apellido_materno'] }}</td>
                </tr>
                <tr>
                    <th>Carnet de Identidad:</th>
                    <td>{{ $tutor['ci'] }}</td>
                </tr>
                <tr>
                    <th>Profesión:</th>
                    <td>{{ $tutor['profesion'] }}</td>
                </tr>
                <tr>
                    <th>Colegio/Unidad Educativa:</th>
                    <td>{{ $tutor['colegio'] }}</td>
                </tr>
                <tr>
                    <th>Áreas inscritas:</th>
                    <td>{{ implode(', ', $tutor['areas']) }}</td>
                </tr>
            </table>
            @if(!$loop->last)
            <hr style="margin: 15px 0; border: 0; border-top: 1px dashed #ccc;">
            @endif
            @endforeach
        </div>
        
        <!-- Sección 3: Información de Inscripción -->
        <div class="section">
            <div class="section-title">INFORMACIÓN DE INSCRIPCIÓN</div>
            <table class="inscripciones-table">
                <thead>
                    <tr>
                        <th>Área</th>
                        <th>Categoría</th>
                        <th>Grado</th>
                        <th>Modalidad</th>
                        <th>Precio (Bs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inscripciones as $inscripcion)
                    <tr>
                        <td>{{ $inscripcion['area'] }}</td>
                        <td>{{ $inscripcion['categoria'] }}</td>
                        <td>{{ $estudiante['grado'] }}</td>
                        <td>INDIVIDUAL</td>
                        <td>{{ number_format($inscripcion['precio'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="total-section">
                Total a pagar: Bs. {{ number_format($totalPagar, 2) }}
            </div>
        </div>
        
        <!-- Información de contacto -->
        <div class="contacto-info">
            Número de contacto: 1234567
        </div>
    </div>

</body>
</html>