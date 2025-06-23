@push('styles')
    <link rel="stylesheet" href="/css/areasCategorias.css">
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
    document.addEventListener('DOMContentLoaded', function() {
        // Variables de paginación
        let currentPage = 1;
        const areasPerPage = 10;
        let filteredAreas = [];
        let allAreas = [];

        // Elementos del DOM
        const searchInput = document.getElementById('searchInput');
        const orderSelect = document.getElementById('orderBy');
        const table = document.querySelector('.area-table');
        const tbody = table.querySelector('tbody');
        
        // Inicializar paginación
        initializeAreasPagination();

        function initializeAreasPagination() {
            // Agrupar filas por área
            groupRowsByArea();
            
            // Inicializar áreas filtradas
            filteredAreas = [...allAreas];
            
            // Crear contenedor de paginación
            createAreasPaginationContainer();
            
            // Mostrar primera página
            showAreasPage(1);
            
            // Configurar event listeners
            setupAreasEventListeners();
        }

        function groupRowsByArea() {
            const rows = Array.from(tbody.querySelectorAll('tr'));
            allAreas = [];
            let currentArea = null;

            rows.forEach(row => {
                const areaCell = row.querySelector('td.bg-gray-100');
                
                if (areaCell) {
                    // Es la primera fila de un área nueva
                    const areaName = areaCell.textContent.trim();
                    const rowspan = parseInt(areaCell.getAttribute('rowspan') || 1);
                    
                    currentArea = {
                        name: areaName,
                        rows: [row],
                        totalRows: rowspan
                    };
                    
                    allAreas.push(currentArea);
                } else if (currentArea && currentArea.rows.length < currentArea.totalRows) {
                    // Es una fila adicional del área actual
                    currentArea.rows.push(row);
                }
            });
        }

        function createAreasPaginationContainer() {
            // Verificar si ya existe el contenedor
            if (document.querySelector('.areas-categorias-pagination-container')) {
                return;
            }

            const container = document.querySelector('.bg-white.overflow-hidden');
            const paginationHTML = `
                <div class="areas-categorias-pagination-container">
                    <div class="areas-categorias-pagination-controls">
                        <button id="prevPageAreasCategorias" class="areas-categorias-pagination-btn" disabled>
                            <i class="fas fa-chevron-left"></i> Anterior
                        </button>
                        <div id="pageNumbersAreasCategorias" class="areas-categorias-page-numbers"></div>
                        <button id="nextPageAreasCategorias" class="areas-categorias-pagination-btn" disabled>
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="areas-categorias-pagination-info">
                        <span id="areasCategoriasInfo">Mostrando 0 de 0 áreas</span>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', paginationHTML);
        }

        function setupAreasEventListeners() {
            // Búsqueda mejorada
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    debounce(() => {
                        filterAndSortAreas();
                        currentPage = 1;
                        showAreasPage(currentPage);
                    }, 300)();
                });
            }

            // Ordenamiento
            if (orderSelect) {
                orderSelect.addEventListener('change', function() {
                    filterAndSortAreas();
                    currentPage = 1;
                    showAreasPage(currentPage);
                });
            }

            // Botones de paginación
            document.getElementById('prevPageAreasCategorias').addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    showAreasPage(currentPage);
                }
            });

            document.getElementById('nextPageAreasCategorias').addEventListener('click', function() {
                const totalPages = Math.ceil(filteredAreas.length / areasPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    showAreasPage(currentPage);
                }
            });
        }

        function filterAndSortAreas() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const orderBy = orderSelect ? orderSelect.value : 'name';

            // Filtrar áreas
            filteredAreas = allAreas.filter(area => {
                if (!searchTerm) return true;

                // Buscar en nombre del área
                if (area.name.toLowerCase().includes(searchTerm)) {
                    return true;
                }

                // Buscar en categorías y grados
                return area.rows.some(row => {
                    const categoryCell = row.querySelector('td:nth-child(2)');
                    const gradeCell = row.querySelector('td:last-child');
                    
                    // Buscar en categoría
                    if (categoryCell && categoryCell.textContent.toLowerCase().includes(searchTerm)) {
                        return true;
                    }
                    
                    // Buscar en grados
                    if (gradeCell) {
                        const gradePills = gradeCell.querySelectorAll('.grade-pill');
                        return Array.from(gradePills).some(pill => 
                            pill.textContent.toLowerCase().includes(searchTerm)
                        );
                    }
                    
                    return false;
                });
            });

            // Ordenar áreas
            switch(orderBy) {
                case 'name':
                    filteredAreas.sort((a, b) => a.name.localeCompare(b.name));
                    break;
                case 'level':
                    // Ordenar por número de categorías
                    filteredAreas.sort((a, b) => a.totalRows - b.totalRows);
                    break;
                default:
                    // Mantener orden original
                    break;
            }
        }

        function showAreasPage(page) {
            const startIndex = (page - 1) * areasPerPage;
            const endIndex = startIndex + areasPerPage;
            const totalPages = Math.ceil(filteredAreas.length / areasPerPage);

            // Obtener las áreas que deben mostrarse en esta página
            const pageAreas = filteredAreas.slice(startIndex, endIndex);
            
            // Ocultar todas las filas
            allAreas.forEach(area => {
                area.rows.forEach(row => {
                    row.style.display = 'none';
                });
            });
            
            // Mostrar solo las áreas de la página actual
            pageAreas.forEach(area => {
                area.rows.forEach(row => {
                    row.style.display = '';
                });
            });

            // Manejar mensaje de "no hay resultados"
            handleNoAreasResultsMessage();

            // Actualizar información y controles de paginación
            updateAreasInfo(startIndex, Math.min(endIndex, filteredAreas.length), filteredAreas.length);
            updateAreasPaginationControls(page, totalPages);
            updateAreasPageNumbers(page, totalPages);
        }

        function handleNoAreasResultsMessage() {
            let noDataRow = tbody.querySelector('.no-areas-results-row');
            
            if (filteredAreas.length === 0) {
                if (!noDataRow) {
                    noDataRow = document.createElement('tr');
                    noDataRow.innerHTML = '<td colspan="3" class="text-center no-results-cell">No se encontraron áreas que coincidan con la búsqueda</td>';
                    noDataRow.classList.add('no-areas-results-row');
                    tbody.appendChild(noDataRow);
                }
                noDataRow.style.display = '';
            } else if (noDataRow) {
                noDataRow.style.display = 'none';
            }
        }

        function updateAreasInfo(startIndex, endIndex, total) {
            const paginationInfo = document.getElementById('areasCategoriasInfo');
            if (total === 0) {
                paginationInfo.textContent = 'No hay áreas para mostrar';
            } else {
                paginationInfo.textContent = `Mostrando ${startIndex + 1}-${endIndex} de ${total} áreas`;
            }
        }

        function updateAreasPaginationControls(page, totalPages) {
            const prevBtn = document.getElementById('prevPageAreasCategorias');
            const nextBtn = document.getElementById('nextPageAreasCategorias');
            
            prevBtn.disabled = page <= 1;
            nextBtn.disabled = page >= totalPages || totalPages === 0;
            
            // Actualizar clases para estilos
            prevBtn.classList.toggle('disabled', page <= 1);
            nextBtn.classList.toggle('disabled', page >= totalPages || totalPages === 0);
        }

        function updateAreasPageNumbers(currentPage, totalPages) {
            const pageNumbersContainer = document.getElementById('pageNumbersAreasCategorias');
            pageNumbersContainer.innerHTML = '';

            if (totalPages <= 1) return;

            // Determinar rango de páginas a mostrar
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            // Ajustar si estamos cerca del inicio o final
            if (currentPage <= 3) {
                endPage = Math.min(5, totalPages);
            }
            if (currentPage >= totalPages - 2) {
                startPage = Math.max(1, totalPages - 4);
            }

            // Botón primera página
            if (startPage > 1) {
                createAreasPageButton(1, currentPage, pageNumbersContainer);
                if (startPage > 2) {
                    const ellipsis = document.createElement('span');
                    ellipsis.textContent = '...';
                    ellipsis.className = 'areas-categorias-page-ellipsis';
                    pageNumbersContainer.appendChild(ellipsis);
                }
            }

            // Botones de páginas
            for (let i = startPage; i <= endPage; i++) {
                createAreasPageButton(i, currentPage, pageNumbersContainer);
            }

            // Botón última página
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const ellipsis = document.createElement('span');
                    ellipsis.textContent = '...';
                    ellipsis.className = 'areas-categorias-page-ellipsis';
                    pageNumbersContainer.appendChild(ellipsis);
                }
                createAreasPageButton(totalPages, currentPage, pageNumbersContainer);
            }
        }

        function createAreasPageButton(pageNum, currentPage, container) {
            const button = document.createElement('button');
            button.textContent = pageNum;
            button.className = `areas-categorias-page-btn ${pageNum === currentPage ? 'active' : ''}`;
            button.addEventListener('click', function() {
                currentPage = pageNum;
                showAreasPage(currentPage);
            });
            container.appendChild(button);
        }

        // Función debounce para optimizar la búsqueda
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Export PDF button
        const exportPdfBtn = document.getElementById('exportPdf');
        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Mostrar todas las áreas antes de exportar
                allAreas.forEach(area => {
                    area.rows.forEach(row => {
                        row.style.display = '';
                    });
                });
                
                // Realizar la exportación
                const url = exportPdfBtn.getAttribute('data-url') || window.location.href.replace('/areasCategorias', '/areasCategorias/exportar/pdf');
                window.location.href = url;
                
                // Restaurar la paginación después de un breve delay
                setTimeout(() => {
                    showAreasPage(currentPage);
                }, 100);
            });
        }

        // Export Excel button
        const exportExcelBtn = document.getElementById('exportExcel');
        if (exportExcelBtn) {
            exportExcelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Mostrar todas las áreas antes de exportar
                allAreas.forEach(area => {
                    area.rows.forEach(row => {
                        row.style.display = '';
                    });
                });
                
                // Realizar la exportación
                const url = exportExcelBtn.getAttribute('data-url') || window.location.href.replace('/areasCategorias', '/areasCategorias/exportar/excel');
                window.location.href = url;
                
                // Restaurar la paginación después de un breve delay
                setTimeout(() => {
                    showAreasPage(currentPage);
                }, 100);
            });
        }

        // Función pública para reinicializar la paginación
        window.reinitializeAreasCategoriasP = function() {
            currentPage = 1;
            initializeAreasPagination();
        };
    });
</script>
