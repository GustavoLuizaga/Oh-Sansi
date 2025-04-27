<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/inscripcion/listaEstudiantes.css') }}">

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-1">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Header Section -->
    <div class="estudiantes-header py-2">
        <h1><i class="fas fa-clock"></i> {{ __('Estudiantes Pendientes de Inscripción') }}</h1>
    </div>

    <!-- Actions Container -->
    <div class="actions-container mb-1">
        <div class="button-group">
            <a href="{{ route('estudiantes.lista') }}" class="add-button py-1 px-2">
                <i class="fas fa-arrow-left"></i> Volver a Lista de Estudiantes
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
                <i class="fas fa-file-pdf"></i> Generar orden de pago
            </button>

            <button type="button" class="export-button excel py-1 px-2" id="exportExcel">
                <i class="fas fa-file-excel"></i> Subir comprobante de pago
            </button>
        </div>
    </div>

    <!-- Search and Filter -->
    <form action="{{ route('estudiantes.pendientes') }}" method="GET" id="filterForm">
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
        </div>
    </form>

    <!-- Table -->
    <table class="estudiantes-table">
        <thead>
            <tr>
                <th>CI</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Estado</th>
                <th>Fecha de Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($estudiantes as $estudiante)
            <tr>
                <td>{{ $estudiante->user->ci }}</td>
                <td>{{ $estudiante->user->name }}</td>
                <td>{{ $estudiante->user->apellidoPaterno }} {{ $estudiante->user->apellidoMaterno }}</td>
                <td>
                    <span class="status-badge pending">Pendiente</span>
                </td>
                <td>{{ $estudiante->user->created_at->format('d/m/Y') }}</td>
                <td class="actions">
                    <div class="flex space-x-1">
                        <a href="{{ route('estudiantes.ver', $estudiante->id) }}" class="action-button view w-5 h-5">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        <a href="{{ route('estudiantes.completar', $estudiante->id) }}" class="action-button edit w-5 h-5">
                            <i class="fas fa-check text-xs"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No hay estudiantes pendientes de inscripción</td>
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
            const filterForm = document.getElementById('filterForm');

            // Actualizar filtros cuando cambian los selects
            convocatoriaSelect.addEventListener('change', function() {
                filterForm.submit();
            });

            // Exportar a PDF
            document.getElementById('exportPdf').addEventListener('click', function() {
                window.location.href = '{{ route("estudiantes.pendientes.exportPdf") }}' + '?' + new URLSearchParams(new FormData(filterForm)).toString();
            });

            // Exportar a Excel
            document.getElementById('exportExcel').addEventListener('click', function() {
                window.location.href = '{{ route("estudiantes.pendientes.exportExcel") }}' + '?' + new URLSearchParams(new FormData(filterForm)).toString();
            });
        });
    </script>
</x-app-layout>