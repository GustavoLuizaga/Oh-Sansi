<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Pago</title>
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

        /* Resto del CSS del segundo documento */
        .info-row {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background-color: black;
            color: white;
            padding: 5px;
            text-align: left;
            border: 1px solid black;
        }

        td {
            border: 1px solid black;
            padding: 5px;
        }

        .table-header {
            background-color: black;
            color: white;
            font-weight: bold;
            padding: 5px;
        }

        .modalidad-row {
            background-color: black;
            color: white;
            font-weight: bold;
        }

        .header-row {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .total-row {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .details-table {
            margin-top: 20px;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
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

        .red-text {
            color: #FF0000;
        }
    </style>
</head>

<!-- ... mantener los estilos ... -->

<body>
    <div class="watermark">
        <img src="{{ public_path('img/logo-ohsansi.png') }}" alt="Logo UMSS" style="width: 100%; height: auto;">
    </div>

    <div class="header">
        <!-- ... mantener header ... -->

        <div class="logo-container">
            <!-- El logo ya está como watermark -->
        </div>

        <div class="institution-info">
            <p>Universidad Mayor de San Simon</p>
            <p>Ciencia y Conocimiento Desde 1832</p>
            <p>Dirección Av. Oquendo final Jordán s/n</p>
        </div>

        <!-- ...existing code... -->

        <div class="code-container">
            <p>CODIGO ORDEN DE PAGO:</p>
            <p>{{ $codigoOrden }}</p>
        </div>

        <div class="title">ORDEN DE PAGO</div>
        <div class="date">Fecha generación de Orden de Pago: {{ $fecha }}</div>

    </div>
    <!-- DATOS DE TUTOR/DELEGADO -->
    <table>
        <tr>
            <th colspan="4">DATOS DE TUTOR/DELEGADO</th>
        </tr>
        <tr class="header-row">
            <td>Nombre(s):</td>
            <td>Apellido Paterno:</td>
            <td>Apellido Materno:</td>
            <td>CI:</td>
        </tr>
        <tr>
            <td>{{ $tutor['nombre'] }}</td>
            <td>{{ $tutor['apellidoPaterno'] }}</td>
            <td>{{ $tutor['apellidoMaterno'] }}</td>
            <td>{{ $tutor['ci'] }}</td>
        </tr>
        <tr class="header-row">
            <td>Profesión:</td>
            <td>Área:</td>
            <td>Colegio:</td>
            <td></td>
        </tr>
        <tr>
            <td>{{ $tutor['profesion'] }}</td>
            <td>{{ $tutor['areas'] }}</td>
            <td>{{ $tutor['colegio'] }}</td>
            <td></td>
        </tr>
    </table>

    <div>DATOS DE LOS ESTUDIANTES SEGÚN EL AREA Y CATEGORIA EN LA QUE PARTICIPAN</div>

    @foreach($inscripciones as $area => $categorias)
    @foreach($categorias as $categoria => $modalidades)
    @foreach($modalidades as $modalidad => $estudiantes)
    <table>
        <tr>
            <td colspan="3" class="table-header">AREA: {{ strtoupper($area) }}</td>
            <td colspan="3" class="table-header">CATEGORIA: {{ strtoupper($categoria) }}</td>
        </tr>
        <tr class="modalidad-row">
            <td colspan="3">Modalidad: {{ $modalidad }}</td>
            <td colspan="3">Precio: {{ $modalidad == 'Individual' ? '35' : ($modalidad == 'Duo' ? '25' : '15') }} Bs por Estudiante</td>
        </tr>
        <tr class="header-row">
            <td>Nº</td>
            <td>Nombre(s):</td>
            <td>Apellido Paterno:</td>
            <td>Apellido Materno:</td>
            <td>CI:</td>
            <td>Grado</td>
        </tr>

        @foreach($estudiantes as $index => $estudiante)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $estudiante->name }}</td>
            <td>{{ $estudiante->apellidoPaterno }}</td>
            <td>{{ $estudiante->apellidoMaterno }}</td>
            <td>{{ $estudiante->ci }}</td>
            <td>{{ $estudiante->grado }}</td>
        </tr>
        @endforeach

        <tr>
            <td colspan="4"></td>
            <td class="total-row">CANTIDAD</td>
            <td class="total-row">TOTAL</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td>{{ count($estudiantes) }}</td>
            <td>{{ count($estudiantes) * ($modalidad == 'Individual' ? 35 : ($modalidad == 'Duo' ? 25 : 15)) }} Bs</td>
        </tr>
    </table>
    @endforeach
    @endforeach
    @endforeach

    <!-- DETALLE -->
    <div>DETALLE:</div>
    <table class="details-table">
        <tr>
            <th>MODALIDAD</th>
            <th>CONCEPTO</th>
            <th>MONTO(Bs)</th>
        </tr>
        @foreach($detalles as $detalle)
        <tr>
            <td>{{ strtoupper($detalle->modalidad) }}</td>
            <td>Inscripción Area {{ $detalle->area }}, Categoria {{ $detalle->categoria }}</td>
            <td>{{ $detalle->total }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2">TOTAL A PAGAR:</td>
            <td>{{ $totalGeneral }}</td>
        </tr>
    </table>

    <div class="footer-text">
        Imprime esta hoja y debes dirigirte a cajas facultativas para realizar el pago de todos los estudiantes que tienes inscrito como DELEGADO.
    </div>
</body>

</html>