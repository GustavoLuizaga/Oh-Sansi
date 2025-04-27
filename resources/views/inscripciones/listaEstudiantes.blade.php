<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/inscripcion/listaEstudiantes.css') }}">

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-1">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(request()->has('deleted') && request()->deleted == 'true')
    <div class="alert alert-success py-1 px-2 mb-1">
        Estudiante eliminado correctamente.
    </div>
    @endif

    <!-- Header Section -->
    <div class="estudiantes-header py-2">
        <h1><i class="fas fa-user-graduate"></i> {{ __('Administrar Estudiantes') }}</h1>
    </div>

    <!-- Actions Container (Add and Export buttons in the same row) -->
    <div class="actions-container mb-1">
        <div class="button-group">
            <a href="{{ route('estudiantes.agregar') }}" class="add-button py-1 px-2">
                <i class="fas fa-plus"></i> Agregar Estudiante
            </a>
            <a href="{{ route('estudiantes.pendientes') }}" class="pending-button py-1 px-2">
                <i class="fas fa-clock"></i> Estudiantes Pendientes
            </a>
        </div>
        <div class="search-filter-container mb-1">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Nombre o CI" value="{{ request('search') }}" class="py-1">
                <button type="submit" class="search-button py-1 px-2">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
        <div class="export-buttons">
            <button type="button" class="export-button pdf py-1 px-2" id="exportPdf">
                <i class="fas fa-file-pdf"></i> PDF
            </button>

            <button type="button" class="export-button excel py-1 px-2" id="exportExcel">
                <i class="fas fa-file-excel"></i> Excel
            </button>
        </div>
    </div>

    <!-- Search and Filter -->
    <form action="{{ route('estudiantes.lista') }}" method="GET" id="filterForm">
        <div class="filter-container mb-2 py-1 px-2">
            <div class="filter-group">
                <label for="convocatoria" class="text-xs mb-1">Convocatoria:</label>
                <select class="filter-select py-1" name="convocatoria" id="convocatoria">
                    <option value="">Todas</option>
                    @foreach($convocatorias as $convocatoria)
                    <option value="{{ $convocatoria->idConvocatoria }}" {{ request('convocatoria') == $convocatoria->idConvocatoria ? 'selected' : '' }}>
                        {{ $convocatoria->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="area" class="text-xs mb-1">Área:</label>
                <select class="filter-select py-1" name="area" id="area">
                    <option value="">Todas</option>
                    @foreach($areas as $area)
                    <option value="{{ $area->idArea }}" {{ request('area') == $area->idArea ? 'selected' : '' }}>
                        {{ $area->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="categoria" class="text-xs mb-1">Categoría:</label>
                <select class="filter-select py-1" name="categoria" id="categoria">
                    <option value="">Todas</option>
                    @foreach($categorias as $categoria)
                    <option value="{{ $categoria->idCategoria }}" {{ request('categoria') == $categoria->idCategoria ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="delegacion" class="text-xs mb-1">Colegio:</label>
                <select class="filter-select py-1" name="delegacion" id="delegacion">
                    <option value="">Todos</option>
                    @foreach($delegaciones as $delegacion)
                    <option value="{{ $delegacion->idDelegacion }}" {{ request('delegacion') == $delegacion->idDelegacion ? 'selected' : '' }}>
                        {{ $delegacion->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <!-- Table -->
    <table class="estudiantes-table">
        <thead>
            <tr>
                <th>CI</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Colegio</th>
                <th>Área</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($estudiantes as $estudiante)
            <tr>
                <td>{{ $estudiante->user->ci }}</td>
                <td>{{ $estudiante->user->name }}</td>
                <td>{{ $estudiante->user->apellidoPaterno }} {{ $estudiante->user->apellidoMaterno }}</td>
                <td>{{ $estudiante->inscripciones->first()->delegacion->nombre ?? 'No asignado' }}</td>
                <td>{{ $estudiante->inscripciones->first()->area->nombre ?? 'No asignado' }}</td>
                <td>{{ $estudiante->inscripciones->first()->categoria->nombre ?? 'No asignado' }}</td>
                <td class="actions">
                    <div class="flex space-x-1">
                        <a href="{{ route('estudiantes.ver', $estudiante->id) }}" class="action-button view w-5 h-5">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        <a href="{{ route('estudiantes.editar', $estudiante->id) }}" class="action-button edit w-5 h-5">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                        <a href="#" class="action-button delete-button w-5 h-5" data-id="{{ $estudiante->id }}" data-nombre="{{ $estudiante->user->name }}">
                            <i class="fas fa-trash text-xs"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No hay estudiantes registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        {{ $estudiantes->appends(request()->query())->links() }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const convocatoriaSelect = document.getElementById('convocatoria');
            const areaSelect = document.getElementById('area');
            const categoriaSelect = document.getElementById('categoria');
            const delegacionSelect = document.getElementById('delegacion');
            const filterForm = document.getElementById('filterForm');

            // Actualizar filtros cuando cambian los selects
            convocatoriaSelect.addEventListener('change', function() {
                filterForm.submit();
            });

            areaSelect.addEventListener('change', function() {
                filterForm.submit();
            });

            categoriaSelect.addEventListener('change', function() {
                filterForm.submit();
            });

            delegacionSelect.addEventListener('change', function() {
                filterForm.submit();
            });

            // Exportar a PDF
            document.getElementById('exportPdf').addEventListener('click', function() {
                window.location.href = '{{ route("estudiantes.exportPdf") }}' + '?' + new URLSearchParams(new FormData(filterForm)).toString();
            });

            // Exportar a Excel
            document.getElementById('exportExcel').addEventListener('click', function() {
                window.location.href = '{{ route("estudiantes.exportExcel") }}' + '?' + new URLSearchParams(new FormData(filterForm)).toString();
            });

            // Eliminar estudiante
            const deleteButtons = document.querySelectorAll('.delete-button');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const nombre = this.dataset.nombre;
                    
                    if (confirm(`¿Está seguro que desea eliminar al estudiante ${nombre}?`)) {
                        window.location.href = `{{ url('/estudiantes/eliminar') }}/${id}`;
                    }
                });
            });
        });
    </script>
</x-app-layout>