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