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
            <p>CODIGO ORDEN DE PAGO:</p>
            <p>456123789</p>
        </div>
    </div>
    
    <div class="title">ORDEN DE PAGO</div>

    <!-- Resto del contenido del segundo documento -->
    <div class="date">Fecha generación de Orden de Pago: 27/04/2025</div>
    <div class="reference">Referencia: Inscripciones Oh Sansi</div>

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
            <td>Leonardo</td>
            <td>Perez</td>
            <td>cayola</td>
            <td>9485400</td>
        </tr>
        <tr class="header-row">
            <td>Profesión:</td>
            <td>Área:</td>
            <td>Colegio:</td>
            <td></td>
        </tr>
        <tr>
            <td>Ama de casa</td>
            <td>Informatica Matematicas, etc.</td>
            <td>Unidad Educativa Tupac Katari</td>
            <td></td>
        </tr>
    </table>

    <div>DATOS DE LOS ESTUDIANTES SEGÚN EL AREA Y CATEGORIA EN LA QUE PARTICIPAN</div>

    <!-- AREA: INFORMATICA - CATEGORIA: LONDRA - INDIVIDUAL -->
    <table>
        <tr>
            <td colspan="3" class="table-header">AREA: INFORMATICA</td>
            <td colspan="3" class="table-header">CATEGORIA: LONDRA</td>
        </tr>
        <tr class="modalidad-row">
            <td colspan="3">Modalidad: Individual</td>
            <td colspan="3">Precio: 35 Bs por Estudiante</td>
        </tr>
        <tr class="header-row">
            <td>Nº</td>
            <td>Nombre(s):</td>
            <td>Apellido Paterno:</td>
            <td>Apellido Materno:</td>
            <td>CI:</td>
            <td>Grado</td>
        </tr>
        <tr>
            <td>1</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>1ro de Secundaria</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>2do de Secundaria</td>
        </tr>
        <tr>
            <td>3</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>2do de Secundaria</td>
        </tr>
        <tr>
            <td>4</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>3ro de Secundaria</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td class="total-row">CANTIDAD</td>
            <td class="total-row">TOTAL</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td>4</td>
            <td>140 Bs</td>
        </tr>
    </table>

    <!-- AREA: INFORMATICA - CATEGORIA: LONDRA - DUO -->
    <table>
        <tr>
            <td colspan="3" class="table-header">AREA: INFORMATICA</td>
            <td colspan="3" class="table-header">CATEGORIA: LONDRA</td>
        </tr>
        <tr class="modalidad-row">
            <td colspan="3">Modalidad: Duo</td>
            <td colspan="3">Precio: 25 Bs por Estudiante</td>
        </tr>
        <tr class="header-row">
            <td>Nº</td>
            <td>Nombre(s):</td>
            <td>Apellido Paterno:</td>
            <td>Apellido Materno:</td>
            <td>CI:</td>
            <td>Grado</td>
        </tr>
        <tr>
            <td>1</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>1ro de Secundaria</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>2do de Secundaria</td>
        </tr>
        <tr>
            <td>3</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>2do de Secundaria</td>
        </tr>
        <tr>
            <td>4</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>3ro de Secundaria</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td class="total-row">CANTIDAD</td>
            <td class="total-row">TOTAL</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td>4</td>
            <td>100 Bs</td>
        </tr>
    </table>

    <!-- AREA: INFORMATICA - CATEGORIA: LONDRA - GRUPAL -->
    <table>
        <tr>
            <td colspan="3" class="table-header">AREA: INFORMATICA</td>
            <td colspan="3" class="table-header">CATEGORIA: LONDRA</td>
        </tr>
        <tr class="modalidad-row">
            <td colspan="3">Modalidad: Grupal</td>
            <td colspan="3">Precio: 15 Bs por Estudiante</td>
        </tr>
        <tr class="header-row">
            <td>Nº</td>
            <td>Nombre(s):</td>
            <td>Apellido Paterno:</td>
            <td>Apellido Materno:</td>
            <td>CI:</td>
            <td>Grado</td>
        </tr>
        <tr>
            <td>1</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>1ro de Secundaria</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>2do de Secundaria</td>
        </tr>
        <tr>
            <td>3</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>2do de Secundaria</td>
        </tr>
        <tr>
            <td>4</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>3ro de Secundaria</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td class="total-row">CANTIDAD</td>
            <td class="total-row">TOTAL</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td>4</td>
            <td>60 Bs</td>
        </tr>
    </table>

    <!-- AREA: INFORMATICA - CATEGORIA: JAGUAR - INDIVIDUAL -->
    <table>
        <tr>
            <td colspan="3" class="table-header">AREA: INFORMATICA</td>
            <td colspan="3" class="table-header">CATEGORIA: JAGUAR</td>
        </tr>
        <tr class="modalidad-row">
            <td colspan="3">Modalidad: Individual</td>
            <td colspan="3">Precio: 35 Bs por Estudiante</td>
        </tr>
        <tr class="header-row">
            <td>Nº</td>
            <td>Nombre(s):</td>
            <td>Apellido Paterno:</td>
            <td>Apellido Materno:</td>
            <td>CI:</td>
            <td>Grado</td>
        </tr>
        <tr>
            <td>1</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>1ro de Secundaria</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td class="total-row">CANTIDAD</td>
            <td class="total-row">TOTAL</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td>1</td>
            <td>35 Bs</td>
        </tr>
    </table>

    <!-- AREA: INFORMATICA - CATEGORIA: JAGUAR - DUO -->
    <table>
        <tr>
            <td colspan="3" class="table-header">AREA: INFORMATICA</td>
            <td colspan="3" class="table-header">CATEGORIA: JAGUAR</td>
        </tr>
        <tr class="modalidad-row">
            <td colspan="3">Modalidad: Duo</td>
            <td colspan="3">Precio: 25 Bs por Estudiante</td>
        </tr>
        <tr class="header-row">
            <td>Nº</td>
            <td>Nombre(s):</td>
            <td>Apellido Paterno:</td>
            <td>Apellido Materno:</td>
            <td>CI:</td>
            <td>Grado</td>
        </tr>
        <tr>
            <td>1</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>1ro de Secundaria</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>2do de Secundaria</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td class="total-row">CANTIDAD</td>
            <td class="total-row">TOTAL</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td>2</td>
            <td>50 Bs</td>
        </tr>
    </table>

    <!-- AREA: INFORMATICA - CATEGORIA: JAGUAR - GRUPAL -->
    <table>
        <tr>
            <td colspan="3" class="table-header">AREA: INFORMATICA</td>
            <td colspan="3" class="table-header">CATEGORIA: JAGUAR</td>
        </tr>
        <tr class="modalidad-row">
            <td colspan="3">Modalidad: Grupal</td>
            <td colspan="3">Precio: 15 Bs por Estudiante</td>
        </tr>
        <tr class="header-row">
            <td>Nº</td>
            <td>Nombre(s):</td>
            <td>Apellido Paterno:</td>
            <td>Apellido Materno:</td>
            <td>CI:</td>
            <td>Grado</td>
        </tr>
        <tr>
            <td>1</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>1ro de Secundaria</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
            <td>2do de Secundaria</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td class="total-row">CANTIDAD</td>
            <td class="total-row">TOTAL</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td>2</td>
            <td>30 Bs</td>
        </tr>
    </table>

    <!-- DETALLE -->
    <div>DETALLE:</div>
    <table class="details-table">
        <tr>
            <th>MODALIDAD</th>
            <th>CONCEPTO</th>
            <th>MONTO(Bs)</th>
        </tr>
        <tr>
            <td>INDIVIDUAL, DUO, GRUPAL</td>
            <td>Inscripcion Area Informatica, Categoria Londra</td>
            <td>300</td>
        </tr>
        <tr>
            <td>INDIVIDUAL, DUO, GRUPAL</td>
            <td>Inscripcion Area Informatica, Categoria Jaguar</td>
            <td>115</td>
        </tr>
        <tr class="total-row">
            <td colspan="2">TOTAL A PAGAR:</td>
            <td>415</td>
        </tr>
    </table>

    <div class="footer-text">
        Imprime esta hoja y debes dirigirte a cajas facultativas para realizar el pago de todos los estudiantes que tienes inscrito como DELEGADO.
    </div>
</body>
</html>