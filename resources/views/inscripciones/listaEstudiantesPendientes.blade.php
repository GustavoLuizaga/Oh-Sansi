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
            <button type="button" class="export-button pdf py-1 px-2" id="generarOrdenPago">
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
            @if(!$esTutor)
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
            @endif

            <div class="filter-group">
                <label for="modalidad" class="text-xs mb-1">Modalidad:</label>
                <select class="filter-select py-1" name="modalidad" id="modalidad">
                    <option value="">Todas</option>
                    @foreach($modalidades as $modalidad)
                    <option value="{{ $modalidad }}" {{ request('modalidad') == $modalidad ? 'selected' : '' }}>
                        {{ ucfirst($modalidad) }}
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
            const filterForm = document.getElementById('filterForm');
            const selectElements = filterForm.querySelectorAll('select');

            // Actualizar filtros cuando cambian los selects
            selectElements.forEach(select => {
                select.addEventListener('change', function() {
                    filterForm.submit();
                });
            });

<<<<<<< HEAD
            // Exportar a PDF (Generar orden de pago)
=======
            // Exportar a PDF
>>>>>>> 8a448c88e87a3ff9f68e62b972e0cdde2f36b41e
            document.getElementById('generarOrdenPago').addEventListener('click', function() {
                try {
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
<<<<<<< HEAD
=======

                    // Realizar la solicitud usando fetch
                    fetch('{{ route("boleta.preview") }}', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/pdf'
                            }
                        })
                        .then(response => response.blob())
                        .then(blob => {
                            // Crear un objeto URL para el blob
                            const url = window.URL.createObjectURL(blob);
                            // Crear un enlace temporal
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = url;
                            a.download = 'orden-de-pago.pdf';

                            // Agregar al documento y hacer clic
                            document.body.appendChild(a);
                            a.click();

                            // Limpiar
                            window.URL.revokeObjectURL(url);
                            document.body.removeChild(a);

                            // Restaurar el botón
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-file-pdf"></i> Generar orden de pago';
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('mensajeError').style.display = 'block';
                            document.getElementById('mensajeErrorTexto').textContent = 'Error al generar la orden de pago';

                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-file-pdf"></i> Generar orden de pago';
                        });
                } catch (error) {
                    console.error('Error:', error);
                    document.getElementById('mensajeError').style.display = 'block';
                    document.getElementById('mensajeErrorTexto').textContent = 'Error al generar la orden de pago';

                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-file-pdf"></i> Generar orden de pago';
                }
            });
>>>>>>> 8a448c88e87a3ff9f68e62b972e0cdde2f36b41e

                    // Realizar la solicitud usando fetch
                    fetch('{{ route("boleta.preview") }}', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/pdf'
                            }
                        })
                        .then(response => response.blob())
                        .then(blob => {
                            // Crear un objeto URL para el blob
                            const url = window.URL.createObjectURL(blob);
                            // Crear un enlace temporal
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = url;
                            a.download = 'orden-de-pago.pdf';

                            // Agregar al documento y hacer clic
                            document.body.appendChild(a);
                            a.click();

                            // Limpiar
                            window.URL.revokeObjectURL(url);
                            document.body.removeChild(a);

                            // Restaurar el botón
                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-file-pdf"></i> Generar orden de pago';
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('mensajeError').style.display = 'block';
                            document.getElementById('mensajeErrorTexto').textContent = 'Error al generar la orden de pago';

                            this.disabled = false;
                            this.innerHTML = '<i class="fas fa-file-pdf"></i> Generar orden de pago';
                        });
                } catch (error) {
                    console.error('Error:', error);
                    document.getElementById('mensajeError').style.display = 'block';
                    document.getElementById('mensajeErrorTexto').textContent = 'Error al generar la orden de pago';

                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-file-pdf"></i> Generar orden de pago';
                }
            });
        });
    </script>
</x-app-layout>