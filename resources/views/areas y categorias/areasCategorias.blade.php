@push('styles')
    <link rel="stylesheet" href="{{ asset('css/areasCategorias.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <h1><i class="fas fa-book"></i> {{ __('Gestión de Áreas, Categorías y Grados') }}</h1>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <!-- Botones -->
            <div class="action-buttons">
                <div>
                    <a href="{{ route('areas.index') }}" class="action-btn">
                        <i class="fas fa-th-large"></i> Gestionar Áreas
                    </a>
                    <a href="{{ route('categorias.index') }}" class="action-btn">
                        <i class="fas fa-tags"></i> Gestionar Categorías
                    </a>
                </div>
                <div class="export-buttons">
                    <button type="button" class="export-button pdf" id="exportPdf">
                        <i class="fas fa-file-pdf"></i> Descargar PDF
                    </button>
                    
                    <button type="button" class="export-button excel" id="exportExcel">
                        <i class="fas fa-file-excel"></i> Descargar Excel
                    </button>
                </div>
            </div>

            <!-- Filtros -->
            <div class="search-filter">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Buscar...">
                </div>
                <div class="filter-dropdown">
                    <label for="orderBy" class="mr-2">Ordenar por:</label>
                    <select id="orderBy">
                        <option value="name">Nombre</option>
                        <option value="level">Nivel/Categoría</option>
                    </select>
                </div>
            </div>

            @if(isset($message))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                    <p>{{ $message }}</p>
                </div>
            @else
                <!-- Información de la convocatoria -->
                <div class="convocatoria-header">
                    <h5>
                        <span class="convocatoria-label">Convocatoria Publicada:</span> 
                        <a href="{{ url('/convocatoria/' . $convocatoriaActiva->id) }}" class="convocatoria-nombre">
                            {{ $convocatoriaActiva->nombre }}
                        </a>
                    </h5>
                </div>
                <!-- Tabla -->
                <table class="area-table w-full text-left border">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="w-1/4">Área</th>
                            <th class="w-1/4">Categoría</th>
                            <th class="w-1/4">Grados</th>
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
                                            @foreach($categoria->grados as $grado)
                                                <span class="grade-pill">{{ $grado->grado }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const table = document.querySelector('.area-table');
        const rows = table.querySelectorAll('tbody tr');

        searchInput.addEventListener('input', function() {
            const searchTerm = searchInput.value.toLowerCase();
            
            const areaRows = {};
            
            // Agrupar filas por área
            rows.forEach(row => {
                const areaCell = row.querySelector('td.bg-gray-100');
                if (areaCell) {
                    const areaName = areaCell.textContent.toLowerCase();
                    if (!areaRows[areaName]) {
                        areaRows[areaName] = [];
                    }
                    // Encontrar todas las filas que pertenecen a esta área
                    const rowspan = parseInt(areaCell.getAttribute('rowspan') || 1);
                    let currentRow = row;
                    for (let i = 0; i < rowspan; i++) {
                        if (currentRow) {
                            areaRows[areaName].push(currentRow);
                            currentRow = currentRow.nextElementSibling;
                        }
                    }
                }
            });
            
            // Filtrar por término de búsqueda
            for (const areaName in areaRows) {
                const matchesArea = areaName.includes(searchTerm);
                let matchesCategory = false;
                let matchesGrade = false;
                
                // Verificar si alguna categoría o grado coincide
                areaRows[areaName].forEach(row => {
                    const categoryCell = row.querySelector('td:nth-child(2)');
                    const gradeCell = row.querySelector('td:last-child');
                    
                    if (categoryCell && categoryCell.textContent.toLowerCase().includes(searchTerm)) {
                        matchesCategory = true;
                    }
                    
                    if (gradeCell) {
                        const gradePills = gradeCell.querySelectorAll('.grade-pill');
                        gradePills.forEach(pill => {
                            if (pill.textContent.toLowerCase().includes(searchTerm)) {
                                matchesGrade = true;
                            }
                        });
                    }
                });
                
                // Mostrar u ocultar todas las filas de esta área
                const shouldShow = matchesArea || matchesCategory || matchesGrade;
                areaRows[areaName].forEach(row => {
                    row.style.display = shouldShow ? '' : 'none';
                });
            }
        });

        // Export PDF button
        document.getElementById('exportPdf').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = "{{ route('areasCategorias.exportar.pdf') }}";
        });
        // Export Excel button
        document.getElementById('exportExcel').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = "{{ route('areasCategorias.exportar.excel') }}";
        }); 
        
    });
</script>
