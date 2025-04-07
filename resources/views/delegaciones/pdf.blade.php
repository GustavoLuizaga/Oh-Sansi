<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delegaciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h1 {
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Lista de Colegios</h1>
    <table>
        <thead>
            <tr>
                <th>Código SIE</th>
                <th>Nombre</th>
                <th>Departamento</th>
                <th>Provincia</th>
                <th>Municipio</th>
                <th>Dependencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($delegaciones as $delegacion)
            <tr>
                <td>{{ $delegacion->codigo_sie }}</td>
                <td>{{ $delegacion->nombre }}</td>
                <td>{{ $delegacion->departamento }}</td>
                <td>{{ $delegacion->provincia }}</td>
                <td>{{ $delegacion->municipio }}</td>
                <td>{{ $delegacion->dependencia }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>