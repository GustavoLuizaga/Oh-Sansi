<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Delegaciones</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            color: #0086CE;
            /* Primary color */
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .title {
            color: #002A4C;
            /* Primary dark */
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #B30D1F;
            /* Secondary color */
            font-size: 16px;
            margin-bottom: 5px;
        }

        .filters {
            color: #343a40;
            /* Dark gray */
            font-size: 14px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #0086CE;
            /* Primary color */
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
            /* Light gray */
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo">Oh! Sansi</div>
        <div class="title">{{ $filterInfo['title'] }}</div>

        @if(count($filterInfo['filters']) > 0)
        <div class="subtitle">Filtros aplicados:</div>
        <div class="filters">
            {{ implode(' | ', $filterInfo['filters']) }}
        </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Código SIE</th>
                <th>Nombre de Colegio</th>
                <th>Departamento</th>
                <th>Provincia</th>
                <th>Municipio</th>
                <th>Dependencia</th>
            </tr>
        </thead>
        <tbody>
            @forelse($delegaciones as $delegacion)
            <tr>
                <td>{{ $delegacion->codigo_sie }}</td>
                <td>{{ $delegacion->nombre }}</td>
                <td>{{ $delegacion->departamento }}</td>
                <td>{{ $delegacion->provincia }}</td>
                <td>{{ $delegacion->municipio }}</td>
                <td>{{ $delegacion->dependencia }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No hay colegios registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento generado el {{ date('d/m/Y H:i:s') }}
    </div>
</body>

</html>