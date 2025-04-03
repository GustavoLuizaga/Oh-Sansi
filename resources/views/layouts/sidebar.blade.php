<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Derecho</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        /* Estilos para el sidebar */
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            right: 0;
            top: 0;
            background-color: red;
            padding-top: 20px;
            color: white;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: darkred;
        }
        .sidebar .active {
            background-color: darkred;
        }
        /* Añadir espacio al contenido principal */
        .content {
            margin-right: 260px; /* Dejar espacio para el sidebar */
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div>
    <ul >
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                🏠 Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('servicios') }}" class="{{ request()->is('servicios') ? 'active' : '' }}">
                ⚙️ Servicios
            </a>
        </li>
    </ul>

    <ul class="vertical-links">
        <li>
            <a href="#">Op1</a>
        </li>
        <li>
            <a href="#">Op2</a>
        </li>
        <li>
            <a href="#">Op3</a>
        </li>
    </ul>

    </div>


</body>