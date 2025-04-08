<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $tituloPDF }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        h1 {
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>{{ $tituloPDF }}</h1>
    <!-- Tabla -->
    <table class="area-table w-full text-left border">
        <thead>
            <tr class="bg-gray-200">
                <th class="w-1/4">Área</th>
                <th class="w-1/4">Categoría</th>
                <th>Grados</th>
            </tr>
        </thead>
        <tbody>
            @foreach($areas as $area)
                @php $firstCategory = true; @endphp
                @foreach($area->categorias as $index => $categoria)
                    <tr>
                        @if($firstCategory)
                            <td rowspan="{{ count($area->categorias) }}" class="bg-gray-100 font-bold align-top">{{ $area->nombre }}</td>
                            @php $firstCategory = false; @endphp
                        @endif
                        <td>{{ $categoria->nombre }}</td>
                        <td>
                            <div class="grades-list">
                                {{ $categoria->grados->pluck('grado')->implode(', ') }}
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>