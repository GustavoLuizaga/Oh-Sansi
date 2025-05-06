<style>
    .modal-cuerpo {
        padding: 5px;
        max-width: 700px;
        margin: 0 auto;
        background-color: #ffffff;
    }

    .card-area {
        background-color: #f8fafc;
        border-radius: 8px;
        margin-bottom: 15px;
        padding: 15px;
        border-left: 4px solid #2b6cb0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .titulo-area {
        color: #2b6cb0;
        font-size: 30px;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
    }

    .card-categoria {
        background-color: #ffffff;
        border-radius: 6px;
        padding: 10px;
        margin: 8px 0;
        border-left: 3px solid #4299e1;
    }

    .titulo-categoria {
        color: #2d3748;
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .lista-grados {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .card-grado {
        background-color: #f0f9ff;
        border-radius: 4px;
        padding: 6px 10px;
        margin: 4px 0;
        font-size: 12px;
        color: #000000;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .card-grado:hover {
        background-color: #e0f2fe;
        transform: translateX(2px);
    }

    @media (max-width: 768px) {
        .modal-cuerpo {
            padding: 15px;
            margin: 10px;
        }

        .card-area {
            margin-bottom: 10px;
        }

        .card-grado {
            font-size: 11px;
            padding: 4px 8px;
        }
    }
</style>

<div id="contenidoModal" class="modal-cuerpo">
    <h2>Información actual de la convocatoria</h2>
    @foreach($resultado as $areaData)
    <div class="card-area">
        <h1 class="titulo-area">{{ $areaData['area']->nombre }}</h1>

        @foreach($areaData['categorias'] as $categoriaData)
        <div class="card-categoria">
            <h3 class="titulo-categoria">{{ $categoriaData['categoria']->nombre }}</h3>

            <ul class="lista-grados">
                @foreach($categoriaData['grados'] as $grado)
                <li class="card-grado">{{ $grado->grado }}</li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>
    @endforeach
</div>