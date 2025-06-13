<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
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
        
        .info-row {
            margin: 5px 0;
            clear: both;
        }
        
        .info-row span {
            text-decoration: underline;
            color: #FF0000;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        th {
            background-color: #000;
            color: #fff;
            padding: 5px;
            text-align: left;
            border: 1px solid #000;
        }
        
        td {
            border: 1px solid #000;
            padding: 5px;
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
    
    <div class="info-row">
        Fecha generación de la Orden de Pago: 27/04/2025 14:35
    </div>
    
    <div class="info-row">
        Referencia: Inscripciones Oh-Sansi!
    </div>
    
    <table>
        <tr>
            <th colspan="4">DATOS DE TUTOR</th>
        </tr>
        <tr>
            <td><strong>Nombre(s):</strong></td>
            <td><strong>Apellido Paterno:</strong></td>
            <td><strong>Apellido Materno:</strong></td>
            <td><strong>CI:</strong></td>            
        </tr>
        <tr>
            <td>Leonardo</td>
            <td>Pérez</td>
            <td>cayola</td>
            <td>9485400</td>
        </tr>
        <tr>
            <td><strong>Profesión:</strong></td>
            <td><strong>Área: </strong></td>
            <td><strong>Colegio: </strong></td>
            <td></td>
        </tr>
        <tr>
            <td>Ama de casa</td>
            <td>Informática</td>
            <td>Unidad Educativa Tupack Katari</td>
            <td></td>
        </tr>
    </table>
    
    <table>
        <tr>
            <th colspan="4">DATOS DE TUTOR</th>
        </tr>
        <tr>
            <td><strong>Nombre(s):</strong></td>
            <td><strong>Apellido Paterno:</strong></td>
            <td><strong>Apellido Materno:</strong></td>
            <td><strong>CI:</strong></td> 
        </tr>
        <tr>
            <td>Leyton</td>
            <td>Velasco</td>
            <td>Cayola</td>
            <td>9862123</td>
        </tr>
        <tr>
            <td><strong>Profesión:</strong></td>
            <td><strong>Área: </strong></td>
            <td><strong>Colegio: </strong></td>
            <td></td>
        </tr>
        <tr>
            <td>Ingeniero</td>
            <td>Biología</td>
            <td>Unidad Educativa Santa Cruz de la Sierra</td>
            <td></td>
        </tr>
    </table>
    
    <table>
        <tr>
            <th colspan="4">DATOS DEL ESTUDIANTE</th>
        </tr>
        <tr>
            <td><strong>Nombre(s):</strong></td>
            <td><strong>Apellido Paterno:</strong></td>
            <td><strong>Apellido Materno:</strong></td>
            <td><strong>CI:</strong></td> 
        </tr>
        <tr>
            <td>Kleberaso</td>
            <td>Velasco</td>
            <td>Muruchi</td>
            <td>9455600</td>
        </tr>
    </table>
    
    <table>
        <tr>
            <th colspan="3">DATOS DE INSCRIPCION DEL ESTUDIANTE</th>
        </tr>
        <tr>
            <td><strong>AREA: </strong></td>
            <td><strong>CATEGORIA:</strong></td>
            <td><strong>GRADO:</strong></td>
        </tr>
        <tr>
            <td>Informática</td>
            <td>Londra</td>
            <td>1ro de Secundaria</td>
        </tr>
        <tr>
            <td>Biología</td>
            <td>Builders</td>
            <td>1ro de Secundaria</td>
        </tr>
    </table>
    <div>
        
        DETALLE:
        
    </div>
    <table>
        <tr>
            <th>MODALIDAD</th>
            <th>CONCEPTO</th>
            <th>MONTO(Bs)</th>
        </tr>
        <tr>
            <td>INDIVIDUAL</td>
            <td>Inscripción en el Área: <span class="red-text">Informática </span>Categoria: <span class="red-text"> Londra</span></td>
            <td>15</td>
        </tr>
        <tr>
            <td>INDIVIDUAL</td>
            <td>Inscripción en el Área: <span class="red-text">Biologia </span>Categoria: <span class="red-text"> Builders</span></td>
            <td>20</td>
        </tr>
        <tr class="total-row">
            <td colspan="2">TOTAL A PAGAR:</td>
            <td>35</td>
        </tr>
    </table>
    
    <div class="footer">
        <p>Imprima esta hoja y diríjase a cajas facultativas para realizar el pago. Tenga en cuenta que el pago debe realizarlo una persona mayor de 18 años, un Tutor puede efectuar el pago de la inscripción.</p>
    </div>
</body>
</html>