<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $tituloPDF }}</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin-top: 2cm;
            margin-bottom: 2cm;
            margin-left: 2cm;
            margin-right: 2cm;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            position: relative;
            z-index: 2;
        }
        
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
        }
        
        h1 {
            text-align: center;
            color: #333;
            position: relative;
            z-index: 2;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: 1;
            pointer-events: none;
            width: 500px; /* Tamaño reducido de la imagen */
            height: auto;
        }
        
        .content {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>
    {{-- <!-- Marca de agua (logo) -->
    <div class="watermark">
        <img src="{{ public_path('img/Logo UMSS-TRIANGULO.png') }}" alt="Logo UMSS" style="width: 100%; height: auto;">
    </div> --}}
    
    <div class="content">
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
    </div>
</body>
</html>