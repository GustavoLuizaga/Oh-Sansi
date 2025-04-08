<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $tituloPDF }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th {
            background-color: #0086CE;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .estado-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: bold;
            text-transform: uppercase;
        }
        .estado-publicada { background-color: #28a745; color: white; }
        .estado-borrador { background-color: #ffc107; color: #212529; }
        .estado-cancelada { background-color: #dc3545; color: white; }
        .estado-vencida { background-color: #6c757d; color: white; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h1>{{ $tituloPDF }}</h1>
    <table>
        <thead>
            <tr>
                <th>NOMBRE</th>
                <th>DESCRIPCIÓN</th>
                <th>FECHA INICIO</th>
                <th>FECHA FIN</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($convocatorias as $convocatoria)
            <tr>
                <td>{{ $convocatoria->nombre ?? 'N/A' }}</td>
                <td>{{ Str::limit($convocatoria->descripcion ?? 'Sin descripción', 50) }}</td>
                <td>{{ $convocatoria->fechaInicio ? \Carbon\Carbon::parse($convocatoria->fechaInicio)->format('d M, Y') : 'N/A' }}</td>
                <td>{{ $convocatoria->fechaFin ? \Carbon\Carbon::parse($convocatoria->fechaFin)->format('d M, Y') : 'N/A' }}</td>
                <td>
                    @if($convocatoria->estado)
                    <span class="estado-badge estado-{{ strtolower($convocatoria->estado) }}">
                        {{ $convocatoria->estado }}
                    </span>
                    @else
                    <span class="estado-badge" style="background-color: #6c757d;">
                        Sin estado
                    </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">No hay convocatorias disponibles</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>