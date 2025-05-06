<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>FORMULARIO DE DATOS DE INSCRIPCION DEL ESTUDIANTE</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        
        .header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .logo-container {
            width: 25%;
            float: left;
        }
        
        .institution-info {
            width: 40%;
            float: left;
            font-size: 11px;
            line-height: 1.2;
        }
        
        .code-container {
            width: 25%;
            float: right;
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }
        
        .title {
            clear: both;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0 15px 0;
        }
        
        .info-row {
            margin: 5px 0;
            clear: both;
        }
        
        .info-row span {
            text-decoration: underline;
            color: #FF0000;
        }
        
        /* Estilos para las tablas (consistentes con el primer modelo) */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            table-layout: fixed;
        }
        
        th {
            background-color: #000;
            color: #fff;
            padding: 5px;
            text-align: left;
            border: 1px solid #000;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }
        
        td {
            border: 1px solid #000;
            padding: 5px;
            overflow-wrap: break-word;
            word-wrap: break-word;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .total-row {
            font-weight: bold;
        }
        
        .footer {
            margin-top: 20px;
            font-size: 11px;
            line-height: 1.3;
        }
        
        .red-text {
            color: #FF0000;
        }
        
        .watermark {
            width: 120px;
            position: absolute;
            top: 10px;
            left: 10px;
        }
        
        .sansi-logo {
            color: #FF0000;
            font-weight: bold;
            font-size: 24px;
        }
        
        .section-header {
            background-color: #000;
            color: #fff;
            padding: 5px;
            font-weight: bold;
            margin-top: 15px;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .bg-primary {
            background-color: #007bff;
            color: white;
        }
        
        .bg-warning {
            background-color: #ffc107;
            color: black;
        }
        
        .bg-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .bg-success {
            background-color: #28a745;
            color: white;
        }
        
        .bg-light {
            background-color: #f8f9fa;
            color: black;
        }
        
        .text-white {
            color: white;
        }
        
        .text-dark {
            color: black;
        }
        
        .text-muted {
            color: #6c757d;
        }
        
        .border-bottom {
            border-bottom: 1px solid #dee2e6;
        }
        
        .rounded {
            border-radius: 4px;
        }
        
        .detail-item {
            margin-bottom: 5px;
        }
        
        .detail-label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
        }
        
        .detail-value {
            display: inline-block;
        }
        
        .tutor-block {
            margin-bottom: 15px;
        }
        
        .tutor-header {
            margin-bottom: 5px;
        }
        
        .areas-section {
            margin-top: 5px;
        }
        
        .section-title {
            font-weight: bold;
            display: block;
            margin-bottom: 3px;
        }
        
        .total-section {
            margin-top: 10px;
            padding-top: 5px;
        }
        
        .total-label {
            font-size: 13px;
        }
        
        .total-amount {
            font-size: 14px;
            padding: 3px 8px;
        }
    </style>
</head>
<body>
    <div class="watermark">
        <img src="{{ public_path('img/logo-ohsansi.png') }}" alt="Logo UMSS" style="width: 100%; height: auto;">
    </div>
    
    <div class="header">
        <div class="logo-container">
            <!-- El logo ya está como watermark -->
        </div>
        
        <div class="institution-info">
            <p>Universidad Mayor de San Simon</p>
            <p>Ciencia y Conocimiento Desde 1832</p>
            <p>Dirección Av. Oquendo final Jordán s/n</p>
        </div>
        
        <div class="code-container">
            <p>CODIGO INSCRIPCION:</p>
            <p>{{ $codigoInscripcion}}</p>    
        </div>
    </div>
    <div style="clear: both;"></div>
    
    <div class="title">FORMULARIO DE REGISTRO DE DATOS PERSONALES, TUTORES ASIGNADOS Y ÁREAS DE INSCRIPCIÓN DEL ESTUDIANTE</div>
    
    <div class="info-row">
        Fecha generación de la Orden de Pago: {{ $fechaGeneracionFormulario }}
    </div>
    
    <div class="info-row">
        Referencia: Inscripciones Oh-Sansi!
    </div>
    
    <!-- SECCIÓN UNIFICADA: Información de Inscripción del Estudiante -->
    <table>
        <tr>
            <th colspan="4">INFORMACIÓN DE INSCRIPCIÓN DEL ESTUDIANTE</th>
        </tr>
        <tr>
            <td colspan="4" style="background-color: #f8f9fa;">
                <strong>ESTADO:</strong> <span class="badge bg-warning">{{ strtoupper($inscripcion['status']) }}</span>
            </td>
        </tr>
        <tr>
            <th colspan="4">Datos Personales</th>
        </tr>
        <tr>
            <td><strong>Nombre completo:</strong></td>
            <td colspan="3">{{ $estudiante['nombre'] }} {{ $estudiante['apellido_paterno'] }} {{ $estudiante['apellido_materno'] }}</td>
        </tr>
        <tr>
            <td><strong>C.I.:</strong></td>
            <td>{{ $estudiante['ci'] }}</td>
            <td><strong>Nacimiento:</strong></td>
            <td>{{ $estudiante['fecha_nacimiento'] }}</td>
        </tr>
        <tr>
            <td><strong>Género:</strong></td>
            <td>{{ $estudiante['genero'] }}</td>
            <td><strong>Grado:</strong></td>
            <td><span class="badge bg-primary">{{ $estudiante['grado'] }}</span></td>
        </tr>
        <tr>
            <td><strong>Número de Contacto:</strong></td>
            <td colspan="3">{{ $inscripcion['numero_contacto'] }}</td>
        </tr>
        <tr>
            <th colspan="2">Datos de Boleta</th>
            <th colspan="2">Datos de Convocatoria</th>
        </tr>
        <tr>
            <td><strong>Código boleta:</strong></td>
            <td class="{{ $codigoOrden ? 'text-success' : 'text-warning' }}">{{ $codigoOrden ?? 'Genera la Boleta' }}</td>
            <td><strong>Convocatoria:</strong></td>
            <td>{{ $convocatoria['nombre'] }}</td>
        </tr>
        <tr>
            <td><strong>Fecha generación:</strong></td>
            <td>{{ $fechaGeneracion }}</td>
            <td><strong>Email de contacto:</strong></td>
            <td>{{ $convocatoria['contacto'] }}</td>
        </tr>
        <tr>
            <td><strong>Fecha Vencimiento:</strong></td>
            <td>{{ $fechaVencimiento }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- TERCERA SECCIÓN: Datos de Tutores -->
    @foreach($tutores as $tutor)
    <table>
        <tr>
            <th colspan="4">DATOS DE TUTOR {{ $loop->iteration }}</th>
        </tr>
        <tr>
            <td><strong>Nombre completo:</strong></td>
            <td colspan="3">{{ $tutor['nombre'] }} {{ $tutor['apellido_paterno'] }} {{ $tutor['apellido_materno'] }}</td>
        </tr>
        <tr>
            <td><strong>C.I.:</strong></td>
            <td>{{ $tutor['ci'] }}</td>
            <td><strong>Profesión:</strong></td>
            <td>{{ $tutor['profesion'] }}</td>
        </tr>
        <tr>
            <td><strong>Teléfono:</strong></td>
            <td>{{ $tutor['telefono'] }}</td>
            <td><strong>Email:</strong></td>
            <td>{{ $tutor['email'] }}</td>
        </tr>
        <tr>
            <td><strong>Relación:</strong></td>
            <td colspan="3">{{ $tutor['relacion'] ?? 'Tutor' }}</td>
        </tr>
        <tr>
            <th colspan="4">Áreas a cargo</th>
        </tr>
        @foreach($tutor['areas'] as $area)
        <tr>
            <td colspan="4">
                <strong>{{ $area['nombre'] }}</strong> <span class="text-muted">({{ $area['categoria'] }})</span>
            </td>
        </tr>
        @endforeach
        <tr>
            <th colspan="4">Colegio/Unidad</th>
        </tr>
        <tr>
            <td colspan="4">
                <strong>{{ $tutor['colegio']['nombre'] }}</strong><br>
                <span class="text-muted">{{ $tutor['colegio']['dependencia'] }}</span><br>
                <span class="text-muted">{{ $tutor['colegio']['departamento'] }}, {{ $tutor['colegio']['provincia'] }}</span><br>
                <span>{{ $tutor['colegio']['direccion'] }}</span><br>
                <span>{{ $tutor['colegio']['telefono'] }}</span>
            </td>
        </tr>
    </table>
    @endforeach

    <!-- CUARTA SECCIÓN: Áreas Inscritas y Resumen de Pago -->
    <table>
        <tr>
            <th colspan="4">ÁREAS INSCRITAS</th>
        </tr>
        <tr>
            <th width="40%">Área</th>
            <th width="30%">Categoría</th>
            <th width="30%">Modalidad</th>
        </tr>
        @foreach($inscripciones as $inscripcion)
        <tr>
            <td>{{ $inscripcion['area'] }}</td>
            <td>{{ $inscripcion['categoria'] }}</td>
            <td>{{ $inscripcion['modalidad'] }}</td>
        </tr>
        @endforeach
        
    </table>
    
    <div class="footer">
        <p>Imprima esta hoja y diríjase a cajas facultativas para realizar el pago. Tenga en cuenta que el pago debe realizarlo una persona mayor de 18 años, un Tutor puede efectuar el pago de la inscripción.</p>
    </div>
</body>
</html>