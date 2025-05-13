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
    <div class="actions-container">
        <div class="button-group">
            <a href="{{ route('estudiantes.lista') }}" class="back-button">
                <i class="fas fa-arrow-left"></i>
                <span>Volver a Lista</span>
            </a>
        </div>

        <div class="search-filter-container">
            <form action="{{ route('estudiantes.pendientes') }}" method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Buscar por nombre o CI..." value="{{ request('search') }}">
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                    <span>Buscar</span>
                </button>
            </form>
        </div>

        <div class="export-buttons">
            <button type="button" class="export-button payment" id="generarOrdenPago">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Generar Orden</span>
            </button>

            <button type="button" class="export-button upload" id="exportExcel">
                <i class="fas fa-receipt"></i>
                <span>Subir Comprobante</span>
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
                    <div class="action-buttons">
                        <!-- Change this line 
                        <a href="javascript:void(0)" onclick="verEstudiante({{ $estudiante->id }})" class="action-button view" title="Visualizar">
                        -->
                        <!-- To this -->
                        <a href="#" onclick="verEstudiante('{{ $estudiante->id }}'); return false;" class="action-button view" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('estudiantes.completar', $estudiante->id) }}" class="action-button edit" title="Editar">
                            <i class="fas fa-edit"></i>
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

    <!-- Modal de Visualización -->
    <div id="modalVerEstudiante" class="modal">
        <div class="modal-contenido">
            <button type="button" class="modal-cerrar" onclick="cerrarModalVer()">
                <i class="fas fa-times"></i>
            </button>
            <h2 class="modal-titulo">
                <i class="fas fa-user-graduate"></i>
                Detalles del Estudiante
            </h2>
            <div class="estudiante-detalles">
                <div class="info-section">
                    <div class="info-grupo">
                        <h3>Información Personal</h3>
                        <p><strong>CI:</strong> <span id="verCI"></span></p>
                        <p><strong>Nombre:</strong> <span id="verNombre"></span></p>
                        <p><strong>Apellidos:</strong> <span id="verApellidos"></span></p>
                        <p><strong>Fecha de Registro:</strong> <span id="verFechaRegistro"></span></p>
                    </div>
                    <div class="info-grupo">
                        <h3>Información Académica</h3>
                        <p><strong>Delegación:</strong> <span id="verDelegacion"></span></p>
                        <p><strong>Área:</strong> <span id="verArea"></span></p>
                        <p><strong>Categoría:</strong> <span id="verCategoria"></span></p>
                        <p><strong>Modalidad:</strong> <span id="verModalidad"></span></p>
                        <div id="infoGrupo" style="display: none;">
                            <h4 class="mt-2 text-sm font-semibold">Información del Grupo</h4>
                            <p><strong>Nombre del Grupo:</strong> <span id="verNombreGrupo"></span></p>
                            <p><strong>Código de Invitación:</strong> <span id="verCodigoGrupo"></span></p>
                            <p><strong>Estado:</strong> <span id="verEstadoGrupo"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edición -->
    <div id="modalEditarEstudiante" class="modal">
        <div class="modal-contenido">
            <button type="button" class="modal-cerrar" onclick="cerrarModalEditar()">
                <i class="fas fa-times"></i>
            </button>
            <h2 class="modal-titulo">
                <i class="fas fa-edit"></i>
                Editar Estudiante
            </h2>
            <form id="formEditarEstudiante" class="form-editar">
                @csrf
                @method('PUT')
                <input type="hidden" id="editEstudianteId" name="id">
                
                <div class="form-grupo">
                    <div class="input-group">
                        <label for="editArea">Área:</label>
                        <select id="editArea" name="idArea" required>
                            <option value="">Seleccione un área</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->idArea }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="editCategoria">Categoría:</label>
                        <select id="editCategoria" name="idCategoria" required>
                            <option value="">Seleccione una categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->idCategoria }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="editModalidad">Modalidad:</label>
                        <select id="editModalidad" name="modalidad" required>
                            <option value="">Seleccione una modalidad</option>
                            @foreach($modalidades as $modalidad)
                                <option value="{{ $modalidad }}">{{ ucfirst($modalidad) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancelar" onclick="cerrarModalEditar()">Cancelar</button>
                    <button type="submit" class="btn-guardar">
                        <i class="fas fa-save"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        {{ $estudiantes->appends(request()->query())->links() }}
    </div>

    @push('scripts')
    <script>
        function verEstudiante(id) {
            fetch(`/estudiantes/ver/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const estudiante = data.estudiante;
                    document.getElementById('verCI').textContent = estudiante.ci;
                    document.getElementById('verNombre').textContent = estudiante.nombre;
                    document.getElementById('verApellidos').textContent = `${estudiante.apellidoPaterno} ${estudiante.apellidoMaterno}`;
                    document.getElementById('verFechaRegistro').textContent = estudiante.fechaNacimiento ? new Date(estudiante.fechaNacimiento).toLocaleDateString() : 'No disponible';
                    
                    // Actualizar información académica si existe
                    if (estudiante.area) {
                        document.getElementById('verArea').textContent = estudiante.area.nombre;
                    } else {
                        document.getElementById('verArea').textContent = 'No asignada';
                    }
                    
                    if (estudiante.categoria) {
                        document.getElementById('verCategoria').textContent = estudiante.categoria.nombre;
                    } else {
                        document.getElementById('verCategoria').textContent = 'No asignada';
                    }
                    
                    if (estudiante.delegacion) {
                        document.getElementById('verDelegacion').textContent = estudiante.delegacion.nombre;
                    } else {
                        document.getElementById('verDelegacion').textContent = 'No asignada';
                    }
                    
                    document.getElementById('verModalidad').textContent = estudiante.modalidad || 'No definida';

                    // Mostrar información del grupo si existe y la modalidad es duo o equipo
                    const infoGrupo = document.getElementById('infoGrupo');
                    if (estudiante.grupo && (estudiante.modalidad === 'duo' || estudiante.modalidad === 'equipo')) {
                        document.getElementById('verNombreGrupo').textContent = estudiante.grupo.nombre || 'Sin nombre';
                        document.getElementById('verCodigoGrupo').textContent = estudiante.grupo.codigo;
                        document.getElementById('verEstadoGrupo').textContent = estudiante.grupo.estado;
                        infoGrupo.style.display = 'block';
                    } else {
                        infoGrupo.style.display = 'none';
                    }

                    document.getElementById('modalVerEstudiante').style.display = 'flex';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del estudiante');
            });
        }

        function editarEstudiante(id) {
            fetch(`/estudiantes/ver/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const estudiante = data.estudiante;
                    document.getElementById('editEstudianteId').value = estudiante.id;
                    
                    // Llenar el formulario con los datos actuales
                    if (estudiante.area) {
                        document.getElementById('editArea').value = estudiante.area.id;
                    }
                    if (estudiante.categoria) {
                        document.getElementById('editCategoria').value = estudiante.categoria.id;
                    }
                    if (estudiante.modalidad) {
                        document.getElementById('editModalidad').value = estudiante.modalidad;
                    }

                    document.getElementById('modalEditarEstudiante').style.display = 'flex';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del estudiante');
            });
        }

        function cerrarModalVer() {
            document.getElementById('modalVerEstudiante').style.display = 'none';
        }

        function cerrarModalEditar() {
            document.getElementById('modalEditarEstudiante').style.display = 'none';
        }

        document.getElementById('formEditarEstudiante').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('editEstudianteId').value;
            const formData = new FormData(this);

            fetch(`/estudiantes/update/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error al actualizar el estudiante');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el estudiante');
            });
        });

        // Cerrar modales al hacer clic fuera
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
    @endpush
</x-app-layout>